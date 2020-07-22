<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\parsers;

use PHPUnit\Framework\TestCase;
use pvc\intl\Locale;
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserDecimalConfigurationPvc;
use pvc\parser\numeric\NumberParser\parsers\NumberParserIntegerDecimal;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;

class NumberParserIntegerDecimalTest extends TestCase
{

    protected NumberParserIntegerDecimal $parser;

    public function setUp(): void
    {
        $locale = new Locale('en_US');
        $config = new NumberParserDecimalConfigurationPvc();
        $precisionRange = new NumberParserPrecisionRangeNonNegative();

        // the precision will allow strings with no decimal separator or strings where there is
        // at least one zero following the decimal separator, up to a maximum precision of 3
        $precisionRange->addItem(-1);
        $precisionRange->addRangeSpec('1-3');

        $this->parser = new NumberParserIntegerDecimal($locale, $config, $precisionRange);
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
            'basic test 4 succeeds with just decimal point' => ['12345.', false],
            'basic test 5 succeeds with decimal point and trailing zeros' => ['12345.000', true, 12345],
            'basic test 6 fails with decimal point and too many trailing zeros' => ['12345.0000', false],
            'preceding negative sign OK' => ['-50', true, -50],
            'succeeding negative OK also' => ['50-', true, -50],
        ];
    }
}
