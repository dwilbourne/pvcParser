<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\configurations\NumberParserCurrencyConfiguration;
use PHPUnit\Framework\TestCase;

class NumberParserCurrencyConfigurationTest extends TestCase
{
    protected NumberParserCurrencyConfiguration $config;

    public function setUp(): void
    {
        $this->config = new NumberParserCurrencyConfiguration();
    }

    public function testConstruct() : void
    {
        self::assertEquals(NumberFormatter::CURRENCY, $this->config->getFormatterStyle());
        self::assertEquals(NumberFormatter::TYPE_DOUBLE, $this->config->getFormatterType());
    }
}
