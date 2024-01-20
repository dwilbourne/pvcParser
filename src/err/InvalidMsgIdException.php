<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\parser\err;

use pvc\err\stock\LogicException;
use Throwable;

/**
 * Class InvalidMsgIdException
 */
class InvalidMsgIdException extends LogicException
{
    public function __construct(string $msgId, Throwable $prev = null)
    {
        parent::__construct($msgId, $prev);
    }
}