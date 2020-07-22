<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\parsers;

use PHPUnit\Framework\TestCase;

use pvc\intl\Locale;
use pvc\msg\UserMsg;
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserDecimalConfigurationPvc;
use pvc\parser\numeric\NumberParser\parsers\NumberParserDecimal;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;

class NumberParserDecimalTest extends TestCase
{

    protected NumberParserDecimal $parser;

    public function setUp(): void
    {
        $locale = new Locale('en_US');
        $config = new NumberParserDecimalConfigurationPvc();
        $precisionRange = new NumberParserPrecisionRangeNonNegative();
        // the precision will allow strings with no decimal separator or strings where there is
        // at least one digit following the decimal separator, up to a maximum precision of 3
        $precisionRange->addItem(-1);
        $precisionRange->addRangeSpec('1-3');
        $this->parser = new NumberParserDecimal($locale, $config, $precisionRange);
    }

    public function testDecimalParsePrecision() : void
    {
        $input = '12345.1';
        $expectedResult = 12345.1;
        self::assertTrue($this->parser->parse($input));
        self::assertEquals($expectedResult, $this->parser->getParsedValue());

        $input = '12345.123';
        $expectedResult = 12345.123;
        self::assertTrue($this->parser->parse($input));
        self::assertEquals($expectedResult, $this->parser->getParsedValue());

        $input = '12345.1234';
        self::assertFalse($this->parser->parse($input));

        // all integers are decimals but not vice versa
        $input = '12345';
        $expectedResult = 12345;
        self::assertTrue($this->parser->parse($input));
        self::assertEquals($expectedResult, $this->parser->getParsedValue());

        // zero digits after decimal point is not OK
        $input = '12345.';
        self::assertFalse($this->parser->parse($input));
    }

    public function testErrmsg() : void
    {
        $value = '123ab';
        $result = $this->parser->parse($value);
        self::assertFalse($result);
        $msg = $this->parser->getErrmsg() ?: new UserMsg();
        $vars = $msg->getMsgVars();
        self::assertEquals($value, $vars[0]);
        self::assertEquals(1, count($vars));
    }

    public function testNegativeNumberParse() : void
    {
        $input = '-12345.1';
        $expectedResult = -12345.1;
        self::assertTrue($this->parser->parse($input));
        self::assertEquals($expectedResult, $this->parser->getParsedValue());
    }

    /**
     * @dataProvider numberProvider
     * @param string $input
     * @param bool $succeeded
     * @param null $expectedResult
     */
    public function testDecimalParse(string $input, bool $succeeded, $expectedResult = null) : void
    {
        self::assertEquals($succeeded, $this->parser->parse($input));
        if ($succeeded) {
            self::assertEquals($expectedResult, $this->parser->getParsedValue());
        }
    }

    public function numberProvider() : array
    {
        return [

            'basic test 1' => ['12345.12', true, 12345.12],
            'basic test 2' => ['12345.123', true, 12345.123],
            'basic test 3' => ['12345.0040', false],
            'basic test 4' => ['12345.0', true, 12345.0],
            'basic test 5' => ['12345.', false],
            'basic test 6' => ['12345', true, 12345],

            // check signs
            'sign test 1' => ['-12345.12', true, -12345.12],
            'sign test 2' => ['+12345.12', true, 12345.12],
            'sign test 3' => ['12345.12+', true, 12345.12],
            'sign test 4' => ['12345.12-', true, -12345.12],
            'sign test 5' => ['(12345.12)', true, -12345.12],
            'sign test 6' => ['+12345.12+', false],
            'sign test 7' => ['-12345.12-', false],

            // check grouping separator and grouping size
            'grouping test 1' => ['-12,345.12', true, -12345.12],
            'grouping test 2' => ['-123,45.12', false],

        ];
    }
}
