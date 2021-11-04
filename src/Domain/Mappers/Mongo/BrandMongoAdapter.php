<?php


namespace App\Domain\Mappers\Mongo;

use App\Domain\DTO\Brand;
use App\Domain\Error\DataNotFound;
use App\Domain\Interfaces\DTO\IBrandAdapter;
use App\Domain\Interfaces\Mapper\IBrandMapper;
use App\Infraestructure\DBAL\DAO\MongoAdapterBase;
use App\Infraestructure\Persistence\Mongo\MongoConnection;
use App\Infraestructure\Persistence\Mongo\Queryable\Query;
use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;

final class BrandMongoAdapter extends MongoAdapterBase implements IBrandAdapter, IBrandMapper
{
    const COLLECTION = "brands";

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

    public function parseBrand(array $data): Brand
    {
        return new Brand($data['id'],$data['name'],$data['url_name']);
    }


}