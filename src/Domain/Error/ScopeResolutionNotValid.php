<?php

namespace App\Domain\Error;

use Exception;

final class ScopeResolutionNotValid extends Exception
{
    private const ERROR_NUM = 500;

    public function __construct(string $message)
    {
        parent::__construct($message, self::ERROR_NUM);
    }
}
