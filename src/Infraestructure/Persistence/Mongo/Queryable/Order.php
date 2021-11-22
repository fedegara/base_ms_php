<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

use Exception;

class Order
{
    public const ORDER_ASC = 1;
    public const ORDER_DESC = -1;

    /** @var string */
    private $field;

    /** @var int */
    private $direction;

    /**
     * Order constructor.
     * @param string $field
     * @param int $direction
     * @throws Exception
     */
    public function __construct(string $field, int $direction = self::ORDER_ASC)
    {
        $this->field = $field;
        if (!in_array($direction, [self::ORDER_ASC, self::ORDER_DESC])) {
            throw new Exception("Direction is not valid");
        }
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return int
     */
    public function getDirection(): int
    {
        return $this->direction;
    }


    /**
     * @return array<int>
     */
    public function getOrderBy(): array
    {
        return [$this->field => intval($this->direction)];
    }

    public function jsonSerialize(): string
    {
        return $this->field;
    }
}
