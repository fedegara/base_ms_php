<?php

namespace App\Infraestructure\DBAL\DAO;

use App\Infraestructure\Persistence\Mongo\MongoConnection;
use App\Infraestructure\Persistence\Mongo\Queryable\Filter;
use App\Infraestructure\Persistence\Mongo\Queryable\FilterGroup;
use App\Infraestructure\Persistence\Mongo\Queryable\GeneralAction;
use App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages\Aggregate;
use App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages\BasePipeline;
use App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages\GroupPipeline;
use App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages\MatchPipeline;
use App\Infraestructure\Persistence\Mongo\Queryable\Query;
use App\Infraestructure\Persistence\Mongo\Queryable\QueryResponse;
use Exception;

abstract class MongoAdapterBase
{
    public const TYPE_OPERATIONS = [
        'integer' => '$sum',
        'percentage' => '$avg'
    ];

    /**
     * @var string
     */
    protected $collection;

    /**
     * @var MongoConnection
     */
    protected $connection;

    /**
     * @param MongoConnection $connection
     */
    public function __construct(MongoConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return string
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     */
    public function setCollection(string $collection): void
    {
        $this->collection = $collection;
    }

    /**
     * @param Aggregate $aggregate
     * @return QueryResponse
     * @throws Exception
     */
    public function aggregate(Aggregate $aggregate): QueryResponse
    {
        $data = iterator_to_array($this->connection->aggregate($aggregate));

        $this->connection->parseResponse($data);

        return (new QueryResponse())
            ->setData($data);
    }

    /**
     * @param Query $query
     * @return QueryResponse
     * @throws Exception
     */
    protected function fetch(Query $query): QueryResponse
    {
        $cursor = $this->connection->read($query);
        $data = [];
        if (!is_null($cursor)) {
            $data = $cursor->toArray();
        }
        $this->connection->parseResponse($data);

        return (new QueryResponse())
            ->setData($data)
            ->setFound($this->connection->count($query));
    }

    /**
     * @param array<int>|null $ids
     * @param Query $query
     * @return Query
     * @throws Exception
     */
    protected function addQueryFilterGroup(?array $ids, Query $query): Query
    {
        if (isset($ids) && !empty($ids)) {
            $filterGroup = (new FilterGroup());
            $filterGroup->setMode(FilterGroup::MODE_EXCLUSIVE);
            foreach ($ids as $id) {
                $filterGroup->addFilter((new Filter("dimension.externalMediaId", Filter::EQUAL, $id)));
            }
            $query->addFilter($filterGroup);
        }

        return $query;
    }

    /**
     * @param array<string|int>|null $ids
     * @param MatchPipeline $pipeline
     * @return MatchPipeline
     * @throws Exception
     */
    protected function addAggregateFilterGroup(?array $ids = null, MatchPipeline $pipeline): MatchPipeline
    {
        if (isset($ids) && !empty($ids)) {
            $filterGroup = (new FilterGroup());
            $filterGroup->setMode(FilterGroup::MODE_EXCLUSIVE);
            foreach ($ids as $id) {
                $filterGroup->addFilter((new Filter("dimension.externalMediaId", Filter::EQUAL, $id)));
            }
            $pipeline->addFilter($filterGroup);
        }
        return $pipeline;
    }
}
