<?php

namespace app\repositories;

interface BrandSourceInterface
{
    /**
     * @param string $brandName
     * @return array
     */
    public function fetchByBrand(string $brandName): array;
}