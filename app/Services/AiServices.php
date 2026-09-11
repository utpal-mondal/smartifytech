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

        $phoneNote = !empty($conversation->phone)
            ? ' The customer phone number ' . $conversation->phone . ' is already saved. Do not ask for the phone number again. '
                . 'If they want to speak to a person or ask for more details, tell them an executive will contact them shortly and ask if there is anything else they need help with.'
            : 'If the user wants to speak to a person, asks for more details, or the message does not match any of the tools above, do not call a product tool. Instead, ask for their phone number and say an executive will contact them. '
                . 'Only ask for a phone number for general questions not about products, brands, models, stock, availability, or orders, or when the user asks to speak to a person.';

        $systemMessage = [
            'role' => 'system',
            'content' => 'You are a helpful customer service agent for Smartify Tech, a wholesaler of consumer electronics. '
                . 'Keep answers short and friendly. '
                . 'For product price questions call get_product_price. '
                . 'For product stock/quantity questions call get_product_stock. '
                . 'For product availability questions call search_products. '
                . 'For listing all brands call get_typeof_products. '
                . 'For listing models of a brand call get_modelfrom_type with the brand name. '
                . 'For stock of all models in a brand call get_stock_by_type with the brand name. '
                // . 'For order status call get_order_status. '
                . 'When the user asks for a callback or provides a phone number call update_phone. '
                . 'Pass the product name or ID the user gave as the argument, for example "apple iphone 11" or "Vivo V30". '
                . 'If the user gives a short detail like "64GB" or "128GB" after you asked for it, combine it with the previous product they asked about. '
                . $phoneNote
                . ' Do not answer from your own knowledge. Base your answer only on the tool result. '
                . 'If a product tool returns that the product is not found or not available, respond with "Invalid product name or no product available with this name." '
                . 'Do not ask for a phone number for product questions. '
                . 'If you asked for a phone number in the previous turn and the customer replies with a number, always call update_phone and never treat that number as a product or order ID. '
                . 'When a tool returns a numbered list, keep each item on its own line and do not combine them into one long sentence. '
                . 'Do not use Markdown formatting such as **. '
                . 'If a tool says a product is out of stock, tell the customer the product is not available.',
        ];

        $history = $conversation->messages()
            ->oldest()
            ->get()
            ->map(fn ($m) => [
                'role' => $m->role === 'agent' ? 'assistant' : 'user',
                'content' => $m->message,
            ])
            ->values()
            ->all();

        $messages = array_merge([$systemMessage], $history);

        try {
            $response = $this->callOpenAi($apiKey, $messages);

            if ($response === null) {
                return 'Sorry, I could not process your request right now.';
            }

            $choice = $response['choices'][0]['message'] ?? [];

            // If the model returns no content and no tool call, let it generate a direct reply
            if (empty($choice['tool_calls']) && empty(trim($choice['content'] ?? ''))) {
                if (!empty($conversation->phone)) {
                    $messages[] = [
                        'role' => 'system',
                        'content' => 'No tool was applicable. The customer phone number ' . $conversation->phone . ' is already saved. Tell them an executive will contact them shortly and ask if there is anything else they need help with.',
                    ];
                } else {
                    $messages[] = [
                        'role' => 'system',
                        'content' => 'No tool was applicable. Politely ask the customer for their phone number and say an executive will contact them shortly.',
                    ];
                }

                $response = $this->callOpenAi($apiKey, $messages, 'none');

                if ($response === null) {
                    return 'Sorry, I could not process your request right now.';
                }

                $choice = $response['choices'][0]['message'] ?? [];
            }

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
    private function callOpenAi(string $apiKey, array $messages, string $toolChoice = 'auto'): ?array
    {
        $payload = [
            'model' => config('services.openai.model'),
            'messages' => $messages,
            'tools' => $this->toolDefinitions(),
        ];

        if ($toolChoice !== 'auto') {
            $payload['tool_choice'] = $toolChoice;
        }

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', $payload);

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
            // 'get_order_status' => $this->tools->getOrderStatus($arguments['order_number'] ?? ''),
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
                    'description' => 'Get the price of a specific product. Use this whenever the user asks for a price, cost, how much, or pricing. Pass the product name or ID exactly as the user wrote it.',
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
            // [
            //     'type' => 'function',
            //     'function' => [
            //         'name' => 'get_order_status',
            //         'description' => 'Get the status of a customer order by order number or order ID.',
            //         'parameters' => [
            //             'type' => 'object',
            //             'properties' => [
            //                 'order_number' => [
            //                     'type' => 'string',
            //                     'description' => 'The order number or order ID, for example "O20260001".',
            //                 ],
            //             ],
            //             'required' => ['order_number'],
            //         ],
            //     ],
            // ],
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
                    'description' => 'Save the customer phone number to the conversation. Use this ONLY when the customer provides or confirms a phone number. Do not use this when the user wants to speak to a person or asks for more details; in those cases ask for the phone number instead.',
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