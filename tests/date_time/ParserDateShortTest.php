<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @noinspection PhpCSValidationInspection
 */

declare (strict_types=1);

namespace pvcTests\parser\date_time;

use DateTimeZone;
use IntlDateFormatter;
use pvc\parser\date_time\ParserDateShort;

class ParserDateShortTest extends ParserDateTimeTestMaster
{
    public function setUp(): void
    {
        parent::setUp();
        $tz = new DateTimeZone('America/New_York');
        $this->parser = new ParserDateShort($this->msg, $this->locale, $tz);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateShort::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ParserDateShort::class, $this->parser);
        self::assertEquals(IntlDateFormatter::SHORT, $this->parser->getDateType());
    }

    /**
     * testParseValue
     * @param string $localeString
     * @param string $tzString
     * @param string $input
     * @param mixed $dtStringAtom either false or DateTimeInterface::ATOM
     * @param string $comment
     * @dataProvider parserDateShortDataProvider
     * @covers \pvc\parser\date_time\ParserDateShort::parseValue
     * @covers \pvc\parser\date_time\ParserDateShort::setMsgContent
     *
     * This testing is really for illustration only - it demonstrates the behavior of the IntlDateFormatter's
     * parse method.  Since it is a stock object in PHP, there is no point in 'unit testing' it, but it can
     * be helpful to see test output to see how it behaves with various inputs.
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
            'basic example' => ['en_US', 'America/New_York', '5/9/96', '1996-05-09', 'failed to parse 5/9/96'],
            '4 digit year' => ['en_US', 'America/New_York', '5/9/1996', '1996-05-09', 'failed to parse 5/9/96'],
            'germany goes d/m/y' => ['de_DE', 'Europe/Berlin', '09-02-2014', '2014-02-09', 'failed to parse 09-02-2014'],
            'single digits' => ['de_DE', 'Europe/Berlin', '9-2-2014', '2014-02-09', 'failed to parse 9-2-2014'],
            'cannot do just a year' => ['de_DE', 'Europe/Berlin', '2014', false, 'wrongly parsed 2014'],
            /**
             * demonstrate the parser's tolerance of different separators.  These are three permitted separators
             * without resorting to a proprietary pattern
             */
            ['de_DE', 'Europe/Berlin', '10-02-14', '2014-02-10', 'failed to parse 10-02-14'],
            ['de_DE', 'Europe/Berlin', '10.02.14', '2014-02-10', 'failed to parse 10.02.14'],
            ['de_DE', 'Europe/Berlin', '10/2/14', '2014-02-10', 'failed to parse 10/2/14'],
            /**
             * and this one fails....
             */
            ['de_DE', 'Europe/Berlin', '10|02|14', false, 'wrongly parsed 10|02|14'],
        ];
    }
}
