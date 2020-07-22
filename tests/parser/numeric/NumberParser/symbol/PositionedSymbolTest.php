<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\symbol;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\symbol\CurrencySymbol;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;
use pvc\parser\numeric\NumberParser\symbol\MinusSymbol;
use pvc\parser\numeric\NumberParser\symbol\PercentSymbol;
use pvc\parser\numeric\NumberParser\symbol\PlusSymbol;

class PositionedSymbolTest extends TestCase
{

    public function testPlusMinusSymbols() : void
    {
        $symbol = new PlusSymbol();
        self::assertTrue($symbol->isPositional());

        $symbol = new MinusSymbol();
        self::assertTrue($symbol->isPositional());

        $symbol = new CurrencySymbol(NumberFormatter::CURRENCY_SYMBOL);
        self::assertTrue($symbol->isPositional());

        $symbol = new CurrencySymbol(NumberFormatter::INTL_CURRENCY_SYMBOL);
        self::assertTrue($symbol->isPositional());

        $symbol = new PercentSymbol(NumberFormatter::PERCENT_SYMBOL);
        self::assertTrue($symbol->isPositional());

        $symbol = new PercentSymbol(NumberFormatter::PERMILL_SYMBOL);
        self::assertTrue($symbol->isPositional());
    }

    public function testSetSymbolTypeExceptionWithCurrencySymbol() : void
    {
        $symbol = 43;
        self::expectException(SetSymbolTypeException::class);
        $symbol = new CurrencySymbol($symbol);
    }

    public function testSetSymbolTypeExceptionWithPercentSymbol() : void
    {
        $symbol = 43;
        self::expectException(SetSymbolTypeException::class);
        $symbol = new PercentSymbol($symbol);
    }
}
