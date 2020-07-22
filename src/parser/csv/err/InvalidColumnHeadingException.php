<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\csv\err;

use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\err\throwable\exception\stock_rebrands\ErrorException;
use pvc\msg\ErrorExceptionMsg;

/**
 * Class InvalidColumnHeadingException
 * @package pvc\parser\file\csv\err
 */
class InvalidColumnHeadingException extends ErrorException
{
    public function __construct()
    {
        $msgText = 'Invalid column heading.  Must be text or integer.';
        $msgVars = [];
        $msg = new ErrorExceptionMsg($msgVars, $msgText);
        $code = ec::INVALID_COLUMN_HEADING_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
