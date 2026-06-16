<?php

namespace app\repositories;

use Yii;

class JsonBrandSource implements BrandSourceInterface 
{
    public function fetchByBrand(string $brandName): array
    {
        
        $filePath = '/var/www/html/runtime/product.json';
        
        
        if (!file_exists($filePath)) {
            $possiblePaths = [
                Yii::getAlias('@app/runtime/product.json'),
                Yii::getAlias('@app/data/product.json'),
                '/var/www/html/data/product.json',
                __DIR__ . '/../runtime/product.json',
                __DIR__ . '/../data/product.json',
                '/opt/render/project/src/backend/runtime/product.json',
                '/opt/render/project/src/backend/data/product.json',
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $filePath = $path;
                    break;
                }
            }
        }
        
        if (!file_exists($filePath)) {
            Yii::warning("JSON file not found in any path", 'api');
            return [];
        }

        $jsonData = file_get_contents($filePath);
        if ($jsonData === false) {
            Yii::warning("Failed to read JSON file: {$filePath}", 'api');
            return [];
        }

        $products = json_decode($jsonData, true);
        
        if (!is_array($products)) {
            Yii::warning("Invalid JSON format in: {$filePath}", 'api');
            return [];
        }

        $filtered = array_filter($products, function ($product) use ($brandName) {
            $productBrand = $product['brand_name'] ?? '';
            return strcasecmp($productBrand, $brandName) === 0;
        });
        
        foreach ($filtered as &$product) {
            $product['source'] = 'json';
        }

        return array_values($filtered);
    }
}