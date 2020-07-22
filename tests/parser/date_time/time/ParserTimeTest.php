<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\date_time\time;

use Mockery;
use pvc\intl\err\UtcOffsetException;
use pvc\intl\UtcOffset;
use pvc\parser\date_time\time\ParserTime;
use PHPUnit\Framework\TestCase;

class ParserTimeTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    protected $parser;

    public function setUp(): void
    {
        $this->parser = Mockery::mock(ParserTime::class)->makePartial();
    }

    public function testSetGetUtcOffset() : void
    {
        $utcOffset = Mockery::mock(UtcOffset::class);
        $this->parser->setUtcOffset($utcOffset);
        self::assertEquals($utcOffset, $this->parser->getUtcOffset());
    }
}
