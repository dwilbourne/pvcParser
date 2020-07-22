<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\date_time\time;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use InvalidArgumentException;
use pvc\intl\DateTimePattern;
use pvc\intl\Time;
use pvc\parser\ParserInterface;


/**
 *
 * The format for parsing a time is typically guessed by the IntlDateFormatter object and then that format is adapted
 * to something that Carbon can use.  In this class 'the format' does NOT including the formatting character
 * that adjusts the time to UTC.  That adjustment occurs on the fly in the parsing routine.  So, for example,
 * you can manually set the pattern to be 'H:i'.  You should NOT add the timezone offset specifier, e.g. 'H:i O+0200'.
 * There is a separate UtcOffsetSeconds attribute in the parent class which must be set separately.
 *
 * Class ParserTime
 */
class ParserTimeShort extends ParserTime implements ParserInterface
{
    /**
     * @var string
     */
    protected string $pattern;

    /**
     * @function setPattern
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @function getPattern
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @function parse
     * @param string $localTimeString
     * @return bool
     */
    public function parse(string $localTimeString): bool
    {
        if (!isset($this->pattern)) {
            $this->setPattern(DateTimePattern::getPatternTimeShort($this->getLocale()));
        }

        try {
            // create a UTC timezone
            $tz = CarbonTimeZone::create();

            /* phpstan wants a phpdatetime object, and phpstan sees a carbon timezone */
            /** @phpstan-ignore-next-line */
            $carbon = Carbon::createFromFormat($this->pattern, $localTimeString, $tz);

            /* adjust local time to GMT */
            $seconds = ($carbon->hour * 3600) + ($carbon->minute * 60) - $this->getUtcOffset()->getUtcOffsetSeconds();
            $this->setParsedValue(new time($seconds));
            $this->setErrmsg(null);
            return true;
        } catch (InvalidArgumentException $e) {
            // do nothing
        }

        $msg = new InvalidTimeShortMsg($localTimeString);
        $this->setErrmsg($msg);
        return false;
    }
}
