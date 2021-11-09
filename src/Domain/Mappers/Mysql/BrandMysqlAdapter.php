<?php


namespace App\Domain\Mappers\Mysql;

use App\Domain\Error\DataNotFound;
use App\Domain\Interfaces\DTO\IBrandAdapter;
use App\Infraestructure\Persistence\Mysql\MysqlDB;

final class BrandMysqlAdapter extends MysqlDB implements IBrandAdapter
{
    const COLLECTION = "brand";

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function fetchAll()
    {
        return $this->getRows('SELECT * FROM '.self::COLLECTION);
    }

}