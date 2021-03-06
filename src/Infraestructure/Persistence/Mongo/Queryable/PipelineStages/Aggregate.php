<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

use App\Infraestructure\Persistence\Mongo\Queryable\GeneralAction;

final class Aggregate extends BaseAggregate
{
    /** @var ?string */
    private $collection;
    /** @var BasePipeline[] */
    private $pipelines;

    /**
     * @param string|null $collection
     */
    public function __construct(String $collection = null)
    {
        $this->collection = $collection;
    }

    /**
     * @param String $collection
     * @return Aggregate
     */
    public function setCollection(String $collection): self
    {
        $this->collection = $collection;
        return $this;
    }


    /**
     * @return String|null
     */
    public function getCollection(): ?String
    {
        return $this->collection;
    }


    /**
     * @return BasePipeline[]
     */
    public function getPipelines(): array
    {
        return $this->pipelines;
    }

    /**
     * @param BasePipeline $pipelines
     * @return Aggregate
     */
    public function addPipelines(BasePipeline $pipelines): self
    {
        $this->pipelines[] = $pipelines;
        return $this;
    }

    /**
     * @return array<GeneralAction|int|mixed>
     */
    public function getPipelineParsed(): array
    {
        $return = [];
        foreach ($this->pipelines as $pipeline) {
            $return[] = $pipeline->parsePipeline();
        }
        return $return;
    }
}
