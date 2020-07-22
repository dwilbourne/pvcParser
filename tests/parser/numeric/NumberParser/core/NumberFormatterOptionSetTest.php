<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\core;

use Mockery;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\core\NumberFormatterOptionSet;

class NumberFormatterOptionSetTest extends TestCase
{

    /** @phpstan-ignore-next-line */
    protected $option1;

    /** @phpstan-ignore-next-line */
    protected $option2;

    /** @phpstan-ignore-next-line */
    protected $numberFormatter;

    protected NumberFormatterOptionSet $optionSet;

    public function setUp(): void
    {
        $this->optionSet = new NumberFormatterOptionSet();
        $this->numberFormatter = Mockery::mock('\NumberFormatter');

        $this->option1 = Mockery::mock('pvc\parser\numeric\NumberParser\core\NumberFormatterOption');
        $this->option1->shouldReceive('configure')->with($this->numberFormatter)->andReturn(true);
        $this->option2 = Mockery::mock('pvc\parser\numeric\NumberParser\core\NumberFormatterOption');
        $this->option2->shouldReceive('configure')->with($this->numberFormatter)->andReturn(true);
    }

    public function testAddOptionGetOption() : void
    {
        $this->optionSet->addOption($this->option1);
        $this->optionSet->addOption($this->option2);
        self::assertTrue(2 == count($this->optionSet->getOption()));
    }

    public function testConfigureOptions() : void
    {
        $this->optionSet->addOption($this->option1);
        $this->optionSet->addOption($this->option2);

        self::assertTrue($this->optionSet->configureOptions($this->numberFormatter));
    }

    public function testGetAllSymbols() : void
    {
        self::assertIsArray($this->optionSet->getAllSymbols());
    }
}
