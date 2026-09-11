<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\ChatConversation;
use App\Models\Brand;
use App\Models\ChatbotProduct;

class AiToolServices
{
    /**
     * Get the stock message for a product by ID or name from the chatbot product catalog.
     *
     * @param string $query Product ID or name
     * @return string
     */
    public function getProductStock($query): string
    {
        $query = trim($query);

        if (empty($query)) {
            return 'Please provide a product name.';
        }

        $product = $this->findChatbotProduct($query);

        return $product ? $this->formatChatbotProductStock($product) : 'Invalid product name or no product available with this name.';
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
            return 'Please provide a product name.';
        }

        $product = $this->findChatbotProduct($productName);

        if (!$product) {
            return 'Invalid product name or no product available with this name.';
        }

        $stock = (int) $product->product_stock;

        if ($stock <= 0) {
            return 'Product ' . $product->product_name . ' is not available.';
        }

        return 'Product ' . $product->product_name . ' is available, quantity: ' . $stock;
    }

    /**
     * Get the price of a product by ID or name from the chatbot product catalog.
     *
     * @param string $query Product ID or name
     * @return string
     */
    public function getProductPrice($query): string
    {
        $query = trim($query);

        if (empty($query)) {
            return 'Please provide a product name.';
        }

        $product = ChatbotProduct::whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($query) . '%'])           
            ->first();

        if (!$product) {
            return 'Invalid product name or no product available with this name.';
        }

        return $this->formatChatbotProductPrice($product);
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
        $types = Brand::orderBy('name')->pluck('name')->filter()->all();

        if (empty($types)) {
            return 'No product types or brands available.';
        }

        $lines = [];
        foreach ($types as $index => $type) {
            $lines[] = ($index + 1) . '. ' . ucwords(strtolower($type));
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

        $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($type)])->first();

        if (!$brand) {
            return 'brand Not Found.';
        }

        $models = ChatbotProduct::where('brand_id', $brand->id)            
            ->orderBy('product_name')
            ->pluck('product_name')
            ->unique()
            ->filter()
            ->all();

        if (empty($models)) {
            return 'No smartphone models found for ' . ucwords(strtolower($brand->name)) . '.';
        }

        $lines = [];
        foreach ($models as $index => $model) {
            $lines[] = ($index + 1) . '. ' . $model;
        }

        return 'Models for ' . ucwords(strtolower($brand->name)) . ":\n" . implode("\n", $lines);
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
     * Save the customer phone number to the conversation.
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

        $product = Product::whereRaw('LOWER(model) = ?', [strtolower($query)])->first();
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
     * Find a chatbot product by numeric ID, exact product name, or product name contained in the query.
     *
     * @param string $query
     * @return ChatbotProduct|null
     */
    private function findChatbotProduct(string $query): ?ChatbotProduct
    {
        if (is_numeric($query)) {
            $product = ChatbotProduct::find($query);
            if ($product) {
                return $product;
            }
        }

        $product = ChatbotProduct::whereRaw('LOWER(product_name) = ?', [strtolower($query)])->first();
        if ($product) {
            return $product;
        }

        $products = ChatbotProduct::all();
        foreach ($products as $product) {
            if (!empty($product->product_name) && stripos($query, $product->product_name) !== false) {
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
     * Format the price response for a chatbot product.
     *
     * @param ChatbotProduct $product
     * @return string
     */
    private function formatChatbotProductPrice(ChatbotProduct $product): string
    {
        $price = (float) $product->product_price;

        return 'Product ' . $product->product_name . ' price is ' . number_format($price, 2);
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

    /**
     * Format the stock response for a chatbot product.
     *
     * @param ChatbotProduct $product
     * @return string
     */
    private function formatChatbotProductStock(ChatbotProduct $product): string
    {
        $stock = (int) $product->product_stock;

        if ($stock <= 0) {
            return 'product out of stock';
        }

        return 'Product ' . $product->product_name . ' is in stock, quantity: ' . $stock;
    }
}