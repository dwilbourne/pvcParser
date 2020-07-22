<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\csv\err;

use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\msg\ErrorExceptionMsg;

/**
 * Class InvalidFieldDelimiterException
 * @package pvc\parser\file\csv\err
 */
class InvalidFieldDelimiterException extends Exception
{
    public function __construct()
    {
        $msgText = "field delimiter must be a single character";
        $msgVars = [];
        $msg = new ErrorExceptionMsg($msgVars, $msgText);
        $code = ec::INVALID_FIELD_DELIMITER_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
