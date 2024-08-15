<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\date_time;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\interfaces\parser\ParserInterface;
use pvc\parser\date_time\ParserDateTime;

/**
 * Class ParserDateTimeTestMaster
 */
class ParserDateTimeTestMaster extends TestCase
{
    protected ParserDateTime $parser;

    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
    }

    /**
     * testParseValue
     * @param ParserInterface $parser
     * @param string $localeString
     * @param string $tzString
     * @param string $input
     * @param mixed $dtStringAtom either false or DateTimeInterface::ATOM
     * @param string $comment
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
        $this->locale->method('__toString')->willReturn($localeString);
        $tz = new DateTimeZone($tzString);
        if (!$dtStringAtom) {
            $this->msg->expects($this->once())->method('setContent');
            $expectedResult = false;
        } else {
            /**
             * create the timezone and convert the string to a timestamp
             */
            $dt = new DateTimeImmutable((string)$dtStringAtom, $tz);
            $expectedResult = $dt->getTimestamp();
        }
        $this->parser->setTimeZone($tz);
        $result = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $result, $comment);
    }
}
