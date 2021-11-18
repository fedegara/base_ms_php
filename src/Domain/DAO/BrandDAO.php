<?php

namespace App\Domain\DAO;

use App\Domain\DTO\Brand;
use App\Domain\Interfaces\DAO\IBrandDAO;
use App\Domain\Interfaces\DTO\IBrandAdapter;
use App\Domain\Interfaces\Mapper\IBrandMapper;

class BrandDAO implements IBrandDAO
{
    /**
     * @var IBrandAdapter
     */
    private $adapter;

    /**
     * @var IBrandMapper
     */
    private $mapper;


    /**
     * BrandDAO constructor.
     * @param IBrandAdapter $adapter
     * @param IBrandMapper $mapper
     */
    public function __construct(IBrandAdapter $adapter, IBrandMapper $mapper)
    {
        $this->adapter = $adapter;
        $this->mapper = $mapper;
    }


    /** @inheritDoc */
    public function fetchAll(): array
    {
        return array_map(function ($row) {
            return $this->mapper->parseBrand($row);
        }, $this->adapter->fetchAll()->getData());
    }
}
