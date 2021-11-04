<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

final class Aggregate extends BaseAggregate
{
    /** @var String */
    private $collection;
    /** @var BasePipeline[] */
    private $pipelines;

    /**
     * @param String|null $collection
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
     * @return array
     */
    public function getPipelineParsed(): array
    {
        $return = null;
        foreach ($this->pipelines as $pipeline) {
            $return[] = $pipeline->parsePipeline();
        }
        return $return;
    }
}