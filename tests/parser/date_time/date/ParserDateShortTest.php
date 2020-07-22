<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\date_time\date;

use DateTime;
use DateTimeZone;
use pvc\err\throwable\exception\pvc_exceptions\InvalidValueException;
use pvc\intl\Locale;
use pvc\intl\TimeZone;
use pvc\parser\date_time\date\ParserDateShort;
use PHPUnit\Framework\TestCase;

class ParserDateShortTest extends TestCase
{
    protected Locale $locale;
    protected TimeZone $tz;
    protected ParserDateShort $parser;

    public function setUp(): void
    {
        $this->locale = new locale('en_US');
        $this->tz = new TimeZone('America/New_York');
        $this->parser = new ParserDateShort($this->locale, $this->tz);
    }

    public function testAddGetSeparators() : void
    {
        self::assertEquals(0, count($this->parser->getSeparators()));

        $sep = '*';
        $this->parser->addSeparator($sep);
        $separators = $this->parser->getSeparators();
        self::assertEquals(1, count($this->parser->getSeparators()));
        self::assertEquals($sep, $separators[0]);

        $sep = '@';
        $this->parser->addSeparator($sep);
        $separators = $this->parser->getSeparators();
        self::assertEquals(2, count($this->parser->getSeparators()));
        self::assertEquals($sep, $separators[1]);
    }

    public function testSetGetDatePartsOrder() : void
    {
        $acceptableValues = ['ymd', 'dmy', 'mdy'];
        foreach ($acceptableValues as $dpo) {
            $this->parser->setDatePartsOrder($dpo);
            self::assertEquals($dpo, $this->parser->getDatePartsOrder());
        }
    }

    public function testSetDatePartsOrderException() : void
    {
        self::expectException(InvalidValueException::class);
        $this->parser->setDatePartsOrder('foo');
    }

    public function testSetGetInterpretYearsLiterally() : void
    {
        $value = true;
        $this->parser->setInterpretYearsLiterally($value);
        self::assertEquals($value, $this->parser->getInterpretYearsLiterally());
    }

    /**
     * @function testParse1
     * @param string $dateStr
     * @param int|bool $expectedResult
     * @throws InvalidValueException
     * @dataProvider dateDataProvider
     */
    public function testParse1(string $dateStr, $expectedResult) : void
    {
        self::assertEquals($expectedResult, $this->parser->parse($dateStr));
    }

    public function dateDataProvider() : array
    {
        $dt = new DateTime();
        $tz = new DateTimeZone('America/New_York');
        $dt->setTimezone($tz);
        $dt->setDate(2020, 5, 10);
        $ts = $dt->getTimestamp();

        return [
            '5/10/2020' => ['5/10/2020', $ts],
            '5.10.2020' => ['5.10.2020', $ts],
            '5-10-2020' => ['5-10-2020', $ts],
            '5@10@2020' => ['5@10@2020', false]
        ];
    }

    // illustrate that there are no hours / minutes / seconds associated with the parsed date, e.g.
    // the timestamp created is at midnight on the date specified (in the timezone specified)

    public function testParse2() : void
    {
        $dateStringToParse = '5/10/2020';
        // create a DateTime object with no hours / minutes / seconds
        $dt = new DateTime('2020-05-10');
        $expectedResult = $dt->getTimestamp();
        self::assertEquals($expectedResult, $this->parser->parse($dateStringToParse));
    }

    // unlike the international date formatter, Carbon kicks out an error if the end of the string
    // has extra characters on it.
    public function testParse3() : void
    {
        $dateStringToParse = '5/10/2020 x2 abn';
        self::assertFalse($this->parser->parse($dateStringToParse));
    }
}
