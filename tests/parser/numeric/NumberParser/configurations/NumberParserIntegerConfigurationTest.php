<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\configurations\NumberParserIntegerConfiguration;
use PHPUnit\Framework\TestCase;

class NumberParserIntegerConfigurationTest extends TestCase
{
    protected NumberParserIntegerConfiguration $config;

    public function setUp(): void
    {
        $this->config = new NumberParserIntegerConfiguration();
    }


    public function testConstruct() : void
    {
        self::assertEquals(NumberFormatter::DECIMAL, $this->config->getFormatterStyle());
        // rather than test for 32 bit or 64 bit, we will just make sure the type is set to some integer
        self::assertIsInt($this->config->getFormatterType());
    }
}
