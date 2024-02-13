<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\err;

use pvc\err\stock\RuntimeException;
use Throwable;

/**
 * Class FileHandleException
 */
class FileHandleException extends RuntimeException
{
    public function __construct(string $fileName, Throwable $prev = null)
    {
        parent::__construct($fileName, $prev);
    }
}