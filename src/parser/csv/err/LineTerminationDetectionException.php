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
 * Class InvalidLineTerminationException
 * @package pvc\parser\file\csv\err
 */
class LineTerminationDetectionException extends Exception
{
    public function __construct()
    {
        $msgText = '';
        $msgText .= 'Unable to detect a valid record (line) termination sequence.  Must be either CRLF or CR ';
        $msgText .= 'and either neither was detected or both were detected.';
        $msgVars = [];
        $msg = new ErrorExceptionMsg($msgVars, $msgText);
        $code = ec::INVALID_LINE_TERMINATOR_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
