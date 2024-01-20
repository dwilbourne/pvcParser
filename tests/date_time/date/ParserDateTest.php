<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvcTests\parser\date_time\date;

use Mockery;
use PHPUnit\Framework\TestCase;
use pvc\intl\TimeZone;
use pvc\parser\date_time\date\ParserDate;

class ParserDateTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    protected $parser;

    public function setUp(): void
    {
        $this->parser = Mockery::mock(ParserDate::class)->makePartial();
    }

    public function testSetGetTz(): void
    {
        $tz = new timeZone('Europe/Paris');
        $this->parser->setTimeZone($tz);
        self::assertEquals($tz, $this->parser->getTimeZone());
    }
}
