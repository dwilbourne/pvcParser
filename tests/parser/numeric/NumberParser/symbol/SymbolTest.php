<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\symbol;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolFromCharException;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;
use pvc\parser\numeric\NumberParser\symbol\err\UnsetSymbolValueException;
use pvc\parser\numeric\NumberParser\symbol\Symbol;
use PHPUnit\Framework\TestCase;

class SymbolTest extends TestCase
{

    protected Symbol $symbol;

    public function setUp(): void
    {
        $this->symbol = new Symbol();
    }

    public function testSetGetSymbolTypeWithPlusSignSymbol() : void
    {
        $type = NumberFormatter::PLUS_SIGN_SYMBOL;
        $this->symbol->setSymbolType($type);
        self::assertEquals($type, $this->symbol->getSymbolType());
        self::assertFalse($this->symbol->getQuoted());
    }

    public function testSetGetSymbolTypeWithCurrencySymbol() : void
    {
        $type = NumberFormatter::CURRENCY_SYMBOL;
        $this->symbol->setSymbolType($type, true);
        self::assertEquals($type, $this->symbol->getSymbolType());
        self::assertTrue($this->symbol->getQuoted());
    }

    public function testSetGetSymbolTypeWithLiteral() : void
    {
        $type = Symbol::LITERAL;
        $this->symbol->setSymbolType($type, true);
        self::assertEquals($type, $this->symbol->getSymbolType());
        // quoted parameter is ignored when type is a literal
        self::assertFalse($this->symbol->getQuoted());
    }

    public function testSetGetSymbolTypeWithInvalidSymbolConstant() : void
    {
        $type = 999;
        $this->expectException(SetSymbolTypeException::class);
        $this->symbol->setSymbolType($type);
    }

    public function testIsLiteralPlusSignSymbol() : void
    {
        $type = NumberFormatter::PLUS_SIGN_SYMBOL;
        $this->symbol->setSymbolType($type);
        self::assertFalse($this->symbol->isLiteral());
    }

    public function testIsLiteralWithListeralTypeSymbol() : void
    {
        $type = Symbol::LITERAL;
        $this->symbol->setSymbolType($type);
        self::assertTrue($this->symbol->isLiteral());
    }

    public function testSetGetSymbolValueWithPlusSignSymbol() : void
    {
        $type = NumberFormatter::PLUS_SIGN_SYMBOL;
        $this->symbol->setSymbolType($type);
        self::assertEquals('', $this->symbol->getSymbolValue());
    }

    public function testSetGetSymbolValueWithListeralTypeSymbol() : void
    {
        $type = Symbol::LITERAL;
        $this->symbol->setSymbolType($type);
        $symbolValue = 'P';
        $this->symbol->setSymbolValue($symbolValue);
        self::assertEquals($symbolValue, $this->symbol->getSymbolValue());
    }

    public function testSetSymbolFromCharWithPlusSign() : void
    {
        $char = '+';
        $this->symbol->setSymbolFromChar($char);
        self::assertEquals(NumberFormatter::PLUS_SIGN_SYMBOL, $this->symbol->getSymbolType());
        self::assertEquals('', $this->symbol->getSymbolValue());
    }

    public function testSetSymbolFromCharWithLetterB() : void
    {
        $char = 'B';
        $this->symbol->setSymbolFromChar($char);
        self::assertEquals(Symbol::LITERAL, $this->symbol->getSymbolType());
        self::assertEquals($char, $this->symbol->getSymbolValue());
    }

    public function testSetSymbolFromCharWithStringABC() : void
    {
        $char = 'ABC';
        $this->expectException(SetSymbolFromCharException::class);
        $this->symbol->setSymbolFromChar($char);
    }

    public function testGetPatternCharWithPlusSign() : void
    {
        $char = '+';
        $this->symbol->setSymbolFromChar($char);
        self::assertEquals($char, $this->symbol->getPatternChar());
    }

    public function testGetPatternCharWithLetterB() : void
    {
        $char = 'B';
        $this->symbol->setSymbolFromChar($char);
        self::assertEquals($char, $this->symbol->getPatternChar());
    }

    public function testGetPatternCharWithLiteralType() : void
    {
        $type = Symbol::LITERAL;
        $this->symbol->setSymbolType($type);
        $this->expectException(UnsetSymbolValueException::class);
        self::assertEquals('', $this->symbol->getPatternChar());
    }

    public function testGetPatternCharWithQuotedPlusSignSymbol() : void
    {
        $type = NumberFormatter::PLUS_SIGN_SYMBOL;
        $quoted = true;
        $this->symbol->setSymbolType($type, $quoted);
        $expectedResult = "'+'";
        self::assertEquals($expectedResult, $this->symbol->getPatternChar());
    }
}
