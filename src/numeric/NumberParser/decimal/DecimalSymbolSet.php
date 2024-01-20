<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\decimal;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\core\ConfigurationSet;
use pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * Class DecimalSymbolSet
 */
class DecimalSymbolSet extends ConfigurationSet
{
    /**
     * @var bool
     */
    protected bool $monetary;

    /**
     * @var Symbol
     */
    protected Symbol $decimalSymbol;

    /**
     * DecimalSymbolSet constructor.
     * @param bool $monetary
     */
    public function __construct(bool $monetary)
    {
        $this->setMonetary($monetary);
    }

    /**
     * @function setMonetary
     * @param bool $monetary
     */
    public function setMonetary(bool $monetary) : void
    {
        $this->monetary = $monetary;
    }

    /**
     * @function getMonetary
     * @return bool
     */
    public function getMonetary(): bool
    {
        return $this->monetary;
    }

    /**
     * @function setDecimalSymbol
     * @param Symbol $symbol
     * @throws InvalidSymbolException
     * @throws \pvc\parser\numeric\NumberParser\symbol\err\UnsetSymbolValueException
     */
    public function setDecimalSymbol(Symbol $symbol) : void
    {
        if (!$symbol->isDecimal()) {
            throw new InvalidSymbolException('decimal', $symbol);
        }
        if ($symbol->isMonetary() != $this->monetary) {
            throw new InvalidSymbolException('monetary', $symbol);
        }
        $this->decimalSymbol = $symbol;
    }

    /**
     * @function getDecimalSymbol
     * @return Symbol
     */
    public function getDecimalSymbol(): Symbol
    {
        return $this->decimalSymbol;
    }

    /**
     * @function getAllSymbols
     * @return array|Symbol[]
     */
    public function getAllSymbols(): array
    {
        return isset($this->decimalSymbol) ? [$this->decimalSymbol] : [];
    }

    /**
     * @function configureOptions
     * @param NumberFormatter $frmtr
     * @return bool
     */
    public function configureOptions(NumberFormatter $frmtr): bool
    {
        // configure any symbol substitutions
        return $this->configureSymbolOptionSet($frmtr);
    }
}
