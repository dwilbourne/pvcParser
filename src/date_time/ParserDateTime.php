<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\date_time;

use IntlDateFormatter;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\intl\TimeZoneInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\Parser;

/**
 * Class ParserDateTime
 * @extends Parser<float>
 *     parsedValue is a timestamp.  This class has no notion of whether the pattern in IntlDateFormatter permits
 * fractions of a second or not so the data type for the parsedValue is treated as a float, not an int
 */
abstract class ParserDateTime extends Parser
{

    protected IntlDateFormatter $intlDateFormatter;

    protected LocaleInterface $locale;

    protected TimeZoneInterface $timeZone;

    public function __construct(
        MsgInterface $msg,
        LocaleInterface $locale,
        TimeZoneInterface $timeZone,
        IntlDateFormatter $intlDateFormatter
    ) {
        parent::__construct($msg);
        $this->locale = $locale;
        $this->timeZone = $timeZone;
        $this->intlDateFormatter = $intlDateFormatter;
    }

    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }

    public function getTimeZone(): TimeZoneInterface
    {
        return $this->timeZone;
    }

    public function getIntlDateFormatter(): IntlDateFormatter
    {
        return $this->intlDateFormatter;
    }

    /**
     * @function parseValue
     * @param string $data
     * @return bool
     */
    public function parseValue(string $data): bool
    {
        $pos = 0;
        $expectedPos = strlen($data);
        $result = $this->getIntlDateFormatter()->parse($data, $pos);

        /**
         * IntlDateFormatter 'fails gracefully' if it successfully parses a part of a string, e.g. it will not throw
         * an exception if it parses the first x characters of the string into the return type and can't parse any
         * more from the x + 1 character to the end of the string.  The $pos variable holds the offset of the last
         * character successfully parsed.
         *
         * There is also the possibility that it parses the whole string but the result is false.  I came across an
         * date time combination where this was the case: try parsing the date / time '5/9/96 23:14' with the en_US
         * locale.  It expects an am/pm designation and does not permit the hours to be more than 12.  So it gets to
         * the end of the string but the parse is unsuccessful.
         *
         */
        if (($pos == $expectedPos) && ($result !== false)) {
            $this->parsedValue = $result;
            return true;
        } else {
            return false;
        }
    }
}
