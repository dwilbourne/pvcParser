<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\date_time;

use Mockery;
use pvc\intl\Locale;
use pvc\intl\TimeZone;
use pvc\parser\date_time\ParserDateTime;
use PHPUnit\Framework\TestCase;

class ParserDateTimeTest extends TestCase
{
    protected Locale $locale;
    protected TimeZone $tz;
    /** @phpstan-ignore-next-line */
    protected $parser;

    public function setUp(): void
    {
        $this->locale = new locale('en_US');
        $this->tz = new timeZone('America/New_York');
        $this->parser = Mockery::mock(ParserDateTime::class)->makePartial();
    }

    public function testSetGetLocale() : void
    {
        $locale = new locale('de_DE');
        $this->parser->setLocale($locale);
        self::assertEquals($locale, $this->parser->getLocale());
    }
}
