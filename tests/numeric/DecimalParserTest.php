<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace numeric;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\numeric\DecimalParser;

class DecimalParserTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;

    protected DecimalParser $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->locale->method('__toString')->willReturn('en_US');
        $this->parser = new DecimalParser($this->msg, $this->locale);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\numeric\DecimalParser::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(DecimalParser::class, $this->parser);
    }

    /**
     * testParse
     * @param string $input
     * @param mixed $expectedResult
     * @param string $comment
     * @dataProvider decimalDataProvider
     * @covers       \pvc\parser\numeric\DecimalParser::parseValue
     * @covers       \pvc\parser\numeric\DecimalParser::setMsgContent
     */
    public function testParse(string $input, mixed $expectedResult, string $comment): void
    {
        $actualResult = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $actualResult, $comment);
    }

    public function decimalDataProvider(): array
    {
        return [
            ['123', 123.0, 'failed to parse 123'],
            ['123.0', 123.0, 'failed to parse 123.0'],
            ['123.', 123.0, 'failed to parse 123.'],
            /**
             * by default accepts grouping separators
             */
            ['1,234.567', 1234.567, 'failed to parse 1,234.567'],
            /**
             * max digits and rounding mode play no role in parsing.  The parser gobbles all digits.
             */
            ['123.476512964', 123.476512964, 'failed to parse 123.476512964'],
            ['123.45000', 123.45, 'failed to parse 123.45000'],
            ['123.45a9', false, 'wrongly parsed 123.45a9'],
        ];
    }
}
