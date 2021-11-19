<?php

namespace App\Infraestructure\Interfaces\Mapper;

use App\Domain\DTO\Brand;

interface IBrandMapper
{
    /**
     * @param array $data
     * @return Brand
     */
    public function parseBrand(array $data): Brand;
}