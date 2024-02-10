<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\parser\date_time;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\intl\TimeZoneInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\date_time\ParserDateShortTimeShort;

class ParserDateShortTimeShortTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;
    protected TimeZoneInterface|MockObject $timeZone;

    protected string $currentTimeZone;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->timeZone = $this->createMock(TimeZoneInterface::class);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateShortTimeShort::__construct
     */
    public function testConstruct(): void
    {
        $this->timeZone->method('__toString')->willReturn('America/New_York');
        $parser = new ParserDateShortTimeShort($this->msg, $this->locale, $this->timeZone);
        self::assertInstanceOf(ParserDateShortTimeShort::class, $parser);
    }


    /**
     * testParseValue
     * @param string $localeString
     * @param string $tzString
     * @param string $input
     * @param mixed $dtStringAtom either false of DateTimeInterface::ATOM
     * @param string $comment
     * @dataProvider parserDateShortDataProvider
     * @covers       \pvc\parser\date_time\ParserDateShortTimeShort::parseValue
     * @covers       \pvc\parser\date_time\ParserDateShortTimeShort::setMsgContent
     */
    public function testParseValue(
        string $localeString,
        string $tzString,
        string $input,
        mixed $dtStringAtom,
        string $comment
    ): void {
        $this->locale->method('__toString')->willReturn($localeString);
        $this->timeZone->method('__toString')->willReturn($tzString);
        if ((bool)$dtStringAtom == false) {
            $this->msg->expects($this->once())->method('setContent');
            $expectedResult = false;
        } else {
            /**
             * convert the string to a timestamp
             */
            $tz = new DateTimeZone($tzString);
            $dt = new DateTimeImmutable((string)$dtStringAtom, $tz);
            $expectedResult = $dt->getTimestamp();
        }
        $parser = new ParserDateShortTimeShort($this->msg, $this->locale, $this->timeZone);
        $result = ($parser->parse($input) ? $parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $result, $comment);
    }

    public function parserDateShortDataProvider(): array
    {
        return [
            ['en_US', 'America/New_York', '5/9/96 11:14 pm', '1996-05-09T23:14', 'failed to parse 5/9/96 11:14 pm'],
            /**
             * the pattern does not permit 'military time'
             */
            ['en_US', 'America/New_York', '5/9/96 23:14', false, 'failed to parse 5/9/96 23:14 pm'],

            /**
             * demonstrate different separators - the following three are permitted
             */
            ['de_DE', 'Europe/Berlin', '10-02-14, 17:32', '2014-02-10T17:32', 'failed to parse 10.02.14, 17:32'],
            ['de_DE', 'Europe/Berlin', '10-02-14, 17-32', '2014-02-10T17:32', 'failed to parse 10.02.14, 17-32'],
            ['de_DE', 'Europe/Berlin', '10-02-14, 17.32', '2014-02-10T17:32', 'failed to parse 10.02.14, 17.32'],
            /**
             * others will fails
             */
            ['de_DE', 'Europe/Berlin', '10-02-14, 17*32', false, 'wrongly parsed 10.02.14, 17*32'],
        ];
    }
}
