<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\parsers;

use PHPUnit\Framework\TestCase;
use pvc\intl\Locale;
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserIntegerConfigurationPvc;
use pvc\parser\numeric\NumberParser\parsers\NumberParserInteger;

class NumberParserIntegerTest extends TestCase
{

    protected NumberParserInteger $parser;

    public function setUp(): void
    {
        $locale = new Locale('en_US');
        $config = new NumberParserintegerConfigurationPvc();
        $this->parser = new NumberParserInteger($locale, $config);
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
            'basic test 1' => ['12345', true, 12345],
            'basic test 2 includes decimal point' => ['12345.0040', false],
            'basic test 3 fails with alpha char' => ['123K45', false],
            'basic test 4 fails with decimal point' => ['12345.', false],
            'basic test 5 fails with decimal point and trailing zeros' => ['12345.000', false],
            'preceding plus sign OK' => ['+50', true, 50],
            'succeeding plus sign OK' => ['50+', true, 50],
            'preceding negative sign OK' => ['-50', true, -50],
            'succeeding negative sign OK' => ['50-', true, -50],
            'parentheses OK' => ['(50)', true, -50],
        ];
    }
}
