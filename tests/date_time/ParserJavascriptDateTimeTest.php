<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @noinspection PhpCSValidationInspection
 */

declare (strict_types=1);

namespace pvcTests\parser\date_time;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\date_time\ParserDateShort;
use pvc\parser\date_time\ParserJavascriptDateTime;

class ParserJavascriptDateTimeTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected ParserJavascriptDateTime $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new ParserJavascriptDateTime($this->msg);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateShort::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ParserJavascriptDateTime::class, $this->parser);
    }

    public function parserJavascriptDateTimeDataProvider(): array
    {
        return [

            'basic test' => ['2012-07-15T13:54:56Z-05:00', '2012-07-15T13:54:56-05:00', '-0500', 'failed to parse 2012-07-15T13:54:56Z-05:00'],
            'also parses tz abbreviation' => ['2012-07-15T13:54:56ZEDT', '2012-07-15T13:54:56-04:00', '-0400', 'failed to parse 2012-07-15T13:54:56ZEDT'],
            'also parses tz id' => ['2012-07-15T13:54:56ZAmerica/New_York', '2012-07-15T13:54:56-04:00', '-0400', 'failed to parse 2012-07-15T13:54:56ZEDT'],

            'no timezone' => ['2012-07-15T13:54:56', '2012-07-15T13:54:56', '', 'failed to parse 2012-07-15T13:54:56'],
            'no time' => ['2012-07-15', '2012-07-15T00:00:00', '', 'failed to parse 2012-07-15'],
            'partial date' => ['2012-07', '2012-07-01T00:00:00', '', 'failed to parse 2012-07'],


            /*
             * '2012' as input is interpreted as a time on the current date.
             * 'year only' => ['2012', ?? depends what day you run the test! ??, '', 'parses 2012 as a time'],
             */
        ];
    }

    public function testBadTimeZone(): void
    {
        $this->testParseValue('2012-07-15T13:54:56Zfoo', false, 'foo', 'incorrectly parsed 2012-07-15T13:54:56Zfoo');
    }

    /**
     * testParseValue
     * @param string $input
     * @param mixed $dtStringAtom either false or DateTimeInterface::ATOM
     * @param string $tzOffset
     * @param string $comment
     * @dataProvider parserJavascriptDateTimeDataProvider
     * @covers       \pvc\parser\date_time\ParserJavascriptDateTime::parseValue
     * @covers       \pvc\parser\date_time\ParserDateShort::setMsgContent
     */
    public function testParseValue(
        string $input,
        mixed $dtStringAtom,
        string $tzOffset,
        string $comment
    ): void {
        if (!$dtStringAtom) {
            $this->msg->expects($this->once())->method('setContent');
            $expectedResult = false;
        } else {
            /**
             * convert the string to a timestamp.  If $tzOffset is null (e.g. there is no timezone info in the
             * string), assume the local timezone.
             */
            $tzOffset = empty($tzOffset) ? date_default_timezone_get() : $tzOffset;
            $tz = new DateTimeZone($tzOffset);
            $dt = new DateTimeImmutable($dtStringAtom, $tz);
            $expectedResult = $dt->getTimestamp();
        }
        $result = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $result, $comment);
    }
}
