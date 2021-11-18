<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable;


use Exception;


class FilterGroup
{
    const MODE_INCLUSIVE = true; //AND
    const MODE_EXCLUSIVE = false; //OR

    /**
     * @var array
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
    public function __construct($mode = self::MODE_INCLUSIVE)
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
        $this->filters[md5(json_encode($filter->getValue()))] = $filter;
        return $this;
    }

    /**
     * @return array|null
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
            }
            else {
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