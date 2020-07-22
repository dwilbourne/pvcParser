<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\symbol;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;
use pvc\parser\numeric\NumberParser\symbol\GroupingSymbol;
use PHPUnit\Framework\TestCase;

class GroupingSymbolTest extends TestCase
{


    public function testConstructWithGroupingSeparatorSymbol() : void
    {
        $type = NumberFormatter::GROUPING_SEPARATOR_SYMBOL;
        $symbol = new GroupingSymbol($type);
        self::assertTrue($symbol instanceof GroupingSymbol);
        self::assertEquals($type, $symbol->getSymbolType());
    }

    public function testConstructWithMonetaryGroupingSeparatorSymbol() : void
    {
        $type = NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL;
        $symbol = new GroupingSymbol($type);
        self::assertTrue($symbol instanceof GroupingSymbol);
        self::assertEquals($type, $symbol->getSymbolType());
    }

    public function testValidateSymbolType() : void
    {
        $type = 95;
        self::expectException(SetSymbolTypeException::class);
        $symbol = new GroupingSymbol($type);
    }
}
