<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */


use NumberFormatter;
use pvc\array_utils\CartesianProduct\CartesianProduct;
use pvc\intl\Locale;
use pvc\parser\numeric\NumberParser\configurations\NumberParserConfiguration;
use pvc\parser\numeric\NumberParser\core\ConfigurationBlock;
use pvc\parser\numeric\NumberParser\core\NumberFormatterConfiguration;
use PHPUnit\Framework\TestCase;
use Mockery;
use pvc\testingTraits\MockeryIteratorTrait;

class NumberParserConfigurationTest extends TestCase
{
    use MockeryIteratorTrait;

    protected NumberParserConfiguration $npc;

    public function setUp(): void
    {
        $this->npc = new NumberParserConfiguration(NumberFormatter::DECIMAL, NumberFormatter::TYPE_DOUBLE);
    }

    public function testSetGetFormatterStyle() : void
    {
        $style = NumberFormatter::CURRENCY;
        $this->npc->setFormatterStyle($style);
        self::assertEquals($style, $this->npc->getFormatterStyle());

        $style = NumberFormatter::PERCENT;
        $this->npc->setFormatterStyle($style);
        self::assertEquals($style, $this->npc->getFormatterStyle());
    }

    public function testGetFormatterConstructionStyle() : void
    {
        // test to make sure currency style actually returns a formatter type of decimal
        $this->npc->setFormatterStyle(NumberFormatter::CURRENCY);
        self::assertEquals(NumberFormatter::DECIMAL, $this->npc->getFormatterConstructionStyle());
    }

    public function testSetGetFormatterType() : void
    {
        $type = NumberFormatter::TYPE_DOUBLE;
        $this->npc->setFormatterType($type);
        self::assertEquals($type, $this->npc->getFormatterType());

        $type = NumberFormatter::TYPE_INT32;
        $this->npc->setFormatterType($type);
        self::assertEquals($type, $this->npc->getFormatterType());
    }

    public function testGetNumberFormatter() : void
    {
        $locale = new Locale('de_DE');
        self::assertTrue($this->npc->getNumberFormatter($locale) instanceof NumberFormatter);
    }

    public function testAddConfigurationBlocks() : void
    {
        $configurationBlockA = Mockery::mock(ConfigurationBlock::class);
        $this->npc->addConfigurationBlock($configurationBlockA);
        self::assertEquals(1, count($this->npc->getConfigurationBlocks()));

        $configurationBlockB = Mockery::mock('pvc\parser\numeric\NumberParser\core\ConfigurationBlock');
        $this->npc->addConfigurationBlock($configurationBlockB);
        self::assertEquals(2, count($this->npc->getConfigurationBlocks()));
    }

    public function testGetNumberFormatterConfiguration() : void
    {
        // nothing in the test should care what is in these 'optionSetArrays' since we are mocking the
        // behavior of the 'addOptionSets' method.

        $configurationBlock1 = 'foo';
        $configurationBlock2 = 'bar';

        // nothing in the test should care that there are actually no option blocks in the configuration object since
        // we are mocking the result of the Cartesian product in any case.

        $cpData = [$configurationBlock1, $configurationBlock2];
        $cartesianMock = Mockery::mock('overload:' . CartesianProduct::class, 'Iterator');
        // $this->>mockIterator is available via the MockIteratorTrait
        $cartesianMock = $this->mockIterator($cartesianMock, $cpData);

        // OK, so we have the hard dependency on CartesianProduct mocked and the SUT can iterate over the mock and
        // the iteration should return $configurationBlock1 and $configurationBlock2

        $mockNfc = Mockery::mock('overload:' . NumberFormatterConfiguration::class);
        $mockNfc->shouldReceive('addConfigSets')->withAnyArgs();
        // now the hard dependency on NumberFormatterConfiguration is mocked and expectations set

        $result = $this->npc->getNumberFormatterConfiguration();
        self::assertEquals(2, count($result));
        foreach ($result as $configuration) {
            self::assertTrue($configuration instanceof NumberFormatterConfiguration);
        }
    }
}
