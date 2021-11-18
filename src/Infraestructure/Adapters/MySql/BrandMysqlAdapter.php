<?php

namespace App\Infraestructure\Adapters\Mysql;

use App\Infraestructure\Interfaces\DTO\IBrandAdapter;
use App\Infraestructure\Persistence\Mongo\MySqlAdapterBase;
use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;
use App\Infraestructure\Persistence\MySql\MySqlConnection;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Table;

class BrandMysqlAdapter extends MySqlAdapterBase implements IBrandAdapter
{
    public const TABLE = "brands";

    /**
     * BrandMysqlAdapter constructor.
     */
    public function __construct(MySqlConnection $connection)
    {
        parent::__construct($connection, new Table(self::TABLE));
    }

    public function fetchAll(): QueryResponse
    {
        $query = new Query($this->getTable());
        $query->setLimit(Query::NO_LIMIT);
        return $this->read($query);
    }
}
