<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\prefix_suffix;

use Countable;
use pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolFromCharException;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * Class Ix
 */
abstract class Ix implements Countable
{
    protected array $symbols = [];

    /**
     * @function merge
     * @param Ix $ix
     * @return mixed
     */
    abstract public function merge(Ix $ix);

    /**
     * @function addSymbol
     * @param Symbol $symbol
     * @throws InvalidSymbolException
     * @throws \pvc\parser\numeric\NumberParser\symbol\err\UnsetSymbolValueException
     */
    public function addSymbol(Symbol $symbol) : void
    {
        if (!$symbol->isPositional()) {
            throw new InvalidSymbolException('positional', $symbol);
        }
        $this->symbols[] = $symbol;
    }

    /**
     * @function addSymbolArray
     * @param array $symbolArray
     * @throws InvalidSymbolException
     */
    public function addSymbolArray(array $symbolArray) : void
    {
        foreach ($symbolArray as $symbol) {
            $this->addSymbol($symbol);
        }
    }

    /**
     * @function addSymbolsFromString
     * @param string $chars
     * @throws InvalidSymbolException
     * @throws SetSymbolFromCharException
     * @throws SetSymbolTypeException
     */
    public function addSymbolsFromString(string $chars) : void
    {
        if (0 < strlen($chars)) {
            foreach (str_split($chars) as $char) {
                $symbol = new Symbol();
                $symbol->setSymbolFromChar($char);
                $this->addSymbol($symbol);
            }
        }
    }

    /**
     * @function getIx
     * @return string
     */
    public function getIx(): string
    {
        $result = '';
        foreach ($this->symbols as $symbol) {
            $result .= $symbol->getPatternChar();
        }
        return $result;
    }

    /**
     * @function getSymbols
     * @return array
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * @function isEmpty
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (0 == count($this->symbols));
    }

    /**
     * @function count
     * @return int
     */
    public function count() : int
    {
        return count($this->symbols);
    }
}
