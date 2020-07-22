<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\date_time\time;

use PHPUnit\Framework\TestCase;
use pvc\intl\err\UtcOffsetException;
use pvc\intl\Locale;
use pvc\intl\Time;
use pvc\intl\UtcOffset;
use pvc\parser\date_time\time\ParserTimeShort;


class ParserTimeShortTest extends TestCase
{
    protected Locale $locale;
    protected UtcOffset $utcOffset;

    public function setUp() : void
    {
        $this->locale = new locale();
        $this->utcOffset = new UtcOffset();
    }

    public function testSetGetPattern() : void
    {
        $parser = new ParserTimeShort($this->locale, $this->utcOffset);

        $pat = 'H:i';
        $parser->setPattern($pat);
        self::assertEquals($pat, $parser->getPattern());
    }

    /**
     * @function testParse
     * @param int $utcOffsetSeconds
     * @param string $timestring
     * @param bool $expectedResult
     * @param int|null $expectedTimestamp
     * @throws UtcOffsetException
     * @dataProvider timestringProvider
     */
    public function testParse(
        int $utcOffsetSeconds,
        string $timestring,
        bool $expectedResult,
        int $expectedTimestamp = null
    ): void {
        $this->utcOffset->setUtcOffsetSeconds($utcOffsetSeconds);
        $parser = new ParserTimeShort($this->locale, $this->utcOffset);

        $parser->setPattern('H:i');

        self::assertEquals($expectedResult, $parser->parse($timestring));

        if ($expectedResult) {
            $timeObject = $parser->getParsedValue();
            self::assertEquals($expectedTimestamp, $timeObject->getTimestamp());
        }
    }

    public function timestringProvider() : array
    {
        return [
                [0, '12:00', true, 43200],
                [-4 * 60 * 60, '17:00', true, 21 * 60 * 60],
                // parser cannot deal with trailing seconds
                [0, '18:22.34', false, null]
        ];
    }

    public function testParseNoPatternSet() : void
    {
        $this->locale->setLocale('de_DE');
        // 7200 seconds offset
        $this->utcOffset->setUtcOffsetHours(2);
        $parser = new ParserTimeShort($this->locale, $this->utcOffset);

        self::assertTrue($parser->parse('3:54'));
        // should be 1:54 in in the morning after the 2 hour adjust from Germany to UTC
        $time = new Time((1 * 60 * 60) + (54 * 60));

        self::assertEquals($time->getTimestamp(), $parser->getParsedValue()->getTimestamp());
    }
}
