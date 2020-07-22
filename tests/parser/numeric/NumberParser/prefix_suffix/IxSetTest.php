<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\prefix_suffix;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\prefix_suffix\IxSet;
use pvc\parser\numeric\NumberParser\prefix_suffix\Prefix;
use pvc\parser\numeric\NumberParser\prefix_suffix\Suffix;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

class IxSetTest extends TestCase
{
    protected IxSet $ixSet;

    public function setUp(): void
    {
        $this->ixSet = new IxSet();
    }

    public function testSetFormatException() : void
    {
        $this->expectException('pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidFormatException');
        $this->ixSet->setFormat('', '', '', '');
    }

    public function testSetFormatGetIx() : void
    {
        $this->ixSet->setFormat('+', 'foo', '-', 'bar');
        $ixArray = $this->ixSet->getIx();

        $expected = '+';
        $ix = $ixArray[0];
        self::assertEquals($this->ixSet->getPositivePrefix(), $ixArray[0]);
        self::assertEquals($expected, $ix->getIx());

        $expected = 'foo';
        $ix = $ixArray[1];
        self::assertEquals($this->ixSet->getPositiveSuffix(), $ixArray[1]);
        self::assertEquals($expected, $ix->getIx());

        $expected = '-';
        $ix = $ixArray[2];
        self::assertEquals($this->ixSet->getNegativePrefix(), $ixArray[2]);
        self::assertEquals($expected, $ix->getIx());

        $expected = 'bar';
        $ix = $ixArray[3];
        self::assertEquals($this->ixSet->getNegativeSuffix(), $ixArray[3]);
        self::assertEquals($expected, $ix->getIx());
    }

    public function testSetGetPrefixesSuffixesGetAllSymbols() : void
    {
        $symbolA = new Symbol();
        $symbolA->setSymbolFromChar('&');

        $prefix = new Prefix();
        $prefix->addSymbol($symbolA);

        $symbolB = new Symbol();
        $symbolB->setSymbolFromChar('$');

        $suffix = new Suffix();
        $suffix->addSymbol($symbolB);

        $this->ixSet->setPositivePrefix($prefix);
        self::assertEquals($prefix, $this->ixSet->getPositivePrefix());

        $this->ixSet->setPositiveSuffix($suffix);
        self::assertEquals($suffix, $this->ixSet->getPositiveSuffix());

        $expectedResult = [$symbolA, $symbolB];
        self::assertEquals($expectedResult, $this->ixSet->getAllSymbols());

        $this->ixSet->setNegativePrefix($prefix);
        self::assertEquals($prefix, $this->ixSet->getNegativePrefix());

        $this->ixSet->setNegativeSuffix($suffix);
        self::assertEquals($suffix, $this->ixSet->getNegativeSuffix());

        $expectedResult = [$symbolA, $symbolB, $symbolA, $symbolB];
        self::assertEquals($expectedResult, $this->ixSet->getAllSymbols());
    }

    public function testMerge() : void
    {
        $this->ixSet->setFormat('+', 'foo', '-', 'bar');
        $newIxSet = new IxSet();
        $newIxSet->setFormat('$', '', '', '$$');
        $this->ixSet->merge($newIxSet);

        $expected = '+$';
        self::assertEquals($expected, $this->ixSet->getPositivePrefixString());

        $expected = 'foo';
        self::assertEquals($expected, $this->ixSet->getPositiveSuffixString());

        $expected = '-';
        self::assertEquals($expected, $this->ixSet->getNegativePrefixString());

        $expected = '$$bar';
        self::assertEquals($expected, $this->ixSet->getNegativeSuffixString());
    }

    public function testConfigureOptions() : void
    {
        $this->ixSet->setFormat('+', '', '-', '');
        $frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
        self::assertTrue($this->ixSet->configureOptions($frmtr));
        $expectedPattern = '+#,##0.###;-#,##0.###';
        self::assertEquals($expectedPattern, $frmtr->getPattern());
    }
}
