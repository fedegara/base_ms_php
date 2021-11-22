<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

//TODO: Create & Implement IQueryResponse
use ArrayObject;
use MongoDB\Model\{BSONArray, BSONDocument};

final class QueryResponse
{
    /**
     * @var array<BSONArray|BSONDocument|ArrayObject>
     */
    private $data;

    /**
     * @var int
     */
    private $found;

    /**
     * @return array<BSONArray|BSONDocument|ArrayObject>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<BSONArray|BSONDocument|ArrayObject> $data
     * @return $this
     */
    public function setData(array $data): QueryResponse
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return int
     */
    public function getFound(): int
    {
        return $this->found;
    }

    /**
     * @param int $found
     * @return $this
     */
    public function setFound(int $found): QueryResponse
    {
        $this->found = $found;
        return $this;
    }
}
