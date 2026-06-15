<?php

namespace app\services;

use yii\web\NotFoundHttpException;
use app\repositories\DbBrandSource;
use app\repositories\JsonBrandSource;

class ProductService
{
    public function getBrandMinMaxPrice(string $brandName): array
    {
        $sources = [
            new DbBrandSource(),
            new JsonBrandSource(),
        ];

        $mergedProducts = [];
        foreach ($sources as $source) {
            $products = $source->fetchByBrand($brandName);
            $mergedProducts = array_merge($mergedProducts, $products);
        }

        if (empty($mergedProducts)) {
            throw new NotFoundHttpException("Товары для бренда '$brandName' не найдены");
        }

        $minProduct = null;
        $maxProduct = null;

        foreach ($mergedProducts as $product) {
            $actualPrice = $this->calculateActualPrice($product);
            $numericPrice = $this->getNumericPriceForComparison($product);

            $product['_calculated_price'] = $actualPrice;
            $product['_numeric_price'] = $numericPrice;

            if ($minProduct === null || $numericPrice < $minProduct['_numeric_price']) {
                $minProduct = $product;
            }

            if ($maxProduct === null || $numericPrice > $maxProduct['_numeric_price']) {
                $maxProduct = $product;
            }
        }

        // источникт
        return [
            [
                "min" => [
                    "id" => (int)$minProduct['id'],
                    "price" => $minProduct['_calculated_price'],
                    "source" => $this->getSource($minProduct)  // добавлено
                ]
            ],
            [
                "max" => [
                    "id" => (int)$maxProduct['id'],
                    "price" => $maxProduct['_calculated_price'],
                    "source" => $this->getSource($maxProduct)  // добавлено
                ]
            ]
        ];
    }

    private function calculateActualPrice(array $product)
    {
        if ((int)$product['status'] === 2) {
            return "цена по запросу";
        }

        $brand = strtolower($product['brand_name'] ?? '');
        if ($brand === 'dell' || $brand === 'lenovo') {
            return (int)$product['rrp_price'];
        }

        return (int)$product['price'];
    }

    private function getSource(array $product): string
    {
        return $product['source'] ?? 'database';
    }

    private function getNumericPriceForComparison(array $product): int
    {
        $brand = strtolower($product['brand_name'] ?? '');
        if ($brand === 'dell' || $brand === 'lenovo') {
            return (int)$product['rrp_price'];
        }
        return (int)$product['price'];
    }
}