<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvcTests\parser\date_time;

use IntlDateFormatter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\intl\TimeZoneInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\date_time\ParserDateTime;

class ParserDateTimeTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected LocaleInterface|MockObject $locale;
    protected TimeZoneInterface|MockObject $timeZone;

    protected IntlDateFormatter $formatter;

    protected ParserDateTime|MockObject $parserDateTime;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->locale = $this->createMock(LocaleInterface::class);
        $this->timeZone = $this->createMock(TimeZoneInterface::class);
        $this->formatter = $this->createMock(IntlDateFormatter::class);
        $args = [$this->msg, $this->locale, $this->timeZone, $this->formatter];
        $this->parserDateTime = $this->getMockForAbstractClass(ParserDateTime::class, $args);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\date_time\ParserDateTime::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ParserDateTime::class, $this->parserDateTime);
    }

    /**
     * testGetLocale
     * @covers \pvc\parser\date_time\ParserDateTime::getLocale()
     */
    public function testGetLocale(): void
    {
        self::assertEquals($this->locale, $this->parserDateTime->getLocale());
    }

    /**
     * testGetTimeZone
     * @covers \pvc\parser\date_time\ParserDateTime::getTimeZone()
     */
    public function testGetTimeZone(): void
    {
        self::assertEquals($this->timeZone, $this->parserDateTime->getTimeZone());
    }

    /**
     * testGetFormatter
     * @covers \pvc\parser\date_time\ParserDateTime::getIntlDateFormatter()
     */
    public function testGetFormatter(): void
    {
        self::assertEquals($this->formatter, $this->parserDateTime->getIntlDateFormatter());
    }
}
