<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\symbol;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;

/**
 * Class DecimalSymbol
 */
class DecimalSymbol extends Symbol
{
    /**
     * DecimalSymbol constructor.
     * @param int $type
     * @param bool $quoted
     * @throws SetSymbolTypeException
     */
    public function __construct(int $type, bool $quoted = false)
    {
        if (!$this->validateSymbolType($type)) {
            throw new SetSymbolTypeException($type);
        }
        parent::setSymbolType($type, $quoted);
    }

    /**
     * @function validateSymbolType
     * @param int $type
     * @return bool
     */
    public function validateSymbolType(int $type): bool
    {
        return ($type == NumberFormatter::DECIMAL_SEPARATOR_SYMBOL
            || $type == NumberFormatter::MONETARY_SEPARATOR_SYMBOL);
    }
}
