<?php

namespace app\repositories;

use app\models\Product;

class DbBrandSource implements BrandSourceInterface
{
    public function fetchByBrand(string $brandName): array
    {
        
        return Product::find()
            ->where(['like', 'brand_name', $brandName, false])
            ->asArray()
            ->all();

            
          
    }

}