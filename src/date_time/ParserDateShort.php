<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\date_time;

use IntlDateFormatter;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\intl\TimeZoneInterface;
use pvc\interfaces\msg\MsgInterface;

class ParserDateShort extends ParserDateTime
{
    public function __construct(MsgInterface $msg, LocaleInterface $locale, TimeZoneInterface $timeZone)
    {
        $formatter = new IntlDateFormatter(
            (string)$locale,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::NONE,
            (string)$timeZone
        );
        parent::__construct($msg, $locale, $timeZone, $formatter);
    }


    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'not_short_date';
        $msgParameters = [];
        $domain = 'Parser';
        $msg->setContent($domain, $msgId, $msgParameters);
    }
}
