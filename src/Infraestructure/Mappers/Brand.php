<?php

namespace App\Infraestructure\Mappers;

use App\Infraestructure\Interfaces\Mapper\IBrandMapper;

class Brand implements IBrandMapper
{
    /**
     * @param array<string|string> $data
     * @return \App\Domain\DTO\Brand
     */
    public function parseBrand(array $data): \App\Domain\DTO\Brand
    {
        return new \App\Domain\DTO\Brand(intval($data['id']), strval($data['name']), strval($data['url_name']));
    }
}
