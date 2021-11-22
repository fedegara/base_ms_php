<?php
declare(strict_types=1);

namespace App\Infraestructure\Interfaces\Mapper;

use App\Domain\DTO\Brand;

interface IBrandMapper
{
    /**
     * @param array<string,array<string>>|array<string> $data
     * @return Brand
     */
    public function parseBrand(array $data): Brand;
}
