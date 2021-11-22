<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

use Exception;

final class Query
{
    public const NO_LIMIT = -1;

    /** @var ?string */
    private $collection;

    /** @var array<Filter|FilterGroup> */
    private $filters;

    /** @var ?Order */
    private $order = null;

    /** @var ?int */
    private $limit = null;

    /** @var ?int */
    private $offset = null;

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
     * @return ?int
     */
    public function getLimit(): ?int
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
     * @return ?int
     */
    public function getOffset(): ?int
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
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return Filter[]
     * @throws Exception
     */
    public function getFiltersParsed(): array
    {
        $filters = $this->getFilters();
        $filtersResponse = [];
        array_walk($filters, function ($filter) use (&$filtersResponse) {
            $filter = $filter->getFilter();
            if(!is_null($filter)){
                $filtersResponse = array_merge($filtersResponse, $filter);
            }
        });
        return $filtersResponse;
    }

    /**
     * @return array<Filter|FilterGroup>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array<string, array<int>|int>
     */
    public function getOptions(): array
    {
        $options = [];
        if ($this->limit != Query::NO_LIMIT) {
            if (!is_null($this->limit)) {
                $options['limit'] = $this->limit;
            } else {
                $options['limit'] = 20;
            }
        }
        if (!is_null($this->offset)) {
            $options['skip'] = $this->offset;
        }
        if (!empty($this->order)) {
            $options['sort'] = $this->order->getOrderBy();
        }
        return $options;
    }
}
