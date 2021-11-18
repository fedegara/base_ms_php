<?php

namespace App\Domain\Error;

use Exception;

final class DataNotFound extends Exception
{
    private const ERROR_NUM = 204;

    public function __construct(string $message)
    {
        parent::__construct($message, self::ERROR_NUM);
    }
}
