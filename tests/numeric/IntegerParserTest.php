<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvcTests\parser\numeric;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\numeric\IntegerParser;

class IntegerParserTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;

    protected IntegerParser $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->locale->method('__toString')->willReturn('en_US');
        $this->parser = new IntegerParser($this->msg, $this->locale);
    }

    /**
     * testPattern
     * sanity check
     */
    public function testPattern(): void
    {
        $expectedPattern = '#,##0';
        self::assertEquals($expectedPattern, $this->parser->getFrmtr()->getPattern());
    }

    /**
     * @function testParse
     * @param string $input
     * @param int|bool $expectedResult
     * @dataProvider numberProvider
     */
    public function testParse(string $input, $expectedResult, $comment): void
    {
        $actualResult = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $actualResult, $comment);
    }

    public function numberProvider(): array
    {
        return [
            ['12345', 12345, 'failed to pass 12345'],
            ['0', 0, 'failed to pass 0'],
            ['12345.0040', false, 'wrongly passed a fractional number'],
            ['123K45', false, 'wrongly passed string with alpha char'],
            ['12345.', false, 'wrongly passed string with trailing decimal point'],
            ['12345.000', false, 'wrongly passed string with decimal point and trailing zeros'],
            ['12,345', 12345, 'failed to pass string with grouping separator'],
            ['-50', -50, 'failed to pass -50'],
            ['50-', false, 'wrongly passed 50-'],
            ['0-', false, 'wrongly passed 0-'],
        ];
    }

    public function testBasic(): void
    {
        $input = '12345';
        $expectedResult = 12345;
        $actualResult = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function testAllowGroupingSeparator(): void
    {
        /**
         * test the default behavior - grouping separator is allowed
         */
        $testString = '12,345';
        $expectedResult = 12345;
        self::assertTrue($this->parser->parseValue($testString));
        self::assertEquals($expectedResult, $this->parser->getParsedValue());

        /**
         * turn it off
         */
        $this->parser->allowGroupingSeparator(false);
        self::assertFalse($this->parser->parseValue($testString));

        /**
         * furn it back on
         */
        $this->parser->allowGroupingSeparator(true);
        $testString = '12345';
        /**
         * grouping separator is optional
         */
        self::assertTrue($this->parser->parseValue($testString));

        /**
         * but it will not allow a grouping separator in the wrong place
         */
        $testString = '123,45';
        self::assertFalse($this->parser->parseValue($testString));
    }
}
