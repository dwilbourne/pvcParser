<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\symbol\err;

use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use Throwable;

/**
 * Class SetSymbolValueException
 */
class SetSymbolFromCharException extends Exception
{
    public function __construct(string $char, Throwable $previous = null)
    {
        $msgText = 'Error trying to set symbol from char.  Argument must be a single character (value = %s).';
        $vars = [$char];
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::SET_SYMBOL_FROM_CHAR_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
