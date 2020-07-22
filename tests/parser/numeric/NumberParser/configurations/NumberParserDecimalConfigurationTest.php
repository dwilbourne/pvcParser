<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\configurations\NumberParserDecimalConfiguration;
use PHPUnit\Framework\TestCase;

class NumberParserDecimalConfigurationTest extends TestCase
{
    protected NumberParserDecimalConfiguration $config;

    public function setUp(): void
    {
        $this->config = new NumberParserDecimalConfiguration();
    }

    public function testConstruct() : void
    {
        self::assertEquals(NumberFormatter::DECIMAL, $this->config->getFormatterStyle());
        self::assertEquals(NumberFormatter::TYPE_DOUBLE, $this->config->getFormatterType());
    }
}
