<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\parser\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class NonExistentFilePathException
 */
class NonExistentFilePathException extends LogicException
{
    public function __construct(string $filePath, Throwable $prev = null)
    {
        parent::__construct($filePath, $prev);
    }
}