<?php

namespace App\Domain\DTO;

use JsonSerializable;

class Brand implements JsonSerializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url_name;

    /**
     * Brand constructor.
     * @param int $id
     * @param string $name
     * @param string $url_name
     */
    public function __construct(int $id, string $name, string $url_name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url_name = $url_name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrlName(): string
    {
        return $this->url_name;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url_name' => $this->url_name
        ];
    }
}
