<?php

namespace App\Infraestructure\Persistence\Mongo;

use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;
use App\Infraestructure\Persistence\MySql\MySqlConnection;
use Cratia\ORM\DBAL\QueryExecute;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Table;

class MySqlAdapterBase
{
    /** @var MySqlConnection */
    private $mysqlConnection;
    /** @var Table */
    private $table;

    /**
     * MySqlAdapterBase constructor.
     * @param MySqlConnection $mysqlConnection
     * @param Table $table
     */
    public function __construct(MySqlConnection $mysqlConnection, Table $table)
    {
        $this->mysqlConnection = $mysqlConnection;
        $this->table = $table;
    }

    /**
     * @return MySqlConnection
     */
    public function getMysqlConnection(): MySqlConnection
    {
        return $this->mysqlConnection;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }



    protected function read(Query $query): QueryResponse
    {
        $result =  (new QueryExecute($this->mysqlConnection))->executeQuery($query);
        return (new QueryResponse())->setData($result->getRows())->setFound($result->getCount());
    }
}
