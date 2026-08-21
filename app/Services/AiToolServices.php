<?php

namespace App\Services;

use App\Models\Product;

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