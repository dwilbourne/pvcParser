<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\date_time\date;

use Mockery;
use pvc\intl\TimeZone;
use pvc\parser\date_time\date\ParserDate;
use PHPUnit\Framework\TestCase;

class ParserDateTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    protected $parser;

    public function setUp(): void
    {
        $this->parser = Mockery::mock(ParserDate::class)->makePartial();
    }

    public function testSetGetTz() : void
    {
        $tz = new timeZone('Europe/Paris');
        $this->parser->setTimeZone($tz);
        self::assertEquals($tz, $this->parser->getTimeZone());
    }
}
