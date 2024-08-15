<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\parser\date_time;

use DateTimeZone;
use IntlDateFormatter;
use pvc\parser\date_time\ParserDateShortTimeShort;

class ParserDateShortTimeShortTest extends ParserDateTimeTestMaster
{
    public function setUp(): void
    {
        parent::setUp();
        $tz = new DateTimeZone('America/New_York');
        $this->parser = new ParserDateShortTimeShort($this->msg, $this->locale, $tz);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateShortTimeShort::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ParserDateShortTimeShort::class, $this->parser);
        self::assertEquals(IntlDateFormatter::SHORT, $this->parser->getDateType());
        self::assertEquals(IntlDateFormatter::SHORT, $this->parser->getTimeType());
    }

    /**
     * testParseValue
     * @param string $localeString
     * @param string $tzString
     * @param string $input
     * @param mixed $dtStringAtom either false or DateTimeInterface::ATOM
     * @param string $comment
     * @dataProvider parserDateShortDataProvider
     * @covers \pvc\parser\date_time\ParserDateShortTimeShort::parseValue
     * @covers \pvc\parser\date_time\ParserDateShortTimeShort::setMsgContent
     */
    public function testParseValue(
        string $localeString,
        string $tzString,
        string $input,
        mixed $dtStringAtom,
        string $comment
    ): void {
        parent::testParseValue(
            $localeString,
            $tzString,
            $input,
            $dtStringAtom,
            $comment
        );
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
             * others will fail
             */
            ['de_DE', 'Europe/Berlin', '10-02-14, 17*32', false, 'wrongly parsed 10.02.14, 17*32'],
        ];
    }
}
