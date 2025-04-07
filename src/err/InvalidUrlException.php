<?php

declare(strict_types=1);

namespace pvc\parser\err;

use pvc\err\stock\LogicException;
use Throwable;

class InvalidUrlException extends LogicException
{
    public function __construct(string $badUrl, ?Throwable $previous = null)
    {
        parent::__construct($badUrl, $previous);
    }
}
