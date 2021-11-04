<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

use App\Domain\Interfaces\IQueryable;
use DateTime;
use Exception;

final class Queryable implements IQueryable
{
    const DEFAULT_LIMIT = 10;
    const DEFAULT_ORDER = ["field" => "-interactions", "edge" => "metrics"];
    const DEFAULT_OFFSET = 0;

    /**
     * @var string
     */
    private $instance;
    /**
     * @var int
     */
    private $organizationId;
    /**
     * @var string
     */
    private $periodId;
    /**
     * @var string
     */
    private $periodStart;
    /**
     * @var string
     */
    private $periodEnd;
    /**
     * @var int
     */
    private $limit;
    /**
     * @var Order
     */
    private $order;

    /**
     * @var int
     */
    private $offset;

    /** @var Filter[] */
    private $filters;

    /**
     * @param string $instance
     * @param int $organizationId
     * @param string $periodId
     * @param string $periodStart
     * @param string $periodEnd
     * @param int|null $limit
     * @param array|null $order
     * @param int|null $offset
     * @throws Exception
     */
    public function __construct(string $instance, int $organizationId, string $periodId, string $periodStart, string $periodEnd, ?int $limit = null, ?array $order = null, ?int $offset = null)
    {
        $this->instance = $instance;
        $this->organizationId = $organizationId;
        $this->periodId = $periodId;
        $this->periodStart = new DateTime($periodStart . " 00:00:00.000Z");
        $this->periodEnd = new DateTime($periodEnd . " 23:59:59.000Z");
        $this->limit = ($limit ?: self::DEFAULT_LIMIT);
        $this->order = (isset($order["field"]) ? $this->setOrder($order) : $this->setOrder(self::DEFAULT_ORDER));
        $this->offset = ($offset ?: self::DEFAULT_OFFSET);
    }

    /**
     * @param array $order
     * @return Order
     * @throws Exception
     */
    private function setOrder(array $order): Order
    {
        $direction_param = substr($order["field"], 0, 1);
        $field = substr($order["field"], 1, strlen($order["field"]));
        if ($direction_param == '-') {
            $direction = Order::ORDER_DESC;
        }
        else {
            $direction = Order::ORDER_ASC;
        }

        $edge = (!empty($order["edge"])) ? $order["edge"] . "." : "";

        return new Order("{$edge}{$field}", $direction);
    }

    /**
     * @return string
     */
    public function getInstance(): string
    {
        return $this->instance;
    }

    /**
     * @return int
     */
    public function getOrganizationId(): int
    {
        return $this->organizationId;
    }

    /**
     * @return string
     */
    public function getPeriodId(): string
    {
        return $this->periodId;
    }

    /**
     * @return DateTime
     */
    public function getPeriodStart(): DateTime
    {
        return $this->periodStart;
    }

    /**
     * @return DateTime
     */
    public function getPeriodEnd(): DateTime
    {
        return $this->periodEnd;
    }

    /**
     * @return int
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return int
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): ?array
    {
        return $this->filters;
    }

    /**
     * @param Filter[] $filters
     * @return Queryable
     */
    public function addFilters(Filter $filter): IQueryable
    {
        $this->filters[] = $filter;
        return $this;
    }


}