<?php

namespace App\Infraestructure\Persistence\Mongo\Queryable;

use Exception;

class Filter
{
    public const EQUAL = '==';
    public const MAJOR_EQUAL = '>=';
    public const MAJOR = '>';
    public const LESS_EQUAL = '<=';
    public const LESS = '<';
    public const DISTINCT = '!=';
    public const BETWEEN = '~';
    public const IN = '^';
    public const NOT_IN = '!^';
    public const LIKE = '@@';
    public const IS_NULL = 'IS NULL';
    public const IS_NOT_NULL = 'IS NOT NULL';
    public const CONTAIN = '@@';
    public const NOT_CONTAIN = '!@@';
    public const GROUP_BY = 'GROUP BY';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string;
     */
    private $operator;

    /**
     * @var array<string,string>|string
     */
    private $value;

    /**
     * Filter constructor.
     * @param string $field
     * @param string $operator
     * @param int|string|string[]|number[]|boolean|boolean[]|mixed|mixed[] $value
     * @throws Exception
     */
    public function __construct(string $field, string $operator, $value)
    {
        $this
            ->setOperator($operator)
            ->checkValue($value)
            ->setField($field)
            ->setValue($value);
    }

    /**
     * @param array<int|mixed>|null $value
     * @return $this
     * @throws Exception
     */
    protected function checkValue($value): Filter
    {
        if (is_null($value)) {
            throw new Exception("MongoDBFilter: Value cannot be null");
        }
        switch ($this->getOperator()) {
            case self::IN:
            case self::NOT_IN:
                if (!is_array($value)) {
                    throw new Exception("MongoDBFilter: For operator ({$this->getOperator()} value must be an array");
                }
                break;
            case self::BETWEEN:
                if (!isset($value['min']) || !isset($value['max'])) {
                    throw new Exception("MongoDBFilter: For operator ({$this->getOperator()} value must be an array and must have the keys min and max");
                }
                break;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @return Filter
     */
    public function setOperator(string $operator): self
    {
        $this->operator = $operator;
        return $this;
    }

    /**
     * @return array<string|mixed>
     * @throws Exception
     */
    public function jsonSerialize(): array
    {
        return
            [
                'filter' => $this->getField(),
                'operator' => $this->getOperator(),
                'filter_params' => $this->getFilter()
            ];
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return Filter
     */
    public function setField(string $field): self
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getFilter(): ?array
    {
        $return = null;

        switch ($this->getOperator()) {
            case self::EQUAL:
                $return = [$this->getField() => $this->getValue()];
                break;
            case self::MAJOR:
                $return = [$this->getField() => ['$gt' => $this->getValue()]];
                break;
            case self::MAJOR_EQUAL:
                $return = [$this->getField() => ['$gte' => $this->getValue()]];
                break;
            case self::LESS:
                $return = [$this->getField() => ['$lt' => $this->getValue()]];
                break;
            case self::LESS_EQUAL:
                $return = [$this->getField() => ['$lte' => $this->getValue()]];
                break;
            case self::IN:
                $return = [$this->getField() => ['$in' => $this->getValue()]];
                break;
            case self::NOT_IN:
                $return = [$this->getField() => ['$nin' => $this->getValue()]];
                break;
            case self::BETWEEN:
                if (!is_array($this->getValue()) || !isset($this->getValue()['min']) || !isset($this->getValue()['max'])) {
                    throw  new Exception("Between value must have a min and max value");
                }
                $return = [
                    $this->getField() =>
                        [
                            '$gte' => $this->getValue()['min'],
                            '$lte' => $this->getValue()['max']
                        ]
                ];
                break;
            case self::CONTAIN:
                if(!is_string($this->getValue())){
                    throw  new Exception("Contain filter must be a simple string");
                }
                $return = [$this->getField() => ['$regex' => ".*{$this->getValue()}.*", '$options' => 'i']];
                break;
            case self::GROUP_BY:
                $return = [
                    '$group' => [
                        $this->getField() => ['$sum' => $this->getValue()]
                    ],
                    'count' => ['$sum' => 1]
                ];
                break;
        }
        return $return;
    }

    /**
     * @return array<string,string>|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array<string,string>|string $value
     * @return Filter
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
