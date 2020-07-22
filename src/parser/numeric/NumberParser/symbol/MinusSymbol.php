<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\symbol;

use NumberFormatter;

class MinusSymbol extends Symbol
{
    /**
     * MinusSymbol constructor.
     * @param bool $quoted
     * @throws err\SetSymbolTypeException
     */
    public function __construct(bool $quoted = false)
    {
        parent::setSymbolType(NumberFormatter::MINUS_SIGN_SYMBOL, $quoted);
    }
}
