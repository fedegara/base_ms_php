<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable;

use Exception;

final class Query
{
    const NO_LIMIT = -1;

    /** @var string */
    private $collection;

    /** @var Filter[] */
    private $filters;

    /** @var Order */
    private $order;

    /** @var int */
    private $limit;

    /** @var int */
    private $offset;

    private $projection;

    /**
     * Query constructor.
     * @param string|null $collection
     */
    public function __construct(?string $collection = null)
    {
        $this->collection = $collection;
        $this->filters = [];
    }


    /**
     * @return string
     */
    public function getCollection(): ?string
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     * @return Query
     */
    public function setCollection(string $collection): Query
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Query
     */
    public function setLimit(int $limit): Query
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return Query
     */
    public function setOffset(int $offset): Query
    {
        $this->offset = $offset;
        return $this;
    }

    public function setOrderBy(Order $orderBy): Query
    {
        $this->order = $orderBy;
        return $this;
    }

    /**
     * @param Filter|FilterGroup $filter
     * @return Query
     */
    public function addFilter($filter): Query
    {
        if (is_null($this->filters)) {
            $this->filters = [];
        }
        $this->filters[] = $filter;
        return $this;
    }

    public function addProjection($key): Query
    {
        $this->projection[$key] = 1;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getProjection(): ?array
    {
        return $this->projection;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getFiltersParsed(): array
    {
        $filters = $this->getFilters();
        $filtersResponse = [];
        array_walk($filters, function ($filter) use (&$filtersResponse) {
            $filtersResponse = array_merge($filtersResponse, $filter->getFilter());
        });
        return $filtersResponse;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOptions(): array
    {
        $options = [];
        if ($this->limit != Query::NO_LIMIT) {
            if (!is_null($this->limit)) {
                $options['limit'] = $this->limit;
            }
            else {
                $options['limit'] = 20;
            }
        }
        if (!is_null($this->offset)) {
            $options['skip'] = $this->offset;
        }
        if (!is_null($this->projection)) {
            $options['projection'] = $this->projection;
        }
        if (!empty($this->order)) {
            $options['sort'] = $this->order->getOrderBy();
        }
        return $options;
    }

    /**
     * @return null
     */
    public function getOType()
    {
        return $this->oType;
    }

    /**
     * @param null $oType
     * @return Query
     */
    public function setOType($oType)
    {
        $this->oType = $oType;
        return $this;
    }

    /**
     * @return null
     */
    public function getOId()
    {
        return $this->oId;
    }

    /**
     * @param null $oId
     * @return Query
     */
    public function setOId($oId)
    {
        $this->oId = $oId;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearOrderBy(): self
    {
        $this->order = [];
        return $this;
    }

}