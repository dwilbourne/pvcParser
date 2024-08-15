<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\date_time;

use DateTimeZone;
use IntlDateFormatter;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * Class ParserDateShortTimeShort
 */
class ParserDateShortTimeShort extends ParserDateTime
{

    public function __construct(MsgInterface $msg, LocaleInterface $locale, DateTimeZone $timeZone)
    {
        parent::__construct($msg, $locale, $timeZone);
        parent::setDateType(IntlDateFormatter::SHORT);
        parent::setTimeType(IntlDateFormatter::SHORT);
    }

    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'not_short_date_time';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}
