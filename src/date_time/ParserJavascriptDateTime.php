<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\date_time;

use DateMalformedStringException;
use DateTimeImmutable;
use DateTimeZone;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\Parser;

/**
 * Class ParserJavascriptDateTime
 * @extends Parser<float>
 *
 * A fully formed JavascriptDateTime string looks like '2012-07-15T13:54:56Z-05:00'.  The timezone info
 * at the end of the string is optional - the local timezone is assumed if none is specified.
 * DateTimeImmutable will parse timezone info of all kinds
 */
class ParserJavascriptDateTime extends Parser
{
    protected DateTimeZone $tz;

    public function __construct(MsgInterface $msg)
    {
        parent::__construct($msg);
    }

    protected function parseValue(string $data): bool
    {
        $data = str_replace('Z', '', $data);
        try {
            /**
             * if tz is ommitted, the local timezone is assumed EXCEPT if the timezone info is specified
             * in the input string.  If the input string contains timezone info, the timezone parameter
             * to the constructor is ignored.
             */
            $dt = new DateTimeImmutable($data);
        } catch (DateMalformedStringException $e) {
            return false;
        }

        $this->parsedValue = $dt->getTimestamp();
        return true;
    }

    /**
     * setMsgContent
     * @param MsgInterface $msg
     */
    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'not_javascript_datetime';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}
