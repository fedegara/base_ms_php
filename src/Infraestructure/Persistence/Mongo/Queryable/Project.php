<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable;


class Project
{
    /*
     * EXAMPLE:
     $project: {_id: 0, type: "$_id"}
     *
     * _id or type are the Projection Keys [mongoField]
     * 0 or $_id are the Projection Values to assign to each key (If its integer, is used to show or hide the Match fields) [displayValue]
     *
     */


    /** @var string */
    private $mongoField;
    /** @var string|int|array */
    private $displayValue;

    /**
     * Project constructor.
     * @param string $mongoField
     * @param string|int|array $displayValue
     */
    public function __construct(string $mongoField, $displayValue)
    {
        $this->mongoField = $mongoField;
        $this->displayValue = $displayValue;
    }

    /**
     * @return string|int|array
     */
    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    /**
     * @return string
     */
    public function getMongoField(): string
    {
        return $this->mongoField;
    }

    /**
     * @return array
     */
    public function buildProject(): array
    {
        return [
            $this->mongoField => $this->displayValue
        ];
    }
}