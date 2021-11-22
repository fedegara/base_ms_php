<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

//TODO: Create & Implement IQueryResponse

final class QueryResponse
{
    /**
     * @var array<string,array<string>>
     */
    private $data;

    /**
     * @var int
     */
    private $found;

    /**
     * @return array<string,array<string>>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string,array<string>> $data
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
