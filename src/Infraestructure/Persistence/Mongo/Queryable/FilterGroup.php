<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

use Exception;

class FilterGroup
{
    public const MODE_INCLUSIVE = true; //AND
    public const MODE_EXCLUSIVE = false; //OR

    /**
     * @var array<Filter>
     */
    private $filters;

    /**
     * @var bool | true -> inclusivo | false -> exclusivo
     */
    private $mode = true;

    /**
     * FilterGroup constructor.
     * @param bool $mode
     */
    public function __construct(bool $mode = self::MODE_INCLUSIVE)
    {
        $this->setMode($mode);
        $this->filters = [];
    }

    /**
     * @param Filter $filter
     * @return $this
     */
    public function addFilter(Filter $filter): FilterGroup
    {
        $json_encode = json_encode($filter->getValue());
        if(!$json_encode){
            throw  new Exception("Error transform tor json: ".json_last_error_msg());
        }
        $this->filters[md5($json_encode)] = $filter;
        return $this;
    }

    /**
     * @return array<string,mixed>|null
     * @throws Exception
     */
    public function getFilter(): ?array
    {
        if (!empty($this->getFilters())) {
            $filters = [];
            foreach ($this->getFilters() as $filter) {
                if ($filter instanceof Filter) {
                    $filters [] = $filter->getFilter();
                }
            }
            if ($this->getMode()) {
                $filters = ['$and' => $filters];
            } else {
                $filters = ['$or' => $filters];
            }
            return $filters;
        }
        return null;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return bool|true
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param bool|true $mode
     * @return $this
     */
    public function setMode(bool $mode = self::MODE_INCLUSIVE): FilterGroup
    {
        $this->mode = $mode;
        return $this;
    }
}
