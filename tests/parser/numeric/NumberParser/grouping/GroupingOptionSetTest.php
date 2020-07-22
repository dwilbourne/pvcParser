<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\grouping;

use Mockery;
use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\core\ConfigurationSet;
use pvc\parser\numeric\NumberParser\grouping\err\OptionSetFormatException;
use pvc\parser\numeric\NumberParser\grouping\GroupingOptionSet;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

class GroupingOptionSetTest extends TestCase
{
    protected GroupingOptionSet $gos;
    protected string $optionSetFormatException;

    public function setUp(): void
    {
        $monetary = false;
        $this->gos = new GroupingOptionSet($monetary);
    }

    public function testSetGetGroupingSeparatorSymbol1() : void
    {
        $symbol = Mockery::mock('pvc\parser\numeric\NumberParser\symbol\Symbol');
        $symbol->shouldReceive('isGrouping')->withNoArgs()->andReturn(true);
        $symbol->shouldReceive('isMonetary')->withNoArgs()->andReturn(false);
        $this->gos->setGroupingSeparatorSymbol($symbol);
        self::assertEquals($symbol, $this->gos->getGroupingSeparatorSymbol());
    }

    public function testSetGetGroupingSeparatorSymbol2() : void
    {
        $this->gos->setMonetary(true);
        $symbol = Mockery::mock('pvc\parser\numeric\NumberParser\symbol\Symbol');
        $symbol->shouldReceive('isGrouping')->withNoArgs()->andReturn(false);
        $symbol->shouldReceive('isMonetary')->withNoArgs()->andReturn(true);
        $symbol->shouldReceive('getPatternChar')->withNoArgs()->andReturn(';');
        self::expectException('pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException');
        $this->gos->setGroupingSeparatorSymbol($symbol);
    }

    public function testSetGetGroupingSeparatorSymbol3() : void
    {
        $this->gos->setMonetary(true);
        $symbol = Mockery::mock('pvc\parser\numeric\NumberParser\symbol\Symbol');
        $symbol->shouldReceive('isGrouping')->withNoArgs()->andReturn(true);
        $symbol->shouldReceive('isMonetary')->withNoArgs()->andReturn(false);
        $symbol->shouldReceive('getPatternChar')->withNoArgs()->andReturn(';');
        self::expectException('pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException');
        $this->gos->setGroupingSeparatorSymbol($symbol);
    }

    public function testConstruct() : void
    {
        self::assertTrue($this->gos instanceof ConfigurationSet);
    }

    public function testSetFormatNoGroupingSeparatorAllowedAndOmitRemainingArgs() : void
    {
        $allowGroupingSeparator = false;
        $this->gos->setFormat($allowGroupingSeparator);
        // should be one option set now
        self::assertEquals(1, count($this->gos));
    }

    public function testSetFormatNoGroupingSeparatorAllowedAndOptionSetFormatException() : void
    {
        $allowGroupingSeparator = false;
        $lenientParse = true;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse);
    }

    public function testSetFormatAllowGroupingButNullLenientParse() : void
    {
        $allowGroupingSeparator = true;
        $this->expectException(OptionSetFormatException::class);
        // lenientParse is a required parameter if grouping separator is allowed and defaults to null if omitted
        $this->gos->setFormat($allowGroupingSeparator);
    }

    public function testSetFormatAllowGroupingWithNonNullLenientParse() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = false;
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse);
        $optionArray = $this->gos->getOptionSet()->getOption();
        self::assertEquals(2, count($optionArray));
    }

    public function testSetFormatWithLenientParseTrueAndPrimaryGroupingSizeSet() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = true;
        $groupingSeparatorSymbol = null;
        $groupingSize = 4;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSeparatorSymbol, $groupingSize);
    }

    public function testSetFormatWithLenientParseTrueAndSecondaryGroupingSizeSet() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = true;
        $groupingSeparatorSymbol = null;
        $groupingSize = null;
        $secondaryGroupingSize = 2;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat(
            $allowGroupingSeparator,
            $lenientParse,
            $groupingSeparatorSymbol,
            $groupingSize,
            $secondaryGroupingSize
        );
    }

    public function testSetFormatSetPrimaryGroupingSizeException1() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = false;
        $groupingSize = 0;
        $secondaryGroupingSize = null;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSize, $secondaryGroupingSize);
    }

    public function testSetFormatSetPrimaryGroupingSizeException2() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = false;
        $groupingSize = 2 ** 31;
        $secondaryGroupingSize = null;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSize, $secondaryGroupingSize);
    }

    public function testSetFormatSetSecondaryGroupingSizeException1() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = false;
        $groupingSize = 3;
        $secondaryGroupingSize = 0;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSize, $secondaryGroupingSize);
    }

    public function testSetFormatSetSecondaryGroupingSizeException2() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = false;
        $groupingSize = 3;
        $secondaryGroupingSize = 2 ** 31;
        $this->expectException(OptionSetFormatException::class);
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSize, $secondaryGroupingSize);
    }

    public function testSetFormatAllOptions() : void
    {
        $allowGroupingSeparator = true;
        $lenientParse = false;
        $groupingSize = 3;
        $secondaryGroupingSize = 2;
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSize, $secondaryGroupingSize);
        $optionArray = $this->gos->getOptionSet()->getOption();
        self::assertEquals(4, count($optionArray));

        $option = $optionArray[0];
        self::assertEquals('setAttribute', $option->getMethod());
        self::assertEquals(NumberFormatter::GROUPING_USED, $option->getAttribute());
        self::assertEquals(1, $option->getValue());

        $option = $optionArray[1];
        self::assertEquals('setAttribute', $option->getMethod());
        self::assertEquals(NumberFormatter::LENIENT_PARSE, $option->getAttribute());
        self::assertEquals(0, $option->getValue());

        $option = $optionArray[2];
        self::assertEquals('setAttribute', $option->getMethod());
        self::assertEquals(NumberFormatter::GROUPING_SIZE, $option->getAttribute());
        self::assertEquals(3, $option->getValue());

        $option = $optionArray[3];
        self::assertEquals('setAttribute', $option->getMethod());
        self::assertEquals(NumberFormatter::SECONDARY_GROUPING_SIZE, $option->getAttribute());
        self::assertEquals(2, $option->getValue());
    }

    public function testConfigureOptions1() : void
    {
        $frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);

        $allowGroupingSeparator = true;
        $lenientParse = false;
        $groupingSize = 5;
        $secondaryGroupingSize = 2;
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse, $groupingSize, $secondaryGroupingSize);

        $symbol = new Symbol();
        $type = NumberFormatter::GROUPING_SEPARATOR_SYMBOL;
        $quoted = true;
        $value = '*';
        $symbol->setSymbolType($type, $quoted);
        $symbol->setSymbolValue($value);
        $this->gos->setGroupingSeparatorSymbol($symbol);

        self::assertTrue($this->gos->configureOptions($frmtr));

        // GROUPING_USED attribute is either 1 (true) or 0 (false)
        self::assertEquals(1, $frmtr->getAttribute(NumberFormatter::GROUPING_USED));

        // LENIENT_PARSE attribute is either 1 (true) or 0 (false)
        self::assertEquals(0, $frmtr->getAttribute(NumberFormatter::LENIENT_PARSE));

        // changed the grouping separator symbol to an asterisk.  if this object had been created with
        // the monetary flag set to true, we would be testing against the MONETARY_GROUPING_SEPARATOR_SYMBOL.
        self::assertEquals($value, $frmtr->getSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL));

        self::assertEquals($groupingSize, $frmtr->getAttribute(NumberFormatter::GROUPING_SIZE));
        self::assertEquals($secondaryGroupingSize, $frmtr->getAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE));

        self::assertFalse($this->gos->getMonetary());
    }

    public function testConfigureOptions2() : void
    {
        $frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);

        $allowGroupingSeparator = false;
        $this->gos->setFormat($allowGroupingSeparator);
        self::assertTrue($this->gos->configureOptions($frmtr));

        // GROUPING_USED attribute is either 1 (true) or 0 (false)
        self::assertEquals(0, $frmtr->getAttribute(NumberFormatter::GROUPING_USED));
    }

    public function testConfigureOptions3() : void
    {
        $frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);

        $allowGroupingSeparator = true;
        $lenientParse = true;
        $this->gos->setFormat($allowGroupingSeparator, $lenientParse);
        self::assertTrue($this->gos->configureOptions($frmtr));

        // GROUPING_USED attribute is either 1 (true) or 0 (false)
        self::assertEquals(1, $frmtr->getAttribute(NumberFormatter::GROUPING_USED));
        self::assertEquals(1, $frmtr->getAttribute(NumberFormatter::LENIENT_PARSE));
    }
}
