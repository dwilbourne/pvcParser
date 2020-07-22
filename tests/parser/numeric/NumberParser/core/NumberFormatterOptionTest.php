<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\core;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\core\NumberFormatterOption;

class NumberFormatterOptionTest extends TestCase
{

    protected NumberFormatterOption $nfOption;

    public function testSettersGettersConstruct() : void
    {
        $method = 'setAttribute';
        $attribute = NumberFormatter::GROUPING_USED;
        $value = true;

        $option = new NumberFormatterOption($method, $attribute, $value);
        self::assertEquals($method, $option->getMethod());
        self::assertEquals($attribute, $option->getAttribute());
        self::assertEquals($value, $option->getValue());
    }

    public function testConfigureException() : void
    {
        $method = 'setFoo';
        $attribute = NumberFormatter::GROUPING_USED;
        $value = true;

        $option = new NumberFormatterOption($method, $attribute, $value);
        self::expectException('pvc\parser\numeric\NumberParser\core\err\OptionConfigurationException');
        $frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
        $option->configure($frmtr);
    }

    public function testConfigure() : void
    {
        $method = 'setAttribute';
        $attribute = NumberFormatter::GROUPING_USED;
        $value = true;
        $option = new NumberFormatterOption($method, $attribute, $value);
        $frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
        $result = $option->configure($frmtr);
        self::assertTrue($result);
        // the grouping used attribute is an integer, not a strict boolean
        self::assertEquals(1, $frmtr->getAttribute(NumberFormatter::GROUPING_USED));
    }
}
