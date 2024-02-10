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
use pvc\parser\date_time\ParserDateShort;

class ParserDateShortTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;

    protected TimeZoneInterface|MockObject $timeZone;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->timeZone = $this->createMock(TimeZoneInterface::class);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateShort::__construct
     */
    public function testConstruct(): void
    {
        $this->timeZone->method('__toString')->willReturn('America/New_York');
        $parser = new ParserDateShort($this->msg, $this->locale, $this->timeZone);
        self::assertInstanceOf(ParserDateShort::class, $parser);
    }

    /**
     * testParseValue
     * @param string $localeString
     * @param string $tzString
     * @param string $input
     * @param mixed $dtStringAtom either false or DateTimeInterface::ATOM
     * @param string $comment
     * @dataProvider parserDateShortDataProvider
     * @covers       \pvc\parser\date_time\ParserDateShort::parseValue
     * @covers       \pvc\parser\date_time\ParserDateShort::setMsgContent
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
        $parser = new ParserDateShort($this->msg, $this->locale, $this->timeZone);
        $result = ($parser->parse($input) ? $parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $result, $comment);
    }

    public function parserDateShortDataProvider(): array
    {
        return [
            ['en_US', 'America/New_York', '5/9/96', '1996-05-09', 'failed to parse 5/9/96'],
            /**
             * demonstrate the parser's tolerance of different separators.  These are three permitted separators
             * without resorting so a proprietary pattern
             */
            ['de_DE', 'Europe/Berlin', '10-02-14', '2014-02-10', 'failed to parse 10-02-14'],
            ['de_DE', 'Europe/Berlin', '10.02.14', '2014-02-10', 'failed to parse 10.02.14'],
            ['de_DE', 'Europe/Berlin', '10/02/14', '2014-02-10', 'failed to parse 10/02/14'],
            /**
             * and this one fails....
             */
            ['de_DE', 'Europe/Berlin', '10|02|14', false, 'wrongly parsed 10|02|14'],
        ];
    }
}
