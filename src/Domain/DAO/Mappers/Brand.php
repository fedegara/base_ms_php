<?php


namespace App\Domain\DAO\Mappers;


use App\Domain\Interfaces\Mapper\IBrandMapper;

class Brand implements IBrandMapper
{
    public function parseBrand(array $data): \App\Domain\DTO\Brand
    {
        return new \App\Domain\DTO\Brand($data['id'], $data['name'], $data['url_name']);
    }
}