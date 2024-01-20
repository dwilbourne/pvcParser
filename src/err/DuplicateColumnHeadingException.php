<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\parser\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class DuplicateColumnHeadingException
 */
class DuplicateColumnHeadingException extends LogicException
{
    public function __construct(string $heading, Throwable $prev = null)
    {
        parent::__construct($heading, $prev);
    }
}