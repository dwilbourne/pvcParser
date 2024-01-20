<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations;

use NumberFormatter;

/**
 * Class NumberParserDecimalConfiguration
 */
class NumberParserDecimalConfiguration extends NumberParserConfiguration
{
    public function __construct()
    {
        $frmtrStyle = NumberFormatter::DECIMAL;
        $frmtrType = NumberFormatter::TYPE_DOUBLE;

        parent::__construct($frmtrStyle, $frmtrType);
    }
}
