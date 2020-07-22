<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations_pvc;

use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserPercentConfigurationPvc;

class NumberParserPercentConfigurationPvcTest extends TestCase
{
    protected NumberParserPercentConfigurationPvc $config;

    public function setUp(): void
    {
        $this->config = new NumberParserPercentConfigurationPvc();
    }

    public function testConstruct() : void
    {
        // the block should have 4 config sets (4 plus minus x 1 currency)

        $blocks = $this->config->getConfigurationBlocks();
        $sets = $blocks[0];
        self::assertEquals(4, count($sets));
    }
}
