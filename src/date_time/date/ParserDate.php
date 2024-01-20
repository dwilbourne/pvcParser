<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\date_time\date;

use pvc\intl\Locale;
use pvc\intl\TimeZone;
use pvc\parser\date_time\ParserDateTime;

/**
 * Class ParserDate
 */
abstract class ParserDate extends ParserDateTime
{
    /**
     * @var TimeZone
     */
    protected TimeZone $timeZone;

    /**
     * ParserDate constructor.
     * @param Locale $locale
     * @param TimeZone $timeZone
     */
    public function __construct(Locale $locale, TimeZone $timeZone)
    {
        parent::__construct($locale);
        $this->setTimeZone($timeZone);
    }

    /**
     * @function getTimeZone
     * @return TimeZone
     */
    public function getTimeZone(): TimeZone
    {
        return $this->timeZone;
    }

    /**
     * @function setTimeZone
     * @param TimeZone $tz
     */
    public function setTimeZone(TimeZone $tz) : void
    {
        $this->timeZone = $tz;
    }
}
