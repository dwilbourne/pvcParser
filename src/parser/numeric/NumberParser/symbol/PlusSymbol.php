<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\symbol;

use NumberFormatter;

/**
 * Class PlusSymbol
 */
class PlusSymbol extends Symbol
{
    /**
     * PlusSymbol constructor.
     * @param bool $quoted
     * @throws err\SetSymbolTypeException
     */
    public function __construct(bool $quoted = false)
    {
        parent::setSymbolType(NumberFormatter::PLUS_SIGN_SYMBOL, $quoted);
    }
}
