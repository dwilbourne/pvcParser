<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core\err;

use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\err\throwable\Throwable;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * Class InvalidClassNamesArrayException
 */
class DuplicateSymbolValueException extends Exception implements Throwable
{
    public function __construct(Symbol $symbol, \Throwable $previous = null)
    {
        $msgText = 'Duplicate and conflicting symbol value set for symbol type = %d, new value = %s.';
        $vars = [$symbol->getSymbolType(), $symbol->getSymbolValue()];
        $msg = new ErrorExceptionMsg($vars, $msgText);

        $code = ec::DUPLICATE_SYMBOL_VALUE_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
