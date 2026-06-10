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
        $products = json_decode($jsonData, true);

        return array_filter($products, function ($product) use ($brandName) {
            return strcasecmp($product['brand_name'] ?? '', $brandName) === 0;
        });
    }
}