<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

use App\Infraestructure\Persistence\Mongo\Queryable\Filter;
use App\Infraestructure\Persistence\Mongo\Queryable\FilterGroup;
use Exception;

class MatchPipeline extends BasePipeline
{
    /**
     * MatchPipeline constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(self::STAGE_MATCH);
    }

    /**
     * @param Filter|FilterGroup $filter
     * @return $this
     */
    public function addFilter($filter): self
    {
        $this->actions[] = $filter;
        return $this;
    }

    /**
     * @return array<int|mixed>
     * @throws Exception
     */
    public function parsePipeline(): array
    {
        $matches = [];
        array_walk($this->actions, function ($filter) use (&$matches) {
            $matches = array_merge($matches, $filter->getFilter());
        });
        return [$this->getStage() => $matches];
    }
}
