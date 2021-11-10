<?php


namespace App\Domain\DAO\Adapters\Mysql;


use App\Domain\DAO\Mapper\Mysql\BrandMysqlMapper;
use App\Domain\Interfaces\DTO\IBrandAdapter;
use App\Infraestructure\Persistence\Mongo\MySqlAdapterBase;
use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;
use App\Infraestructure\Persistence\MySql\MySqlConnection;
use Cratia\ORM\DBAL\QueryExecute;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Table;

class BrandMysqlAdapter extends MySqlAdapterBase implements IBrandAdapter
{
    const TABLE = "brand";
    private $mapper;

    /**
     * BrandMysqlAdapter constructor.
     */
    public function __construct(MySqlConnection $connection)
    {
        $this->mapper = new BrandMysqlMapper();
        parent::__construct($connection,new Table(self::TABLE));
    }

    public function fetchAll(): QueryResponse
    {
        $query = new Query($this->getTable());
        $query->setLimit(Query::NO_LIMIT);
        return $this->read($query);
    }

    /**
     * @return BrandMysqlMapper
     */
    public function getMapper(): BrandMysqlMapper
    {
        return $this->mapper;
    }


}