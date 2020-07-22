<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\prefix_suffix\err;

use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\parser\numeric\NumberParser\symbol\err\UnsetSymbolValueException;
use pvc\parser\numeric\NumberParser\symbol\Symbol;
use Throwable;

/**
 * Class InvalidFormatException
 */
class InvalidSymbolException extends InvalidArgumentException
{
    /**
     * InvalidSymbolException constructor.
     * @param string $symbolType
     * @param Symbol $symbol
     * @param Throwable|null $previous
     * @throws UnsetSymbolValueException
     */
    public function __construct(string $symbolType, Symbol $symbol, Throwable $previous = null)
    {
        $msgText = 'Invalid symbol - must be of type %s (value = %s).';
        $vars = [$symbolType, $symbol->getPatternChar()];
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::INVALID_SYMBOL_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
