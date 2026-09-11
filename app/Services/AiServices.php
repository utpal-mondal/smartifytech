<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatConversation;

class AiServices
{
    private AiToolServices $tools;

    public function __construct(AiToolServices $tools)
    {
        $this->tools = $tools;
    }

    /**
     * Generate a customer service reply for the given user message.
     *
     * @param string $message
     * @param ChatConversation $conversation
     * @return string
     */
    public function reply(string $message, ChatConversation $conversation): string
    {
        $apiKey = config('services.openai.key');

        if (empty($apiKey)) {
            return 'The assistant is not configured right now. Please try again later.';
        }

        // If the previous turn asked for a phone number and the user provided one, save it directly
        $lastAgentMessage = $conversation->messages()->where('role', 'agent')->latest()->first()?->message ?? '';
        if (stripos($lastAgentMessage, 'phone number') !== false) {
            $phone = $this->looksLikePhone($message);
            if ($phone) {
                return $this->tools->updatePhone($conversation, $phone);
            }

            return 'Invalid phone number. Please enter a valid phone number.';
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful customer service agent for Smartify Tech, a wholesaler of consumer electronics. '
                    . 'Keep answers short and friendly. When a customer asks about product price, stock, quantity, availability, or any product-specific detail, '
                    . 'use one of the product tools: get_product_stock for a single product stock/quantity, get_product_price for a single product price, search_products to check availability, get_typeof_products for all brands, get_modelfrom_type for models in a brand, or get_stock_by_type for stock by brand. '
                    . 'When the customer asks about an order status, use the get_order_status tool. '
                    . 'When the customer asks for all product types, brands, or categories, use the get_typeof_products tool. '
                    . 'When the customer asks for all models in a brand or product type, use the get_modelfrom_type tool with the type/brand argument. '
                    . 'When the customer asks about stock or availability for all models in a brand or product type, use the get_stock_by_type tool with the type/brand argument. '
                    . 'If a product tool returns that a product is not found or not available, respond with "Invalid product name or no product available with this name." Do not ask for a phone number for product questions. '
                    . 'Only ask for a phone number when the customer has a general question not about products, brands, models, stock, availability, or orders, or when they explicitly ask to speak to a person. '
                    . 'If the customer provides a phone number, call the update_phone tool with the phone number. '
                    . 'If you asked for a phone number in the previous turn and the customer replies with a number, always call update_phone and never treat that number as a product or order ID. '
                    . 'Base your answer only on the tool result. '
                    . 'When a tool returns a numbered list, keep each item on its own line and do not combine them into one long sentence. '
                    . 'Do not use Markdown formatting such as **. '
                    . 'If a tool says a product is out of stock, tell the customer the product is not available.',
            ],
            ['role' => 'user', 'content' => $message],
        ];

        try {
            $response = $this->callOpenAi($apiKey, $messages);

            if ($response === null) {
                return 'Sorry, I could not process your request right now.';
            }

            $choice = $response['choices'][0]['message'] ?? [];

            // Handle tool calls, then ask the model again with the tool results
            if (!empty($choice['tool_calls'])) {
                $messages[] = $choice;

                foreach ($choice['tool_calls'] as $toolCall) {
                    $arguments = json_decode($toolCall['function']['arguments'] ?? '{}', true) ?: [];
                    $messages[] = [
                        'role' => 'tool',
                        'tool_call_id' => $toolCall['id'],
                        'content' => $this->runTool($toolCall['function']['name'] ?? '', $arguments, $conversation),
                    ];
                }

                $response = $this->callOpenAi($apiKey, $messages);

                if ($response === null) {
                    return 'Sorry, I could not process your request right now.';
                }

                $choice = $response['choices'][0]['message'] ?? [];
            }

            return trim($choice['content'] ?? '') ?: 'Sorry, I did not understand that. Could you rephrase?';

        } catch (\Exception $e) {
            Log::error('AI chat reply failed: ' . $e->getMessage());
            return 'Sorry, I could not process your request right now.';
        }
    }

    /**
     * Check whether a message is or contains a phone number and return the phone number.
     *
     * @param string $message
     * @return string|null
     */
    private function looksLikePhone(string $message): ?string
    {
        $message = trim($message);

        // Whole message is a phone number with optional formatting
        if (preg_match('/^[\d\s\+\-\(\)\.\/]+$/', $message)) {
            $digits = preg_replace('/\D/', '', $message);
            if (strlen($digits) >= 7) {
                return $message;
            }
        }

        // Look for a phone-like number anywhere in the message
        if (preg_match('/(?:\+\d{1,3}[\s\-]?)?\(?\d{1,5}\)?[\s\-\.]?\d{1,5}[\s\-\.]?\d{1,6}[\s\-\.]?\d{0,6}/', $message, $matches)) {
            $phone = trim($matches[0]);
            $digits = preg_replace('/\D/', '', $phone);
            if (strlen($digits) >= 7) {
                return $phone;
            }
        }

        return null;
    }

    /**
     * Send a chat completion request to OpenAI.
     *
     * @param string $apiKey
     * @param array $messages
     * @return array|null
     */
    private function callOpenAi(string $apiKey, array $messages): ?array
    {
        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => config('services.openai.model'),
                'messages' => $messages,
                'tools' => $this->toolDefinitions(),
            ]);

        if ($response->failed()) {
            Log::error('OpenAI request failed', ['body' => $response->body()]);
            return null;
        }

        return $response->json();
    }

    /**
     * Execute a tool requested by the model.
     *
     * @param string $name
     * @param array $arguments
     * @param ChatConversation $conversation
     * @return string
     */
    private function runTool(string $name, array $arguments, ChatConversation $conversation): string
    {
        return match ($name) {
            'get_product_stock' => $this->tools->getProductStock($arguments['product'] ?? ''),
            'get_product_price' => $this->tools->getProductPrice($arguments['product'] ?? ''),
            'search_products' => $this->tools->searchProducts($arguments['product_name'] ?? ''),
            'get_order_status' => $this->tools->getOrderStatus($arguments['order_number'] ?? ''),
            'get_typeof_products' => $this->tools->getTypeOfProducts(),
            'get_modelfrom_type' => $this->tools->getModelFromType($arguments['type'] ?? ''),
            'get_stock_by_type' => $this->tools->getStockByType($arguments['type'] ?? ''),
            'update_phone' => $this->tools->updatePhone($conversation, $arguments['phone'] ?? ''),
            default => 'Unknown tool.',
        };
    }

    /**
     * The tool definitions exposed to the model.
     *
     * @return array
     */
    private function toolDefinitions(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_product_stock',
                    'description' => 'Check whether a product is in stock and how many units are available.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'product' => [
                                'type' => 'string',
                                'description' => 'The product model name or product ID, for example "SM-1000".',
                            ],
                        ],
                        'required' => ['product'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_product_price',
                    'description' => 'Get the price of a product by product name or product ID.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'product' => [
                                'type' => 'string',
                                'description' => 'The product model name or product ID, for example "SM-1000".',
                            ],
                        ],
                        'required' => ['product'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_products',
                    'description' => 'Search for a product by name or model and tell the user if it is available.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'product_name' => [
                                'type' => 'string',
                                'description' => 'The product name or model the customer is asking about, for example "SM-1000".',
                            ],
                        ],
                        'required' => ['product_name'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_order_status',
                    'description' => 'Get the status of a customer order by order number or order ID.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'order_number' => [
                                'type' => 'string',
                                'description' => 'The order number or order ID, for example "O20260001".',
                            ],
                        ],
                        'required' => ['order_number'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_typeof_products',
                    'description' => 'Get the list of all product types or brands available in the store, such as iPhone, Samsung, OnePlus, etc.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_modelfrom_type',
                    'description' => 'Get all product models for a specific product type or brand.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'type' => [
                                'type' => 'string',
                                'description' => 'The product type or brand name, for example "iPhone" or "Samsung".',
                            ],
                        ],
                        'required' => ['type'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_stock_by_type',
                    'description' => 'Get the stock and availability status for all models in a specific product type or brand, including out of stock items.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'type' => [
                                'type' => 'string',
                                'description' => 'The product type or brand name, for example "iPhone" or "Samsung".',
                            ],
                        ],
                        'required' => ['type'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'update_phone',
                    'description' => 'Save the customer phone number to the conversation when the customer provides it so an executive can contact them. Call this whenever a customer provides a phone number and never confuse phone numbers with product or order IDs.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'phone' => [
                                'type' => 'string',
                                'description' => 'The customer phone number.',
                            ],
                        ],
                        'required' => ['phone'],
                    ],
                ],
            ],
        ];
    }
}