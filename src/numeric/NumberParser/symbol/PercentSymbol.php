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
 * Class PercentSymbolOptionSet
 */
class PercentSymbol extends Symbol
{
    /**
     * PercentSymbol constructor.
     * @param int $type
     * @param bool $quoted
     * @throws SetSymbolTypeException
     */
    public function __construct(int $type, bool $quoted = false)
    {
        if (!$this->validateSymbolType($type)) {
            throw new SetSymbolTypeException($type);
        }
        parent::setSymbolType(NumberFormatter::PERCENT_SYMBOL, $quoted);
    }

    /**
     * @function validateSymbolType
     * @param int $type
     * @return bool
     */
    public function validateSymbolType(int $type): bool
    {
        return ($type == NumberFormatter::PERCENT_SYMBOL || $type == NumberFormatter::PERMILL_SYMBOL);
    }
}
