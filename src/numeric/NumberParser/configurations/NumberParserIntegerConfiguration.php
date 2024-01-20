<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations;

use NumberFormatter;

/**
 * Class NumberParserIntegerConfiguration
 */
class NumberParserIntegerConfiguration extends NumberParserConfiguration
{
    public function __construct()
    {
        $frmtrStyle = NumberFormatter::DECIMAL;
        $frmtrType = (PHP_INT_SIZE == 8) ? NumberFormatter::TYPE_INT64 : NumberFormatter::TYPE_INT32;

        parent::__construct($frmtrStyle, $frmtrType);
    }
}
