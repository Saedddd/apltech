<?php

namespace app\repositories;

use app\models\Product;

class DbBrandSource implements BrandSourceInterface
{
    public function fetchByBrand(string $brandName): array
    {
         $brandName = strtolower($brandName);
         
        return Product::find()
            ->where(['like', 'brand_name', $brandName, false])
            ->asArray()
            ->all();

            
          
    }

}