<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

use App\Infraestructure\Persistence\Mongo\Queryable\Order;

abstract class BaseAggregate
{
    public const NO_LIMIT = -1;

    /** @var ?Order */
    private $order = null;

    /** @var ?int */
    private $limit = null;

    /** @var ?int */
    private $offset = null;


    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return BaseAggregate
     */
    public function setLimit(int $limit): BaseAggregate
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
     * @return BaseAggregate
     */
    public function setOffset(int $offset): BaseAggregate
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $orderBy
     * @return $this
     */
    public function setOrder(Order $orderBy): BaseAggregate
    {
        $this->order = $orderBy;
        return $this;
    }

    //TODO: Check if it's necessary

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $options = [];
        if (!is_null($this->limit)) {
            $options['$limit'] = $this->limit;
        } else {
            $options['$limit'] = 20;
        }

        if (!is_null($this->offset)) {
            $options['$skip'] = $this->offset;
        }

        if (!empty($this->order)) {
            $options['$sort'] = $this->order->getOrderBy();
        }
        return $options;
    }
}
