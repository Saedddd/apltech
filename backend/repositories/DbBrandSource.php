<?php 

namespace app\repositories;

use app\models\Product;

class DbBrandSource implements BrandSourceInterface
{
    public function fetchByBrand(string $brandName): array
    {
        return Product::find()
            ->where(['brand_name' => $brandName])
            ->asArray()
            ->all();
    }
}