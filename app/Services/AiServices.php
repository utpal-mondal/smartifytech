<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
     * @return string
     */
    public function reply(string $message): string
    {
        $apiKey = config('services.openai.key');

        if (empty($apiKey)) {
            return 'The assistant is not configured right now. Please try again later.';
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful customer service agent for Smartify Tech, a wholesaler of consumer electronics. '
                    . 'Keep answers short and friendly. When a customer asks about stock, quantity, or whether a product is in stock, '
                    . 'use the get_product_stock tool. When the customer asks whether a product is available or wants to search for a product, '
                    . 'use the search_products tool. When the customer asks about an order status, use the get_order_status tool. '
                    . 'When the customer asks for all product types, brands, or categories, use the get_typeof_products tool. '
                    . 'When the customer asks for all models in a brand or product type, use the get_modelfrom_type tool with the type/brand argument. '
                    . 'When the customer asks about stock or availability for all models in a brand or product type, '
                    . 'use the get_stock_by_type tool with the type/brand argument. '
                    . 'Base your answer only on the tool result. '
                    . 'When a tool returns a numbered list, keep each item on its own line and do not combine them into one long sentence. '
                    . 'Do not use Markdown formatting such as **. '
                    . 'If the tool says the product is out of stock or not available, tell the customer the product is not available.'
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
                        'content' => $this->runTool($toolCall['function']['name'] ?? '', $arguments),
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
     * @return string
     */
    private function runTool(string $name, array $arguments): string
    {
        return match ($name) {
            'get_product_stock' => $this->tools->getProductStock($arguments['product'] ?? ''),
            'search_products' => $this->tools->searchProducts($arguments['product_name'] ?? ''),
            'get_order_status' => $this->tools->getOrderStatus($arguments['order_number'] ?? ''),
            'get_typeof_products' => $this->tools->getTypeOfProducts(),
            'get_modelfrom_type' => $this->tools->getModelFromType($arguments['type'] ?? ''),
            'get_stock_by_type' => $this->tools->getStockByType($arguments['type'] ?? ''),
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
        ];
    }
}