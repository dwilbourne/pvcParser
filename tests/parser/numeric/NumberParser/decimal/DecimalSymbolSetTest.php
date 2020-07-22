<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\decimal;

use Mockery;
use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\decimal\DecimalSymbolSet;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

class DecimalSymbolSetTest extends TestCase
{

    protected DecimalSymbolSet $dss;

    public function setUp(): void
    {
        $monetary = false;
        $this->dss = new DecimalSymbolSet($monetary);
    }

    public function testSetGetGroupingSeparatorSymbol1() : void
    {
        $symbol = Mockery::mock('pvc\parser\numeric\NumberParser\symbol\Symbol');
        $symbol->shouldReceive('isDecimal')->withNoArgs()->andReturn(true);
        $symbol->shouldReceive('isMonetary')->withNoArgs()->andReturn(false);
        $this->dss->setDecimalSymbol($symbol);
        self::assertEquals($symbol, $this->dss->getDecimalSymbol());
    }

    public function testSetGetGroupingSeparatorSymbol2() : void
    {
        $this->dss->setMonetary(true);
        $symbol = Mockery::mock('pvc\parser\numeric\NumberParser\symbol\Symbol');
        $symbol->shouldReceive('isDecimal')->withNoArgs()->andReturn(false);
        $symbol->shouldReceive('isMonetary')->withNoArgs()->andReturn(true);
        $symbol->shouldReceive('getPatternChar')->withNoArgs()->andReturn(';');
        self::expectException('pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException');
        $this->dss->setDecimalSymbol($symbol);
    }

    public function testSetGetGroupingSeparatorSymbol3() : void
    {
        $this->dss->setMonetary(true);
        $symbol = Mockery::mock('pvc\parser\numeric\NumberParser\symbol\Symbol');
        $symbol->shouldReceive('isDecimal')->withNoArgs()->andReturn(true);
        $symbol->shouldReceive('isMonetary')->withNoArgs()->andReturn(false);
        $symbol->shouldReceive('getPatternChar')->withNoArgs()->andReturn(';');
        self::expectException('pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException');
        $this->dss->setDecimalSymbol($symbol);
    }

    public function testConfigureOptions1() : void
    {
        $monetary = false;
        $frmtr = new NumberFormatter('de-DE', NumberFormatter::DECIMAL);
        $dsos = new DecimalSymbolSet($monetary);
        $symbol = new Symbol();
        $type = NumberFormatter::DECIMAL_SEPARATOR_SYMBOL;
        $quoted = true;
        $value = '@';
        $symbol->setSymbolType($type, $quoted);
        $symbol->setSymbolValue($value);
        $dsos->setDecimalSymbol($symbol);
        self::assertTrue($dsos->configureOptions($frmtr));
        self::assertEquals($value, $frmtr->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL));
        self::assertFalse($dsos->getMonetary());
    }

    public function testConfigureOptions2() : void
    {
        $monetary = true;
        $frmtr = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        $dsos = new DecimalSymbolSet($monetary);
        $symbol = new Symbol();
        $type = NumberFormatter::MONETARY_SEPARATOR_SYMBOL;
        $quoted = true;
        $value = '@';
        $symbol->setSymbolType($type, $quoted);
        $symbol->setSymbolValue($value);
        $dsos->setDecimalSymbol($symbol);
        self::assertTrue($dsos->configureOptions($frmtr));
        self::assertEquals($value, $frmtr->getSymbol(NumberFormatter::MONETARY_SEPARATOR_SYMBOL));
        self::assertTrue($dsos->getMonetary());
    }
}
