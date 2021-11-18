<?php


namespace App\Domain\Services\Brand;


use App\Domain\DAO\BrandDAO;
use App\Domain\DTO\Brand;
use App\Domain\Interfaces\DTO\IBrandAdapter;

final class ServiceFetchBrands
{

    /**
     * @var BrandDAO
     */
    private $brandDao;


    public function __construct(IBrandAdapter $adapterBrand)
    {
        $this->brandDao = new BrandDAO($adapterBrand, new \App\Domain\DAO\Mappers\Brand());
    }

    /**
     * @return Brand[]
     */
    public function execute(): array
    {
        return $this->brandDao->fetchAll();
    }


}