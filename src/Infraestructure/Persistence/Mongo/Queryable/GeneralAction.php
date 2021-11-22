<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

class GeneralAction
{
    /*
     * EXAMPLE:
     *  'total_views': {$sum: '$metrics.visitors.allPageViews.total_views'}
     *
     * total_views is the displayName
     * $sum is the operation
     * $metrics.visitors.allPageViews.total_views is the mongoField
     *
     */


    /** @var string */
    private $displayName;
    /** @var string */
    private $action;
    /** @var int|string */
    private $mongoField;

    /**
     * Action constructor.
     * @param string $displayName
     * @param string $action
     * @param int|string $mongoField
     */
    public function __construct(string $displayName, string $action, $mongoField)
    {
        $this->displayName = $displayName;
        $this->action = $action;
        $this->mongoField = $mongoField;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return int|string
     */
    public function getMongoField()
    {
        return $this->mongoField;
    }


    /**
     * @return array<string, array<string, int|string>|int|string>
     */
    public function buildAction(): array
    {
        return (empty($this->action)) ? [$this->displayName => $this->mongoField] : [
            $this->displayName => [$this->action => $this->mongoField]
        ];
    }
}
