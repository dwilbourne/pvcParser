<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\symbol;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\symbol\DecimalSymbol;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;

class DecimalSymbolTest extends TestCase
{

    public function testConstruct1() : void
    {
        $type = NumberFormatter::DECIMAL_SEPARATOR_SYMBOL;
        $symbol = new DecimalSymbol($type);
        self::assertTrue($symbol instanceof DecimalSymbol);
        self::assertEquals($type, $symbol->getSymbolType());
    }

    public function testConstruct2() : void
    {
        $type = NumberFormatter::MONETARY_SEPARATOR_SYMBOL;
        $symbol = new DecimalSymbol($type);
        self::assertTrue($symbol instanceof DecimalSymbol);
        self::assertEquals($type, $symbol->getSymbolType());
    }

    public function testValidateSymbolType() : void
    {
        $type = 95;
        self::expectException(SetSymbolTypeException::class);
        $symbol = new DecimalSymbol($type);
    }
}
