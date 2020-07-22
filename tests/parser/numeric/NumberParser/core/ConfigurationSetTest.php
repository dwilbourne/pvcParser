<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\core;

use Mockery;
use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\core\NumberFormatterOptionSet;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

class ConfigurationSetTest extends TestCase
{

    /** @phpstan-ignore-next-line */
    protected $configSet;
    protected array $symbolArray;

    public function setUp(): void
    {
        $this->configSet = Mockery::mock('pvc\parser\numeric\NumberParser\core\ConfigurationSet')->makePartial();

        $symbolA = new Symbol();
        $type = NumberFormatter::PLUS_SIGN_SYMBOL;
        $quoted = true;
        $value = '*';
        $symbolA->setSymbolType($type, $quoted);
        $symbolA->setSymbolValue($value);

        $symbolB = new Symbol();
        $type = NumberFormatter::MINUS_SIGN_SYMBOL;
        $quoted = true;
        $value = '@';
        $symbolB->setSymbolType($type, $quoted);
        $symbolB->setSymbolValue($value);


        $this->symbolArray = [$symbolA, $symbolB];

        $this->configSet->shouldReceive('getAllSymbols')->withNoArgs()->andReturn($this->symbolArray);
    }

    public function testCreateSymbolMap1() : void
    {
        $expectedResult = [NumberFormatter::PLUS_SIGN_SYMBOL => '*', NumberFormatter::MINUS_SIGN_SYMBOL => '@'];
        self::assertEquals($expectedResult, $this->configSet->createSymbolMap($this->symbolArray));
    }

    public function testCreateSymbolMap2() : void
    {
        $symbolC = new Symbol();
        $type = NumberFormatter::MINUS_SIGN_SYMBOL;
        $quoted = true;
        $value = '!';
        $symbolC->setSymbolType($type, $quoted);
        $symbolC->setSymbolValue($value);

        $this->symbolArray[] = $symbolC;
        $this->expectException('pvc\parser\numeric\NumberParser\core\err\DuplicateSymbolValueException');
        $x = $this->configSet->createSymbolMap($this->symbolArray);
    }

    public function testCreateSymbolOptionSet() : void
    {
        $symbolMap = $this->configSet->createSymbolMap($this->symbolArray);
        $optionSet = $this->configSet->createSymbolOptionSet($symbolMap);
        self::assertTrue($optionSet instanceof NumberFormatterOptionSet);
        $optionArray = $optionSet->getOption();
        self::assertEquals(2, count($optionArray));
    }
}
