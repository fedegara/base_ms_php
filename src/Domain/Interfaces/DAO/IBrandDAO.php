<?php


namespace App\Domain\Interfaces\DAO;


use App\Domain\DTO\Brand;

interface IBrandDAO
{

    /**
     * @return Brand[]
     */
    public function fetchAll(): array;
}