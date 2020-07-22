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
 * Class NonExistentColumnHeadingException
 * @package pvc\parser\csv\err
 */
class NonExistentColumnHeadingException extends Exception
{
    public function __construct()
    {
        $msgText = 'Flag set to get column headings from first row of data but data is empty.';
        $msgVars = [];
        $msg = new ErrorExceptionMsg($msgVars, $msgText);
        $code = ec::NONEXISTENT_COLUMN_HEADING_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
