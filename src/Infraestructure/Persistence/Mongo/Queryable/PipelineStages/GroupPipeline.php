<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;


use App\Infraestructure\Persistence\Mongo\Queryable\GeneralAction;
use App\Infraestructure\Persistence\Mongo\Queryable\MultipleGeneralActions;
use Exception;

class GroupPipeline extends BasePipeline
{

    /**
     * This is the principal action of a group is used to indicate the agroupation type.
     * This could be null
     * @var array|string|null
     */
    private $id;

    /** @var GeneralAction[] */
    private $filters;

    /**
     * GroupPipeline constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(BasePipeline::STAGE_GROUP);
        $this->id = null;
        $this->filters = [];
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function addId($id): self
    {
        $this->id = $id;
        return $this;
    }

    public function addFilter(GeneralAction $action): self
    {
        $this->filters[] = $action;
        return $this;
    }

    public function addAction($action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function parsePipeline(): array
    {
        $id = [];

        foreach ($this->filters as $filter) {
            $_filter = $filter->buildAction();
            $id[key($_filter)] = $_filter[key($_filter)];
        }
        $actions = [];
        foreach ($this->actions as $action) {
            $_action = $action->buildAction();
            $actions[key($_action)] = $_action[key($_action)];
        }

        return [
            $this->getStage() => array_merge([
                '_id' => (!empty($this->getId()) ? $this->getId() : $id)
            ], $actions)
        ];
    }

    /**
     * @return array|string|null
     */
    public function getId()
    {
        return $this->id;
    }


}