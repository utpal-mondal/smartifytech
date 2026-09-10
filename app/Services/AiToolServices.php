<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\ChatConversation;

class AiToolServices
{
    /**
     * Get the stock message for a product by ID or name/model.
     *
     * @param string $query Product ID or name/model
     * @return string
     */
    public function getProductStock($query): string
    {
        $query = trim($query);

        if (empty($query)) {
            return 'Please provide a product name or ID.';
        }

        $product = $this->findProduct($query);

        return $product ? $this->formatStock($product) : 'product out of stock';
    }

    /**
     * Search for a product and tell the user if it is available or not.
     *
     * @param string $productName
     * @return string
     */
    public function searchProducts($productName): string
    {
        $productName = trim($productName);

        if (empty($productName)) {
            return 'Please provide a product name or ID.';
        }

        $product = $this->findProduct($productName);

        if (!$product) {
            return 'Product not found or not available.';
        }

        $quantity = (int) $product->quantity;

        if ($quantity <= 0) {
            return 'Product ' . $product->model . ' is not available.';
        }

        return 'Product ' . $product->model . ' is available, quantity: ' . $quantity;
    }

    /**
     * Get the status of an order by order number or ID.
     *
     * @param string $query
     * @return string
     */
    public function getOrderStatus($query): string
    {
        $query = trim($query);

        if (empty($query)) {
            return 'Please provide an order number or order ID.';
        }

        $order = $this->findOrder($query);

        if (!$order) {
            return 'Order not found.';
        }

        return 'Order ' . $order->order_number . ' status: ' . $order->status;
    }

    /**
     * Get all product types (brands) available in the store.
     *
     * @return string
     */
    public function getTypeOfProducts(): string
    {
        $types = Product::distinct()->orderBy('type')->pluck('type')->filter()->all();

        if (empty($types)) {
            return 'No product types or brands available.';
        }

        $lines = [];
        foreach ($types as $index => $type) {
            $lines[] = ($index + 1) . '. ' . ucfirst($type);
        }

        return "Available smartphone brands:\n" . implode("\n", $lines);
    }

    /**
     * Get all product models for a given product type or brand.
     *
     * @param string $type
     * @return string
     */
    public function getModelFromType($type): string
    {
        $type = trim($type);

        if (empty($type)) {
            return 'Please provide a brand or product type.';
        }

        $models = Product::where('type', 'like', '%' . strtolower($type) . '%')
            ->orderBy('model')
            ->pluck('model')
            ->unique()
            ->filter()
            ->all();

        if (empty($models)) {
            return 'No models found for that brand or type.';
        }

        $lines = [];
        foreach ($models as $index => $model) {
            $lines[] = ($index + 1) . '. ' . $model;
        }

        return 'Models for ' . ucfirst($type) . ":\n" . implode("\n", $lines);
    }

    /**
     * Get stock and availability for all models in a product type or brand.
     *
     * @param string $type
     * @return string
     */
    public function getStockByType($type): string
    {
        $type = trim($type);

        if (empty($type)) {
            return 'Please provide a brand or product type.';
        }

        $products = Product::where('type', 'like', '%' . strtolower($type) . '%')
            ->orderBy('model')
            ->get();

        if ($products->isEmpty()) {
            return 'No products found for that brand or type.';
        }

        $lines = [];
        foreach ($products as $index => $product) {
            $quantity = (int) $product->quantity;

            if ($quantity > 0) {
                $lines[] = ($index + 1) . '. ' . $product->model . ' - in stock, quantity: ' . $quantity . ' - available';
            } else {
                $lines[] = ($index + 1) . '. ' . $product->model . ' - out of stock - not available';
            }
        }

        return 'Stock for ' . ucfirst($type) . ":\n" . implode("\n", $lines);
    }

    /**
     * Save the customer's phone number to the conversation.
     *
     * @param ChatConversation $conversation
     * @param string $phone
     * @return string
     */
    public function updatePhone(ChatConversation $conversation, $phone): string
    {
        $phone = trim($phone);

        if (empty($phone)) {
            return 'Please provide a valid phone number.';
        }

        $conversation->update(['phone' => $phone]);

        return 'Thank you. Our executive will contact you shortly. Is there anything else you need help with?';
    }

    /**
     * Find a product by numeric ID, exact model, or model contained in the query.
     *
     * @param string $query
     * @return Product|null
     */
    private function findProduct(string $query): ?Product
    {
        if (is_numeric($query)) {
            $product = Product::find($query);
            if ($product) {
                return $product;
            }
        }

        $product = Product::where('model', $query)->first();
        if ($product) {
            return $product;
        }

        $products = Product::all();
        foreach ($products as $product) {
            if (!empty($product->model) && stripos($query, $product->model) !== false) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Find an order by numeric ID, exact order number, or order number contained in the query.
     *
     * @param string $query
     * @return Order|null
     */
    private function findOrder(string $query): ?Order
    {
        if (is_numeric($query)) {
            $order = Order::find($query);
            if ($order) {
                return $order;
            }
        }

        $order = Order::where('order_number', $query)->first();
        if ($order) {
            return $order;
        }

        $orders = Order::all();
        foreach ($orders as $order) {
            if (!empty($order->order_number) && stripos($order->order_number, $query) !== false) {
                return $order;
            }
        }

        return null;
    }

    /**
     * Format the stock response for a product.
     *
     * @param Product $product
     * @return string
     */
    private function formatStock(Product $product): string
    {
        $quantity = (int) $product->quantity;

        if ($quantity <= 0) {
            return 'product out of stock';
        }

        return 'Product ' . $product->model . ' is in stock, quantity: ' . $quantity;
    }
}