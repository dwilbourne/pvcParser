<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\core;

use Mockery;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\core\ConfigurationBlock;
use pvc\parser\numeric\NumberParser\core\ConfigurationSet;
use pvc\parser\numeric\NumberParser\core\err\InvalidConfigurationSetException;

class ConfigurationBlockTest extends TestCase
{

    /** @phpstan-ignore-next-line */
    protected $cb;

    /** @phpstan-ignore-next-line */
    protected $cs1;

    /** @phpstan-ignore-next-line */
    protected $cs2;

    public function setUp(): void
    {
        $this->cb = Mockery::mock(ConfigurationBlock::class)->makePartial();
        $this->cs1 = Mockery::mock(ConfigurationSet::class);
        $this->cs2 = Mockery::mock(ConfigurationSet::class);
    }

    public function testAddConfigurationSetsAndIteration() : void
    {
        $this->cb->shouldReceive('validateConfigurationSet')->withAnyArgs()->andReturn(true);
        self::assertEquals(0, count($this->cb));
        $this->cb->addConfigurationSet($this->cs1);
        self::assertEquals(1, count($this->cb));
        $this->cb->addConfigurationSet($this->cs2);
        self::assertEquals(2, count($this->cb));

        foreach ($this->cb as $configSet) {
            self::assertTrue($configSet instanceof ConfigurationSet);
        }
    }

    public function testAddInvalidConfigurationSet() : void
    {
        $this->cb->shouldReceive('validateConfigurationSet')->withAnyArgs()->andReturn(false);
        self::expectException(InvalidConfigurationSetException::class);
        $this->cb->addConfigurationSet($this->cs1);
    }

    public function testKey() : void
    {
        $this->cb->shouldReceive('validateConfigurationSet')->withAnyArgs()->andReturn(true);
        self::assertEquals(0, $this->cb->key());
        $this->cb->addConfigurationSet($this->cs1);
        $this->cb->next();
        self::assertEquals(1, $this->cb->key());
    }
}
