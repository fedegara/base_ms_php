<?php


namespace App\Infraestructure\Persistence\Mongo\Queryable;


class Sort
{
    /** @var string */ //TODO: Add array
    private $mongoField;
    /** @var int */
    private $value;

    /**
     * Project constructor.
     * @param string $mongoField
     * @param int $value
     */
    public function __construct(string $mongoField, int $value)
    {
        $this->mongoField = $mongoField;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
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
    public function buildSort(): array
    {
        return [
            $this->mongoField => $this->value
        ];
    }
}