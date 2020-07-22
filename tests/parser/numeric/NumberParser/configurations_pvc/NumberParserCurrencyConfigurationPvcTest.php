<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations_pvc;

use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserCurrencyConfigurationPvc;

class NumberParserCurrencyConfigurationPvcTest extends TestCase
{
    protected NumberParserCurrencyConfigurationPvc $config;

    public function setUp(): void
    {
        $this->config = new NumberParserCurrencyConfigurationPvc();
    }

    public function testConstruct() : void
    {
        // the block should have 16 config sets (4 plus minus x 4 currency)

        $blocks = $this->config->getConfigurationBlocks();
        $sets = $blocks[0];
        self::assertEquals(16, count($sets));
    }
}
