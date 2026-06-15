<?php

namespace app\repositories;

use Yii;

class JsonBrandSource implements BrandSourceInterface 
{
    public function fetchByBrand(string $brandName): array
    {
        $filePath = Yii::getAlias('@app/runtime/product.json');
        
        if (!file_exists($filePath)) {
            return [];
        }

        $jsonData = file_get_contents($filePath);
        if ($jsonData === false) {
            return [];
        }

        $products = json_decode($jsonData, true);
        
        if (!is_array($products)) {
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