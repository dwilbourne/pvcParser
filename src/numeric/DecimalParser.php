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
 * Class DecimalParser
 * @extends NumericParser<float>
 *
 * You can set the min and max number of permitted fractional digits in the string to be parsed as well as the
 * rounding mode for the remaining portion of the input string.
 *
 * The underlying NumberFormatter object ensures that the min fractional digits are always less than or equal to the
 * max fractional digits.
 */
class DecimalParser extends NumericParser
{

    public function __construct(MsgInterface $msg, LocaleInterface $locale)
    {
        $frmtr = new NumberFormatter((string)$locale, NumberFormatter::DECIMAL);
        parent::__construct($msg, $locale, $frmtr);
        $this->setReturnType(NumberFormatter::TYPE_DOUBLE);
    }

    /**
     * @inheritDoc
     */
    protected function getMsgId(): string
    {
        return 'not_decimal';
    }

    /**
     * @inheritDoc
     */
    protected function getMsgParameters(): array
    {
        return [];
    }
}
