<?php

namespace App\Infraestructure\Adapters\Mongo;

use App\Domain\Error\DataNotFound;
use App\Infraestructure\Interfaces\DTO\IBrandAdapter;
use App\Infraestructure\DBAL\DAO\MongoAdapterBase;
use App\Infraestructure\Persistence\Mongo\MongoConnection;
use App\Infraestructure\Persistence\Mongo\Queryable\Query;
use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;

final class BrandMongoAdapter extends MongoAdapterBase implements IBrandAdapter
{
    public const COLLECTION = "brands";

    /**
     * @param MongoConnection $connection
     */
    public function __construct(MongoConnection $connection)
    {
        parent::__construct($connection);
    }

    public function fetchAll(): QueryResponse
    {
        $data = $this->fetch((new Query(self::COLLECTION))
            ->setLimit(Query::NO_LIMIT));

        if (empty($data->getData())) {
            throw new DataNotFound("Error in BrandMongoAdapter::fetchAll() ... Brand NOT FOUND");
        }

        return $data;
    }
}
