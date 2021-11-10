<?php


namespace App\Domain\DAO\Adapters\Mysql;




use App\Domain\DTO\Brand;
use App\Domain\Interfaces\DTO\IBrandAdapter;
use App\Domain\Interfaces\Mapper\IBrandMapper;
use App\Infraestructure\Persistence\Mongo\MySqlAdapterBase;
use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;
use App\Infraestructure\Persistence\MySql\MySqlConnection;
use Cratia\ORM\DBAL\QueryExecute;
use Cratia\ORM\DQL\Query;
use Cratia\ORM\DQL\Table;

class BrandMysqlAdapter extends MySqlAdapterBase implements IBrandMapper, IBrandAdapter
{
    const TABLE = "brands";

    /**
     * BrandMysqlAdapter constructor.
     */
    public function __construct(MySqlConnection $connection)
    {
        parent::__construct($connection,new Table(self::TABLE));
    }

    public function fetchAll(): QueryResponse
    {
        $query = new Query($this->getTable());
        $query->setLimit(Query::NO_LIMIT);
        return $this->read($query);
    }

    public function parseBrand(array $data): Brand
    {
        return new Brand($data['id'],$data['name'],$data['url_name']);
    }


}