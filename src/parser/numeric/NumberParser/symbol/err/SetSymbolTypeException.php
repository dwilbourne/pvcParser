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
class SetSymbolTypeException extends Exception
{
    public function __construct(int $type, Throwable $previous = null)
    {
        $msgText = '';
        $msgText .= 'Error trying to set symbol type.  Type must either be Symbol::LITERAL ';
        $msgText .= 'or one of the NumberFormatter symbol constants like PLUS_SIGN_SYMBOL, etc. (value = %s)';
        $vars = [$type];
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::SET_SYMBOL_TYPE_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
