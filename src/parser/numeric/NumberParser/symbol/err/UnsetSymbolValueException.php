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
class UnsetSymbolValueException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        $msgText = 'Error trying to get symbol value for a literal where the symbol value is not set.';
        $vars = [];
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::UNSET_SYMBOL_VALUE_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
