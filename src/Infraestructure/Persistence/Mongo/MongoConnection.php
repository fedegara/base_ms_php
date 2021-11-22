<?php

namespace App\Infraestructure\Persistence\Mongo;

use App\Context\ScopeContext;
use App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages\Aggregate;
use App\Infraestructure\Persistence\Mongo\Queryable\Query;
use Exception;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Cursor;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\UpdateResult;
use Traversable;

class MongoConnection
{
    //this must be an array because we could have a connection to read and another to write
    /** @var array<MongoConnection> */
    private static $_instance = [];

    /** @var Client */
    private $client;
    /** @var Collection */
    private $collection;
    /** @var string */
    private $database;

    /**
     * MongoConnection constructor.
     * @param string $dsn
     * @param string $db_name
     * @param bool $use_amazon
     */
    private function __construct(string $dsn, string $db_name, bool $use_amazon)
    {
        $uriOptions = [];
        if ($use_amazon) {
            $uriOptions = [
                'tls' => true,
                'tlsAllowInvalidHostnames' => true,
                'tlsCAFile' => ROOT_PATH . 'src/Infraestructure/Persistence/Mongo/certs/rds-combined-ca-bundle.pem',
                'retryWrites' => false
            ];
        }
        $this->client = new Client($dsn, $uriOptions);
        $this->database = $db_name;
    }

    /**
     * @param string $dsn
     * @param string $db_name
     * @param bool $use_amazon
     * @return mixed
     */
    public static function getInstance(string $dsn, string $db_name, bool $use_amazon)
    {
        if (!isset(self::$_instance[$dsn]) || !(self::$_instance[$dsn] instanceof self)) {
            self::$_instance[$dsn] = new self($dsn, $db_name, $use_amazon);
        }
        return self::$_instance[$dsn];
    }

    /**
     * @param array<BSONDocument|BSONArray|\ArrayObject> $response
     */
    public static function parseResponse(array &$response): void
    {
        array_walk($response, function (&$value) {
            if ($value instanceof BSONDocument || $value instanceof BSONArray) {
                $value = $value->getArrayCopy();
                self::parseResponse($value);
            }
        });
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getCollectionName(): string
    {
        return $this->collection->getCollectionName();
    }


    /**
     * Change collection
     *
     * @param ?string $collection
     */
    private function changeCollection(string $collection = null): void
    {
        if(!is_null($collection)){
            $this->collection = $this->client->selectCollection($this->database, $collection);
        }
    }

    /**
     * @param Query $mongoQueryable
     * @return Cursor|null
     * @throws Exception
     */
    public function read(Query $mongoQueryable)
    {
        $this->changeCollection($mongoQueryable->getCollection());
        return $this->collection->find($mongoQueryable->getFiltersParsed(), $mongoQueryable->getOptions());
    }

    /**
     * Returns the count of document founded by the filters in the Query
     *
     * @param Query $mongoQueryable
     * @return int
     * @throws Exception
     */
    public function count(Query $mongoQueryable): int
    {
        $this->changeCollection($mongoQueryable->getCollection());
        return $this->collection->countDocuments($mongoQueryable->getFiltersParsed());
    }

    /**
     * Return all the distinct values for filter and options setted in Query
     *
     * @param string $fieldName
     * @param Query $mongoQueryable
     * @return mixed[]
     * @throws Exception
     */
    public function distinct(string $fieldName, Query $mongoQueryable): array
    {
        $this->changeCollection($mongoQueryable->getCollection());
        return $this->collection->distinct($fieldName, $mongoQueryable->getFiltersParsed(), $mongoQueryable->getOptions());
    }

    /**
     * @param Aggregate $queryAggregate
     * @return Traversable<mixed>
     */
    public function aggregate($queryAggregate): Traversable
    {
        $this->changeCollection($queryAggregate->getCollection());
        return $this->collection->aggregate($queryAggregate->getPipelineParsed());
    }

    /**
     * @return DatabaseInfoIterator
     */
    public function listCollections(): DatabaseInfoIterator
    {
        return $this->client->listDatabases();
    }
}
