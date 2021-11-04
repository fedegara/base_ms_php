<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable;


use Exception;

class Order
{
    const ORDER_ASC = 1;
    const ORDER_DESC = -1;

    /** @var string */
    private $field;

    /** @var string */
    private $direction;

    /**
     * MongoOrder constructor.
     * @param string $field
     * @param string $direction
     */
    public function __construct(string $field, string $direction = self::ORDER_ASC)
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
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }


    public function getOrderBy(): array
    {
        return [$this->field => intval($this->direction)];
    }

    public function jsonSerialize(): string
    {
        return $this->field;
    }
}