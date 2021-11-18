<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

use App\Infraestructure\Persistence\Mongo\Queryable\Sort;

class SortPipeline extends BasePipeline
{
    public function __construct()
    {
        parent::__construct(self::STAGE_SORT);
    }

    public function addSort(Sort $sort): self
    {
        $this->actions[] = $sort;
        return $this;
    }

    public function parsePipeline(): array
    {
        $sorts = [];

        array_walk($this->actions, function (Sort $sort) use (&$sorts) {
            $sorts[$sort->getMongoField()] = $sort->getValue();
        });

        return [$this->getStage() => $sorts];
    }
}
