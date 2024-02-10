<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace numeric;

use NumberFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\InvalidReturnTypeException;
use pvc\parser\numeric\NumericParser;

class NumericParserTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;

    protected NumberFormatter|MockObject $formatter;

    protected NumericParser|MockObject $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->locale->method('__toString')->willReturn('en_US');
        $this->formatter = $this->createMock(NumberFormatter::class);
        $args = [$this->msg, $this->locale, $this->formatter];
        $this->parser = $this->getMockForAbstractClass(NumericParser::class, $args);
    }

    /**
     * testGetLocale
     * @covers \pvc\parser\numeric\NumericParser::getLocale
     */
    public function testGetLocale(): void
    {
        self::assertEquals($this->locale, $this->parser->getLocale());
    }

    /**
     * testGetFrmtr
     * @covers \pvc\parser\numeric\NumericParser::__construct
     * @covers \pvc\parser\numeric\NumericParser::getFrmtr
     */
    public function testGetFrmtr(): void
    {
        self::assertInstanceOf(NumberFormatter::class, $this->parser->getFrmtr());
    }

    /**
     * testSetReturnTypeThrowsExceptionWithBadArgument
     * @throws InvalidReturnTypeException
     * @covers \pvc\parser\numeric\NumericParser::setReturnType
     */
    public function testSetReturnTypeThrowsExceptionWithBadArgument(): void
    {
        $badReturnType = 5;
        self::expectException(InvalidReturnTypeException::class);
        $this->parser->setReturnType($badReturnType);
    }

    /**
     * testSetGetReturnType
     * @throws InvalidReturnTypeException
     * @covers \pvc\parser\numeric\NumericParser::setReturnType
     * @covers \pvc\parser\numeric\NumericParser::getReturnType
     */
    public function testSetGetReturnType(): void
    {
        $type = NumberFormatter::TYPE_INT64;
        $this->parser->setReturnType($type);
        self::assertEquals($type, $this->parser->getReturnType());
    }

    /**
     * testReturnTypeHasDefaultValue
     * @covers \pvc\parser\numeric\NumericParser::__construct
     */
    public function testReturnTypeHasDefaultValue(): void
    {
        self::assertisInt($this->parser->getReturnType());
    }

    public function testNotAllowGroupingSeparator(): void
    {
        $this->formatter->expects($this->once())->method('setAttribute')->with(NumberFormatter::GROUPING_USED, 0);
        $this->formatter->expects($this->once())->method('getAttribute')->willReturn(0);
        $this->parser->allowGroupingSeparator(false);
        self::assertFalse((bool)$this->parser->isGroupingSeparatorAllowed());
    }

    public function testAllowGroupingSeparator(): void
    {
        $this->formatter->expects($this->once())->method('setAttribute')->with(NumberFormatter::GROUPING_USED, 1);
        $this->formatter->expects($this->once())->method('getAttribute')->willReturn(1);
        $this->parser->allowGroupingSeparator(true);
        self::assertTrue((bool)$this->parser->isGroupingSeparatorAllowed());
    }
}
