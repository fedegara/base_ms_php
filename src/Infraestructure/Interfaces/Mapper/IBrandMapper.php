<?php
declare(strict_types=1);

namespace App\Infraestructure\Interfaces\Mapper;

use App\Domain\DTO\Brand;

interface IBrandMapper
{
    /**
     * @param array<string|int> $data
     * @return Brand
     */
    public function parseBrand(array $data): Brand;
}
