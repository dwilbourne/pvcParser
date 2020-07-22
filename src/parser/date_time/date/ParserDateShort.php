<?php declare(strict_types = 1);

namespace pvc\parser\date_time\date;

use Carbon\Carbon;
use InvalidArgumentException;
use pvc\err\throwable\exception\pvc_exceptions\InvalidValueException;
use pvc\err\throwable\exception\pvc_exceptions\InvalidValueMsg;
use pvc\intl\DateTimePattern;
use pvc\intl\Locale;
use pvc\intl\TimeZone;
use pvc\parser\ParserInterface;

/**
 * This parser creates a carbon object from a string which is a 'short date'.
 *
 * A short date consists of 3 segments of numbers (day, month and year).  Days and months can be 1 or two
 * digits.  Years are any positive integer subject to the limitations of the IntlDateFormatter class and the
 * documentation below.
 *
 * There are several separate considerations for parsing (and formatting) short dates:
 *
 * 1) the locale will predict the convention that is used for ordering the day / month / year parts.  e.g. US dates
 * go month / day / year whereas French dates go day / month / year.  This convention is stored in the
 * $datePartsOrder attribute and may be set manually.
 *
 * 2) The timezone to which the date belongs. June 5 2002 in America/New_York is not the same date as June 5 2002 in
 * Paris. Since there is (generally) a 6 hour time difference, those dates actually have different values when stored.
 * This is true despite the fact that there is no 'time' component in this class - all 'dates' are truncated to be
 * midnight.  Just remember that it is midnight in a particular timezone.
 *
 * 3) what separator(s) are legal in delineating the segments of the date.
 *
 * 4) whether 2 digit years should be interpreted as being in the 20th / 21st centuries or should be interpreted
 * literally.  For the sake of simplicity, the default in this parser is that years are interpreted literally.  So,
 * for example, in the USA 5/20/25 is May20th, year 25AD (CE/Common Era).  If you want to change that behavior, set
 * the $interpretYearsLiterally attribute to false;
 *
 * It is initially tempting to try and correlate the timezone and the locale.  But a moment's thought leads to a
 * use case like a French businessperson traveling in New York and inputting a date or a time.  The language
 * preference (and ordering of the date segments) might be french but the timezone would be New York.
 *
 *
 * Class ParserDateShort
 *
 */
class ParserDateShort extends ParserDate implements ParserInterface
{
    /**
     * @var string
     */
    protected string $datePartsOrder;

    /**
     * @var bool
     */
    protected bool $interpretYearsLiterally;

    /**
     * @var array
     */
    protected array $separators = [];

    /**
     * @var string[]
     */
    private array $defaultSeparators = ['/', '.', '-'];

    /**
     * ParserDateShort constructor.
     * @param Locale $locale
     * @param TimeZone $timeZone
     */
    public function __construct(Locale $locale, TimeZone $timeZone)
    {
        parent::__construct($locale, $timeZone);
        $this->setInterpretYearsLiterally(true);
    }

    /**
     * @function addSeparator
     * @param string $separator
     */
    public function addSeparator(string $separator) : void
    {
        $this->separators[] = $separator;
    }

    /**
     * @function getSeparators
     * @return array
     */
    public function getSeparators(): array
    {
        return $this->separators;
    }

    /**
     * @function setDatePartsOrder
     * @param string $datePartsOrder
     * @throws InvalidValueException
     */
    public function setDatePartsOrder(string $datePartsOrder) : void
    {
        $acceptableValues = ['ymd', 'dmy', 'mdy'];
        $orderLowerCase = strtolower($datePartsOrder);
        if (!in_array($orderLowerCase, $acceptableValues)) {
            $msg = new InvalidValueMsg(
                'datePartsOrderString',
                $datePartsOrder,
                "Acceptable values are 'dmy', 'ymd', 'mdy'."
            );
            throw new InvalidValueException($msg);
        }
        $this->datePartsOrder = $orderLowerCase;
    }

    /**
     * @function getDatePartsOrder
     * @return string
     */
    public function getDatePartsOrder(): string
    {
        return $this->datePartsOrder;
    }

    /**
     * @function getInterpretYearsLiterally
     * @return bool
     */
    public function getInterpretYearsLiterally(): bool
    {
        return $this->interpretYearsLiterally;
    }

    /**
     * @function setInterpretYearsLiterally
     * @param bool $interpretYearsLiterally
     */
    public function setInterpretYearsLiterally(bool $interpretYearsLiterally): void
    {
        $this->interpretYearsLiterally = $interpretYearsLiterally;
    }

    /**
     * @function createPattern
     * @param string $separator
     * @return string
     */
    public function createPattern(string $separator): string
    {
        // pattern is based on Carbon's pattern constants
        $yearsPattern = ($this->getInterpretYearsLiterally() ? 'Y' : 'y');
        $monthsPattern = 'm';
        $daysPattern = 'd';
        $patternArray = ['y' => $yearsPattern, 'm' => $monthsPattern, 'd' => $daysPattern];

        $z = '';
        $z .= $patternArray[$this->datePartsOrder[0]] . $separator;
        $z .= $patternArray[$this->datePartsOrder[1]] . $separator;
        $z .= $patternArray[$this->datePartsOrder[2]];
        return $z;
    }

    /**
     * @function parse
     * @param string $dateStr
     * @return bool
     * @throws InvalidValueException
     */
    public function parse(string $dateStr): bool
    {
        if (!isset($this->datePartsOrder)) {
            $this->setDatePartsOrder(DateTimePattern::getPatternDatePartsOrder($this->getLocale()));
        }
        if (empty($this->getSeparators())) {
            $this->separators = $this->defaultSeparators;
        }

        foreach ($this->separators as $separator) {
            $pattern = $this->createPattern($separator);
            try {
                $result = Carbon::createFromFormat($pattern, $dateStr, $this->getTimeZone());
                $this->setParsedValue($result);
                $this->setErrmsg(null);
                return true;
            } catch (InvalidArgumentException $e) {
                // do nothing
            }
        }

        $msg = new InvalidDateShortMsg($dateStr);
        $this->setErrmsg($msg);
        return false;
    }
}
