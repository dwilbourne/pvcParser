<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\parsers;

use pvc\intl\Locale;
use pvc\msg\UserMsg;
use pvc\parser\numeric\NumberParser\configurations\NumberParserIntegerConfiguration;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;

/**
 * Class NumberParserInteger
 */
class NumberParserInteger extends NumberParser
{
    public function __construct(Locale $locale, NumberParserIntegerConfiguration $configuration)
    {
        $precisionRange = new NumberParserPrecisionRangeNonNegative();
        $precisionRange->addItem(-1);
        parent::__construct($locale, $configuration, $precisionRange);
    }

    public function createPrecisionErrmsg(): void
    {
        $msgText = 'Value could not be parsed into a pure integer (e.g. no decimal point, no trailing zeros).';
        $vars = [];
        $msg = new UserMsg($vars, $msgText);
        $this->setErrmsg($msg);
    }
}
