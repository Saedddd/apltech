<?php

namespace app\repositories;

use app\models\Product;
use Yii;

class DbBrandSource implements BrandSourceInterface
{
   public function fetchByBrand(string $brandName): array
{
    $products = Product::find()
        ->where('LOWER(brand_name) = LOWER(:brand)', [':brand' => $brandName])
        ->asArray()
        ->all();
    
    return $products;
}
}