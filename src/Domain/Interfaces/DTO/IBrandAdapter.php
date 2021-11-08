<?php


namespace App\Domain\Interfaces\DTO;


use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;

interface IBrandAdapter
{
    public function fetchAll();
}