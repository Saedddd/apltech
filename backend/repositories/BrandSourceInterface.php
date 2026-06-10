<?php

namespace app\repositories;

interface BrandSourceInterface
{
    @return array
     
    public function fetchByBrand(@string $brand): array;
}