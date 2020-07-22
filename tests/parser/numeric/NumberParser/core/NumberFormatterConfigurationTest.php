<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\core;

use Mockery;
use pvc\parser\numeric\NumberParser\core\NumberFormatterConfiguration;
use PHPUnit\Framework\TestCase;

class NumberFormatterConfigurationTest extends TestCase
{
    /** @phpstan-ignore-next-line */
    protected $configSet1;

    /** @phpstan-ignore-next-line */
    protected $configSet2;

    /** @phpstan-ignore-next-line */
    protected $numberFormatter;

    protected NumberFormatterConfiguration $nfc;


    public function setUp(): void
    {
        $this->numberFormatter = Mockery::mock('\NumberFormatter');

        $this->configSet1 = Mockery::mock('pvc\parser\numeric\NumberParser\core\ConfigurationSet');
        $this->configSet1->shouldReceive('configureOptions')->with($this->numberFormatter)->andReturn(true);

        $this->configSet2 = Mockery::mock('pvc\parser\numeric\NumberParser\core\ConfigurationSet');
        $this->configSet2->shouldReceive('configureOptions')->with($this->numberFormatter)->andReturn(true);


        $this->nfc = new NumberFormatterConfiguration();
    }

    public function testAddConfigSet() : void
    {
        $this->nfc->addConfigSet($this->configSet1);
        $csa = $this->nfc->getConfigSet();
        self::assertEquals(1, count($csa));
        $this->nfc->addConfigSet($this->configSet2);
        $csa = $this->nfc->getConfigSet();
        self::assertEquals(2, count($csa));
    }

    public function testAddConfigSets() : void
    {
        $this->nfc->addConfigSets([$this->configSet1, $this->configSet2]);
        $csa = $this->nfc->getConfigSet();
        self::assertEquals(2, count($csa));
    }

    public function testConfigure() : void
    {
        $this->nfc->addConfigSets([$this->configSet1, $this->configSet2]);
        self::assertTrue($this->nfc->configure($this->numberFormatter));
    }
}
