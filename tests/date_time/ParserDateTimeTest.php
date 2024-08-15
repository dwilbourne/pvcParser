<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvcTests\parser\date_time;

use DateTime;
use DateTimeZone;
use IntlDateFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\date_time\ParserDateTime;
use pvc\parser\err\InvalidDateTimeTypeException;

class ParserDateTimeTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;

    protected DateTimeZone $timeZone;

    protected ParserDateTime $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->timeZone = new DateTimeZone('America/New_York');
        $args = [$this->msg, $this->locale, $this->timeZone];
        $this->parser = $this->getMockBuilder(ParserDateTime::class)
                             ->setConstructorArgs($args)
                             ->getMockForAbstractClass();
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateTime::__construct
     * @covers \pvc\parser\date_time\ParserDateTime::setLocale
     * @covers \pvc\parser\date_time\ParserDateTime::getLocale
     * @covers \pvc\parser\date_time\ParserDateTime::setTimeZone
     * @covers \pvc\parser\date_time\ParserDateTime::getTimeZone
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ParserDateTime::class, $this->parser);
        self::assertEquals($this->locale, $this->parser->getLocale());
        self::assertEquals($this->timeZone, $this->parser->getTimeZone());
    }

    /**
     * testSetGetDateType
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::setDateType
     * @covers \pvc\parser\date_time\ParserDateTime::getDateType
     */
    public function testSetGetDateType(): void
    {
        $dateType = IntlDateFormatter::RELATIVE_SHORT;
        $this->parser->setDateType($dateType);
        self::assertEquals($dateType, $this->parser->getDateType());
    }

    /**
     * testSetDateTypeThrowsExceptionWithBadArg
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::setDateType
     */
    public function testSetDateTypeThrowsExceptionWithBadArg(): void
    {
        $dateType = 999;
        self::expectException(InvalidDateTimeTypeException::class);
        $this->parser->setDateType($dateType);
    }

    /**
     * testSetGetTimeType
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::setTimeType
     * @covers \pvc\parser\date_time\ParserDateTime::getTimeType
     */
    public function testSetGetTimeType(): void
    {
        $timeType = IntlDateFormatter::FULL;
        $this->parser->setTimeType($timeType);
        self::assertEquals($timeType, $this->parser->getTimeType());
    }

    /**
     * testSetTimeTypeThrowsExceptionWithBadArg
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::setTimeType
     */
    public function testSetTimeTypeThrowsExceptionWithBadArg(): void
    {
        /**
         * does not exist on time types, only on date types
         */
        $timeType = IntlDateFormatter::RELATIVE_FULL;
        self::expectException(InvalidDateTimeTypeException::class);
        $this->parser->setTimeType($timeType);
    }

    /**
     * testSetgetCalendarType
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::setCalendarType
     * @covers \pvc\parser\date_time\ParserDateTime::getCalendarType
     */
    public function testSetgetCalendarType(): void
    {
        $calendarType = IntlDateFormatter::TRADITIONAL;
        $this->parser->setCalendarType($calendarType);
        self::assertEquals($calendarType, $this->parser->getCalendarType());
    }

    /**
     * testSetCalendarTypeThrowsExceptionWithBadArg
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::setCalendarType
     */
    public function testSetCalendarTypeThrowsExceptionWithBadArg(): void
    {
        $calendarType = 999;
        self::expectException(InvalidDateTimeTypeException::class);
        $this->parser->setCalendarType($calendarType);
    }

    /**
     * test that
     *      - if formatter does not parse the entire string or
     *      - if the formatter's parse method returns false
     *      - then
     *      - the parser's parse method returns false.
     *
     * test that
     *      - if the formatter's parse method succeeds and
     *      - if the formatter's parse method reached the end of the string
     *      - then
     *      - the parsedValue attribute is be set and
     *      - the parser's parse method returns false
     */

    /**
     * testParseValueSetsValueAndReturnsTrueUponSuccess
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::parseValue
     */
    public function testParseValueSetsValueAndReturnsTrueUponSuccess(): void
    {
        $localeString = 'en_US';
        $this->locale->method('__toString')->willReturn($localeString);

        $testinput = '4/15/2020';
        $this->parser->setDateType(IntlDateFormatter::SHORT);
        self::assertTrue($this->parser->parseValue($testinput));

        $dt = new DateTime('2020-04-15', $this->timeZone);
        self::assertEquals($dt->getTimestamp(), $this->parser->getParsedValue());
    }

    /**
     * testParseValueReturnsFalseWhenParserDoesNotGetToEndOfString
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::parseValue
     */
    public function testParseValueReturnsFalseWhenParserDoesNotGetToEndOfString()
    {
        $localeString = 'en_US';
        $this->locale->method('__toString')->willReturn($localeString);

        $testinput = '4/15/2020foobar';
        $this->parser->setDateType(IntlDateFormatter::SHORT);
        self::assertFalse($this->parser->parseValue($testinput));
    }

    /**
     * testParseValueReturnsFalseWhenFormatterParserReturnsFalse
     * @throws InvalidDateTimeTypeException
     * @covers \pvc\parser\date_time\ParserDateTime::parseValue
     */
    public function testParseValueReturnsFalseWhenFormatterParserReturnsFalse(): void
    {
        $localeString = 'en_US';
        $this->locale->method('__toString')->willReturn($localeString);

        $testInput = '0';
        $dateType = IntlDateFormatter::SHORT;

        $frmtr = new IntlDateFormatter(
            $this->locale,
            $dateType,
            IntlDateFormatter::NONE,
            $this->timeZone,
            IntlDateFormatter::GREGORIAN
        );
        /**
         * demonstrate that the parse method for IntlDateFormatter returns false for this input
         */
        self::assertFalse($frmtr->parse($testInput));

        $this->parser->setDateType($dateType);
        self::assertFalse($this->parser->parseValue($testInput));
    }
}
