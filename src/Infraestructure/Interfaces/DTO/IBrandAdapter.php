<?php

namespace App\Infraestructure\Interfaces\DTO;

use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;

interface IBrandAdapter
{
    /**
     * @return QueryResponse
     */
    public function fetchAll(): QueryResponse;
}
