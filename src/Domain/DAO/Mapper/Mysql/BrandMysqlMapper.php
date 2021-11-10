<?php

namespace App\Domain\DAO\Mapper\Mysql;

use App\Domain\DTO\Brand;
use App\Domain\Interfaces\Mapper\IBrandMapper;

class BrandMysqlMapper implements IBrandMapper
{

    public function parseBrand(array $data): Brand
    {
        return new Brand($data['id'],$data['name'],$data['url_name']??'');
    }
}