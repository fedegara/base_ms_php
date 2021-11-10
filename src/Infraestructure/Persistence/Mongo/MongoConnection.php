<?php


namespace App\Infraestructure\Persistence\Mongo;


use App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages\Aggregate;
use App\Infraestructure\Persistence\Mongo\Queryable\Query;
use Exception;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use MongoDB\Model\DatabaseInfoIterator;
use MongoDB\UpdateResult;
use Traversable;

class MongoConnection
{
    //this must be an array because we could have a connection to read and another to write
    private static $_instance = [];

    /** @var Client */
    private $client;
    /** @var Collection */
    private $collection;

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

    public static function parseResponse(&$response)
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


    public function upsert(array $new_value, string $collection): UpdateResult
    {
        $this->changeCollection($collection);
        $filter = [
            'instance' => INST_NAME,
            'internalMediaId' => $new_value['internalMediaId']
        ];
        try {
            return $this->collection->updateOne(
                $filter,
                [
                    '$set' => $new_value,
                    '$currentDate' => ['lastModified' => true],
                ], ['upsert' => true]
            );
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change collection
     *
     * @param string $collection
     */
    private function changeCollection(string $collection): void
    {
        $this->collection = $this->client->selectCollection($this->database, $collection);
    }

    public function read(Query $mongoQueryable)
    {
        try {
            $this->changeCollection($mongoQueryable->getCollection());
            return $this->collection->find($mongoQueryable->getFiltersParsed(), $mongoQueryable->getOptions());
        } catch (Exception $exception) {
            die(dump($exception));//ignore-dump
        }
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
     * @return array
     * @throws Exception
     */
    public function distinct(string $fieldName, Query $mongoQueryable): array
    {
        $this->changeCollection($mongoQueryable->getCollection());
        return $this->collection->distinct($fieldName, $mongoQueryable->getFiltersParsed(), $mongoQueryable->getOptions());
    }

    /**
     * @param Query|Aggregate $queryAggregate
     * @return Traversable
     */
    public function aggregate($queryAggregate): Traversable
    {
        try {
            $this->changeCollection($queryAggregate->getCollection());
            return $this->collection->aggregate($queryAggregate->getPipelineParsed());
        } catch (Exception $exception) {
            die(dump($exception));//ignore-dump
        }
    }

    /**
     * @return DatabaseInfoIterator
     */
    public function listCollections(): DatabaseInfoIterator
    {
        return $this->client->listDatabases();

    }
}