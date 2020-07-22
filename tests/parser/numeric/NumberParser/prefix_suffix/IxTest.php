<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\prefix_suffix;

use Mockery;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException;
use pvc\parser\numeric\NumberParser\prefix_suffix\Prefix;
use pvc\parser\numeric\NumberParser\prefix_suffix\Suffix;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class IxTest extends TestCase
{
    protected Prefix $prefix;
    protected Suffix $suffix;

    /** @phpstan-ignore-next-line */
    protected $symbolA;

    /** @phpstan-ignore-next-line */
    protected $symbolB;

    public function setUp(): void
    {
        $this->prefix = new Prefix();
        $this->suffix = new Suffix();
    }

    public function testPrefixAddGetSymbolGetIx() : void
    {
        $symbolA = Mockery::mock(Symbol::class);
        $symbolA->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolA->shouldReceive('getPatternChar')->withNoArgs()->andReturn('A');
        $symbolB = Mockery::mock(Symbol::class);
        $symbolB->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolB->shouldReceive('getPatternChar')->withNoArgs()->andReturn('B');

        self::assertEquals(0, count($this->prefix));
        self::assertTrue($this->prefix->isEmpty());
        $this->prefix->addSymbol($symbolA);
        self::assertEquals(1, count($this->prefix));
        $this->prefix->addSymbol($symbolB);
        self::assertEquals(2, count($this->prefix));

        $expectedResult = [$symbolA, $symbolB];
        self::assertEquals($expectedResult, $this->prefix->getSymbols());

        $expectedResult = 'AB';
        self::assertEquals($expectedResult, $this->prefix->getIx());
    }

    public function testAddSymbolException() : void
    {
        $symbolA = Mockery::mock(Symbol::class);
        $symbolA->shouldReceive('isPositional')->withNoArgs()->andReturn(false);
        $symbolA->shouldReceive('getPatternChar')->withNoArgs()->andReturn('A');
        self::expectException(InvalidSymbolException::class);
        $this->prefix->addSymbol($symbolA);
    }

    public function testAddSymbolsFromString() : void
    {
        // mock the hard dependency on Symbol
        $hardMock = Mockery::mock('overload:' . Symbol::class);
        $hardMock->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $hardMock->shouldReceive('setSymbolFromChar')->with('A');
        $hardMock->shouldReceive('setSymbolFromChar')->with('B');

        $this->prefix->addSymbolsFromString('AB');
        self::assertEquals(2, count($this->prefix));
    }

    /**
     * @function testSuffixAddGetSymbolGetIx
     * note that the order of the characters / symbols coming back is reversed from prefix
     */
    public function testSuffixAddGetSymbolGetIx() : void
    {
        $symbolA = Mockery::mock(Symbol::class);
        $symbolA->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolA->shouldReceive('getPatternChar')->withNoArgs()->andReturn('A');
        $symbolB = Mockery::mock(Symbol::class);
        $symbolB->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolB->shouldReceive('getPatternChar')->withNoArgs()->andReturn('B');

        self::assertEquals(0, count($this->suffix));
        self::assertTrue($this->suffix->isEmpty());
        $this->suffix->addSymbol($symbolA);
        self::assertEquals(1, count($this->suffix));
        $this->suffix->addSymbol($symbolB);
        self::assertEquals(2, count($this->suffix));

        $expectedResult = [$symbolA, $symbolB];
        self::assertEquals($expectedResult, $this->suffix->getSymbols());

        $expectedResult = 'AB';
        self::assertEquals($expectedResult, $this->suffix->getIx());
    }

    public function testPrefixMerge() : void
    {
        $symbolA = Mockery::mock(Symbol::class);
        $symbolA->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolA->shouldReceive('getPatternChar')->withNoArgs()->andReturn('A');
        $symbolB = Mockery::mock(Symbol::class);
        $symbolB->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolB->shouldReceive('getPatternChar')->withNoArgs()->andReturn('B');

        $symbolC = Mockery::mock(Symbol::class);
        $symbolC->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolC->shouldReceive('getPatternChar')->withNoArgs()->andReturn('C');
        $symbolD = Mockery::mock(Symbol::class);
        $symbolD->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolD->shouldReceive('getPatternChar')->withNoArgs()->andReturn('D');

        $prefix1 = new Prefix();
        $prefix1->addSymbolArray([$symbolA, $symbolB]);

        $prefix2 = new Prefix();
        $prefix2->addSymbolArray([$symbolC, $symbolD]);

        $prefix1->merge($prefix2);
        $expectedResult = 'ABCD';
        self::assertEquals($expectedResult, $prefix1->getIx());
    }


    public function testSuffixMerge() : void
    {
        $symbolA = Mockery::mock(Symbol::class);
        $symbolA->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolA->shouldReceive('getPatternChar')->withNoArgs()->andReturn('A');
        $symbolB = Mockery::mock(Symbol::class);
        $symbolB->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolB->shouldReceive('getPatternChar')->withNoArgs()->andReturn('B');

        $symbolC = Mockery::mock(Symbol::class);
        $symbolC->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolC->shouldReceive('getPatternChar')->withNoArgs()->andReturn('C');
        $symbolD = Mockery::mock(Symbol::class);
        $symbolD->shouldReceive('isPositional')->withNoArgs()->andReturn(true);
        $symbolD->shouldReceive('getPatternChar')->withNoArgs()->andReturn('D');

        $suffix1 = new Suffix();
        $suffix1->addSymbolArray([$symbolA, $symbolB]);
        $expectedResult = 'AB';
        self::assertEquals($expectedResult, $suffix1->getIx());

        $suffix2 = new Suffix();
        $suffix2->addSymbolArray([$symbolC, $symbolD]);
        $expectedResult = 'CD';
        self::assertEquals($expectedResult, $suffix2->getIx());

        $suffix1->merge($suffix2);
        $expectedResult = 'CDAB';
        self::assertEquals($expectedResult, $suffix1->getIx());
    }
}
