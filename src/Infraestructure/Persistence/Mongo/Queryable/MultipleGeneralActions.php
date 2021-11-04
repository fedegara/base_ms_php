<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable;


class MultipleGeneralActions
{
    /*
        * EXAMPLE:
        *  _id: {year: {$year: "$dimension.date"},month: {$month: "$dimension.date"}}
        *
        *
        */


    /** @var string */
    private $displayName;
    /** @var GeneralAction[] */
    private $actions;

    /**
     * MultipleGeneralActions constructor.
     * @param string $displayName
     */
    public function __construct(string $displayName = null)
    {
        $this->displayName = $displayName;
    }


    /**
     * @param GeneralAction $action
     * @return $this
     */
    public function addAction(GeneralAction $action): self
    {
        $this->actions[] = $action;
        return $this;
    }


}