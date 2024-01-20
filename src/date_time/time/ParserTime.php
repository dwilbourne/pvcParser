<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\date_time\time;

use pvc\intl\Locale;
use pvc\intl\UtcOffset;
use pvc\parser\date_time\ParserDateTime;
use pvc\parser\ParserInterface;

/**
 * Parent class of the time parser(s)
 *
 * It took me a minute to understand this, but as far as calculating time is concerned,
 * a timezone is not useful unless there is a date involved as well because of Daylight Savings Time.
 * To pick a specific example, the east coast of the US is either 4 or 5 hours behind GMT, depending
 * on the date.  Since this class specifcally is eliminating the date component of a DateTime object
 * (or Carbon or IntlDateFormatter etc), the timezone give you only a guess as to the offset from
 * UTC. Therefore in this class you must manually specify the offset from UTC / GMT.
 *
 * Moreover, if you are looking to store a time that varies according to DST,
 * then you should create a time on a specific day of the year using a Carbon
 * object.  Just know that the cutoffs for when DST starts and stops have changed over the years
 * and so your choice of day in a particular year might yield a different time in
 * a different year because DST started / ended on different dates.
 *
 * Class ParserTime
 */
abstract class ParserTime extends ParserDateTime implements ParserInterface
{


    protected UtcOffset $utcOffset;

    public function __construct(Locale $locale, UtcOffset $utcOffset)
    {
        parent::__construct($locale);
        $this->setUtcOffset($utcOffset);
    }

    /**
     * @return UtcOffset
     */
    public function getUtcOffset(): UtcOffset
    {
        return $this->utcOffset;
    }

    /**
     * @param UtcOffset $utcOffset
     */
    public function setUtcOffset(UtcOffset $utcOffset): void
    {
        $this->utcOffset = $utcOffset;
    }
}
