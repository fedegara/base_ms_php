<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable\PipelineStages;

use App\Infraestructure\Persistence\Mongo\Queryable\GeneralAction;
use Exception;
use ReflectionClass;

abstract class BasePipeline
{
    public const STAGE_MATCH = '$match';
    public const STAGE_GROUP = '$group';
    public const STAGE_PROJECT = '$project';
    public const STAGE_SORT = '$sort';

    /** @var array<GeneralAction|int|mixed> */
    protected $actions;
    /** @var String */
    private $stage;

    /**
     * @param String $stage
     * @throws Exception
     */
    public function __construct(string $stage)
    {
        $this->checkStage($stage);
        $this->stage = $stage;
    }

    /**
     * @param string $stage
     * @throws Exception
     */
    private function checkStage(string $stage): void
    {
        $oClass = new ReflectionClass(__CLASS__);
        if (!in_array($stage, array_values($oClass->getConstants()))) {
            throw new Exception("Error: " . __CLASS__ . "::" . __METHOD__ . " Stage is not valid");
        }
    }

    /**
     * @return String
     */
    public function getStage(): string
    {
        return $this->stage;
    }

    /**
     * @return array<GeneralAction|int|mixed>
     */
    abstract public function parsePipeline(): array;
}
