<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvcTests\parser\numeric\NumberParser\configurations;

use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\configurations\NumberParserCurrencyConfiguration;

class NumberParserCurrencyConfigurationTest extends TestCase
{
    protected NumberParserCurrencyConfiguration $config;

    public function setUp(): void
    {
        $this->config = new NumberParserCurrencyConfiguration();
    }

    public function testConstruct(): void
    {
        self::assertEquals(NumberFormatter::CURRENCY, $this->config->getFormatterStyle());
        self::assertEquals(NumberFormatter::TYPE_DOUBLE, $this->config->getFormatterType());
    }
}
