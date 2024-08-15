<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\date_time;

use DateTimeZone;
use IntlDateFormatter;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\InvalidDateTimeTypeException;

class ParserDateShort extends ParserDateTime
{
    /**
     * @throws InvalidDateTimeTypeException
     */
    public function __construct(MsgInterface $msg, LocaleInterface $locale, DateTimeZone $timeZone)
    {
        parent::__construct($msg, $locale, $timeZone);
        parent::setDateType(IntlDateFormatter::SHORT);
    }


    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'not_short_date';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}
