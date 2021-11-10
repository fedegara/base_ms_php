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
     * BrandDAO constructor.
     * @param IBrandAdapter $adapter
     */
    public function __construct(IBrandAdapter $adapter)
    {
        $this->adapter = $adapter;
    }


    /** @inheritDoc */
    public function fetchAll(): array
    {
        return array_map(function($row){
            return $this->adapter->getMapper()->parseBrand($row);
        },$this->adapter->fetchAll()->getData());
    }


}