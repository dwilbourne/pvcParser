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
use pvc\parser\err\InvalidDateTimeTypeException;
use pvc\parser\Parser;

/**
 * Class ParserDateTime
 * @extends Parser<float>
 *
 * wrapper for the IntlDateFormatter class.
 * parsedValue is a timestamp.  This class has no notion of whether the pattern in IntlDateFormatter permits
 * fractions of a second or not so the data type for the parsedValue (e.g. the generic data type for the class)
 * is float, not int.
 *
 * The locale and timezone attributes look like they might be redundant because IntlDateFormatter also
 * has those attributes.  However, in order to keep everything pvc-branded (encapsulated), this object
 * has properties which are the pvc implementations of these concepts.  The IntlDateFormatter returns a string
 * from getLocale and an IntlTimeZone object from getTimeZone, so the attributes in this class are just object instances
 * of the correct type which can be returned from the getters.
 */
abstract class ParserDateTime extends Parser
{

    protected LocaleInterface $locale;

    protected DateTimeZone $timeZone;

    protected int $dateType;

    protected int $timeType;

    protected int $calendarType;

    public function __construct(
        MsgInterface $msg,
        LocaleInterface $locale,
        DateTimeZone $timeZone,
    ) {
        parent::__construct($msg);
        $this->setLocale($locale);
        $this->setTimeZone($timeZone);
        $this->setDateType(IntlDateFormatter::NONE);
        $this->setTimeType(IntlDateFormatter::NONE);
        $this->setCalendarType(IntlDateFormatter::GREGORIAN);
    }

    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }

    public function setLocale(LocaleInterface $locale): void
    {
        $this->locale = $locale;
    }

    public function getTimeZone(): DateTimeZone
    {
        return $this->timeZone;
    }

    public function setTimeZone(DateTimeZone $timeZone): void
    {
        $this->timeZone = $timeZone;
    }

    public function getDateType(): int
    {
        return $this->dateType;
    }

    public function setDateType(int $dateType): void
    {
        $validDateTypes = [
            IntlDateFormatter::NONE,
            IntlDateFormatter::FULL,
            IntlDateFormatter::LONG,
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::RELATIVE_FULL,
            IntlDateFormatter::RELATIVE_LONG,
            IntlDateFormatter::RELATIVE_MEDIUM,
            IntlDateFormatter::RELATIVE_SHORT,
        ];
        if (!in_array($dateType, $validDateTypes)) {
            throw new InvalidDateTimeTypeException();
        }
        $this->dateType = $dateType;
    }

    public function getTimeType(): int
    {
        return $this->timeType;
    }

    public function setTimeType(int $timeType): void
    {
        $validTimeTypes = [
            IntlDateFormatter::NONE,
            IntlDateFormatter::FULL,
            IntlDateFormatter::LONG,
            IntlDateFormatter::MEDIUM,
            IntlDateFormatter::SHORT,
        ];
        if (!in_array($timeType, $validTimeTypes)) {
            throw new InvalidDateTimeTypeException();
        }
        $this->timeType = $timeType;
    }

    public function getCalendarType(): int
    {
        return $this->calendarType;
    }

    public function setCalendarType(int $calendarType): void
    {
        $validCalendarTypes = [IntlDateFormatter::GREGORIAN, IntlDateFormatter::TRADITIONAL];
        if (!in_array($calendarType, $validCalendarTypes)) {
            throw new InvalidDateTimeTypeException();
        }
        $this->calendarType = $calendarType;
    }

    /**
     * @function parseValue
     * @param string $data
     * @return bool
     */
    public function parseValue(string $data): bool
    {
        $formatter = new IntlDateFormatter(
            (string)$this->locale,
            $this->getDateType(),
            $this->getTimeType(),
            $this->getTimeZone(),
            $this->getCalendarType()
        );
        $pos = 0;
        $expectedPos = strlen($data);
        $result = $formatter->parse($data, $pos);

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

    abstract protected function setMsgContent(MsgInterface $msg): void;
}
