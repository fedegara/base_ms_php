<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

use App\Infraestructure\Persistence\Mongo\Queryable\Project;

class ProjectPipeline extends BasePipeline
{
    public function __construct()
    {
        parent::__construct(self::STAGE_PROJECT);
    }

    public function addProject(Project $project): self
    {
        $this->actions[] = $project;
        return $this;
    }

    /**
     * @return array<array<int|mixed>>
     */
    public function parsePipeline(): array
    {
        $projections = [];

        array_walk($this->actions, function (Project $project) use (&$projections) {
            $projections[$project->getMongoField()] = $project->getDisplayValue();
        });

        return [$this->getStage() => $projections];
    }
}
