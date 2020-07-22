<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\precision;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\parsers\err\CalcNumDecimalPlacesException;
use pvc\parser\numeric\NumberParser\parsers\NumberParser;
use pvc\range\non_negative_integer\NonNegativeIntegerRange;

/**
 * Class NumberParserPrecisionRangeNonNegative
 */
class NumberParserPrecisionRangeNonNegative extends NonNegativeIntegerRange
{
    /**
     * NumberParserPrecisionRangeNonNegative constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $validator = new NumberParserPrecisionRangeValidator();
        $this->setValidator($validator);
    }

    /**
     * @function getCurrencyDefaultPrecision
     * @param string $locale
     * @return int
     * @throws CalcNumDecimalPlacesException
     */
    public function getCurrencyDefaultPrecision(string $locale): int
    {
        $frmtr = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $pattern = $frmtr->getPattern();
        return NumberParser::calcNumDecimalPlaces('.', $pattern);
    }
}
