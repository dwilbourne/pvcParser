<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\numeric;

use NumberFormatter;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * Class IntegerParser
 * @extends NumericParser<int>
 * Parses a string of numbers into an integer.  Does not accept grouping separators by default.
 */
class IntegerParser extends NumericParser
{
    /**
     * IntegerParser constructor.
     */
    public function __construct(MsgInterface $msg, LocaleInterface $locale)
    {
        $frmtr = new NumberFormatter((string)$locale, NumberFormatter::DECIMAL);
        parent::__construct($msg, $locale, $frmtr);

        /**
         * pattern does not have a decimal separator or any fractional digits
         */
        $this->getFrmtr()->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        /**
         * without this flag, the parser parses a number like 123.456, return 123 as the parsed value but the pos
         * offset is at the end of the string, even if the pattern has no decimal separator and fractional digits
         */
        $this->getFrmtr()->setAttribute(NumberFormatter::PARSE_INT_ONLY, 1);
    }


    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'not_integer';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}
