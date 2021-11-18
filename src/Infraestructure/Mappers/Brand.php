<?php

namespace App\Infraestructure\Mappers\Mongo;

use App\Infraestructure\Interfaces\Mapper\IBrandMapper;

class Brand implements IBrandMapper
{
    public function parseBrand(array $data): \App\Domain\DTO\Brand
    {
        return new \App\Domain\DTO\Brand($data['id'], $data['name'], $data['url_name']);
    }
}
