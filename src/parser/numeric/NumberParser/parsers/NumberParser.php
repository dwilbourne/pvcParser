<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\parsers;

use NumberFormatter;
use pvc\intl\Locale;
use pvc\msg\UserMsg;
use pvc\parser\numeric\NumberParser\configurations\NumberParserConfiguration;
use pvc\parser\numeric\NumberParser\parsers\err\CalcNumDecimalPlacesException;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;
use pvc\parser\Parser;
use pvc\parser\ParserInterface;

/**
 * Class ParserNumberFormatter
 */
class NumberParser extends Parser implements ParserInterface
{
    /**
     * @var NumberParserConfiguration
     */
    protected NumberParserConfiguration $configuration;

    /**
     * @var Locale
     */
    protected Locale $locale;

    /**
     * @var NumberFormatter
     */
    protected NumberFormatter $frmtr;

    /**
     * @var NumberParserPrecisionRangeNonNegative
     */
    protected NumberParserPrecisionRangeNonNegative $precisionRange;

    /**
     * @var int|null
     */
    protected ?int $numDecimalPlaces;

    /**
     * NumberParser constructor.
     * @param Locale $locale
     * @param NumberParserConfiguration $configuration
     * @param NumberParserPrecisionRangeNonNegative $range
     */
    public function __construct(
        Locale $locale,
        NumberParserConfiguration $configuration,
        NumberParserPrecisionRangeNonNegative $range
    ) {
        $this->setLocale($locale);
        $this->setConfiguration($configuration);
        $this->setPrecisionRange($range);
    }

    /**
     * @function setConfiguration
     * @param NumberParserConfiguration $configuration
     */
    public function setConfiguration(NumberParserConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @function getConfiguration
     * @return NumberParserConfiguration
     */
    public function getConfiguration(): NumberParserConfiguration
    {
        return $this->configuration;
    }

    /**
     * @function setLocale
     * @param Locale $locale
     */
    public function setLocale(Locale $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @function getLocale
     * @return Locale
     */
    public function getLocale(): Locale
    {
        return $this->locale;
    }

    /**
     * @function setPrecisionRange
     * @param NumberParserPrecisionRangeNonNegative $range
     */
    public function setPrecisionRange(NumberParserPrecisionRangeNonNegative $range) : void
    {
        $this->precisionRange = $range;
    }

    /**
     * @function getPrecisionRange
     * @return NumberParserPrecisionRangeNonNegative
     */
    public function getPrecisionRange(): NumberParserPrecisionRangeNonNegative
    {
        return $this->precisionRange;
    }

    /**
     * @function getNumDecimalPlaces
     * @return int|null
     */
    public function getNumDecimalPlaces(): ?int
    {
        return $this->numDecimalPlaces;
    }

    /**
     * @function parse
     * @param string $fileContents
     * @return bool
     */
    public function parse(string $fileContents): bool
    {
        foreach ($this->configuration->getNumberFormatterConfiguration() as $nfconfig) {
            $this->frmtr = $this->configuration->getNumberFormatter($this->locale);
            $nfconfig->configure($this->frmtr);
            if ($this->parseValue($this->frmtr, $fileContents)) {
                return true;
            }
        }
        $this->createParseErrMsg($fileContents);
        return false;
    }

    /**
     * @function parseValue
     * @param NumberFormatter $frmtr
     * @param string $value
     * @return bool
     * @throws CalcNumDecimalPlacesException
     */
    public function parseValue(NumberFormatter $frmtr, string $value): bool
    {
        $pos = 0;
        $expectedParseLength = mb_strlen($value);
        // type is set in the child classes of this class
        $result = $frmtr->parse($value, $this->configuration->getFormatterType(), $pos);

        if ($pos != $expectedParseLength || $result == false) {
            return false;
        }

        $constant = ($this->configuration->getFormatterStyle() == NumberFormatter::CURRENCY) ?
            NumberFormatter::MONETARY_SEPARATOR_SYMBOL :
            NumberFormatter::DECIMAL_SEPARATOR_SYMBOL;
        $decimalSeparatorSymbol = $frmtr->getSymbol($constant);
        $this->numDecimalPlaces = static::calcNumDecimalPlaces($decimalSeparatorSymbol, $value);

        if ($this->precisionRange->containsValue($this->numDecimalPlaces)) {
            $this->setParsedValue($result);
            $this->setErrmsg(null);
            return true;
        } else {
            $this->createPrecisionErrmsg();
            return false;
        }
    }

    /**
     * @function calcNumDecimalPlaces
     * @param string $decimalSeparatorSymbol
     * @param string $value
     * @return int
     * @throws CalcNumDecimalPlacesException
     */
    public static function calcNumDecimalPlaces(string $decimalSeparatorSymbol, string $value): int
    {
        // if the separator appears more than once in the string, throw an exception
        if (substr_count($value, $decimalSeparatorSymbol) > 1) {
            throw new CalcNumDecimalPlacesException();
        }

        // if the separator is not present in the string, the precision is (by convention) -1.  This is to
        // differentiate it from the number of decimal places in an input like '123.' which is precision = 0.
        $fractionalPart = mb_strrchr($value, $decimalSeparatorSymbol);
        if ($fractionalPart === false) {
            return -1;
        }
        // return the number of digits after the separator
        return mb_strlen($fractionalPart) - 1;
    }

    /**
     * @function createParseErrMsg
     * @param string $value
     */
    protected function createParseErrMsg(string $value): void
    {
        $msgText = 'Unable to parse value = %s into a decimal.';
        $vars = [$value];
        $msg = new UserMsg($vars, $msgText);
        $this->setErrmsg($msg);
    }

    /**
     * @function createPrecisionErrmsg
     */
    protected function createPrecisionErrmsg(): void
    {
        $msgText = 'Input must have a decimal precision in the range of %s.';
        $vars = [];
        $msg = new UserMsg($vars, $msgText);
        $this->setErrmsg($msg);
    }
}
