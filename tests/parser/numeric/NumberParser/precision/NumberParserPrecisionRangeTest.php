<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\precision;

use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeValidator;

class NumberParserPrecisionRangeTest extends TestCase
{
    protected NumberParserPrecisionRangeNonNegative $range;

    public function setUp(): void
    {
        $this->range = new NumberParserPrecisionRangeNonNegative();
    }

    public function testConstruct() : void
    {
        self::assertTrue($this->range instanceof NumberParserPrecisionRangeNonNegative);
        self::assertTrue($this->range->getValidator() instanceof NumberParserPrecisionRangeValidator);
    }

    public function testGetCurrencyDefaultPrecision() : void
    {
        $locale = 'en-US';
        self::assertEquals(2, $this->range->getCurrencyDefaultPrecision($locale));
    }
}
