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
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserCurrencyConfigurationPvc;
use pvc\parser\numeric\NumberParser\parsers\NumberParserCurrency;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;

class NumberParserCurrencyTest extends TestCase
{

    protected Locale $locale;
    protected NumberParserCurrencyConfigurationPvc $config;
    protected NumberParserPrecisionRangeNonNegative $precisionRange;
    protected NumberParserCurrency $parser;

    public function setUp(): void
    {
        $this->locale = new Locale('en_US');
        $pureDecimalAllowed = false;
        $this->config = new NumberParserCurrencyConfigurationPvc($pureDecimalAllowed);

        $this->precisionRange = new NumberParserPrecisionRangeNonNegative();
        $this->precisionRange->addItem(-1);
        $i = $this->precisionRange->getCurrencyDefaultPrecision($this->locale);
        $this->precisionRange->addItem($i);

        $this->parser = new NumberParserCurrency($this->locale, $this->config, $this->precisionRange);
    }

    /**
     * @function testParse
     * @param string $input
     * @param bool $succeeded
     * @param int|null $expectedResult
     *
     * @dataProvider numberProvider
     *
     */
    public function testParse(string $input, bool $succeeded, int $expectedResult = null) : void
    {
        self::assertEquals($succeeded, $this->parser->parse($input));
        if ($succeeded) {
            self::assertEquals($expectedResult, $this->parser->getParsedValue());
        }
    }

    public function numberProvider() : array
    {
        return [
            '$12345' => ['$12345', true, 12345],
            'basic test 2 includes decimal point' => ['12345.0', false],
            'basic test 3 fails with alpha char' => ['123K45', false],
            'basic test 4 fails with decimal point' => ['12345.', false],
            'basic test 5 fails with decimal point and too many trailing zeros' => ['12345.000', false],
            'basic test 6 good with decimal point and correct number of trailing zeros' => ['$12345.00', true, 12345],
            'preceding plus sign OK' => ['+$50', true, 50],
            'succeeding plus sign fails without currency symbol' => ['50+', false],
            'preceding negative sign OK' => ['-$50', true, -50],
            'succeeding negative sign OK' => ['$50-', true, -50],
            'parentheses and then $ OK' => ['($50)', true, -50],
            '$ and then parentheses not OK' => ['$(50)', false],
            'preceding minus sign and trailing currency code' => ['-50USD', true, -50],
        ];
    }

    public function testParseGetErrmsg() : void
    {
        $value = '12345.0';
        self::assertFalse($this->parser->parse($value));
        $msg = $this->parser->getErrmsg() ?: new UserMsg();
        $expectedString = 'Unable to parse value = %s into a currency value.';
        self::assertEquals($expectedString, $msg->getMsgText());
    }

    public function testParsePureDecimalAllowed() : void
    {
        $pureDecimalAllowed = true;
        $config = new NumberParserCurrencyConfigurationPvc($pureDecimalAllowed);
        $parser = new NumberParserCurrency($this->locale, $config, $this->precisionRange);

        $input = '50-';
        $expectedResult = -50;

        self::assertTrue($parser->parse($input));
        self::assertEquals($expectedResult, $parser->getParsedValue());
    }
}
