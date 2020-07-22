<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations_pvc;

use PHPUnit\Framework\TestCase;
use pvc\intl\Locale;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PlusMinusIxBlockPvc;
use pvc\parser\numeric\NumberParser\configurations_pvc\NumberParserDecimalConfigurationPvc;

class NumberParserDecimalConfigurationPvcTest extends TestCase
{
    protected Locale $locale;
    protected NumberParserDecimalConfigurationPvc $config;

    public function setUp(): void
    {
        $this->locale = new Locale('en_US');
        $this->config = new NumberParserDecimalConfigurationPvc();
    }

    public function testConstruct() : void
    {
        // there should be one PlusMinus configuration block

        $blocks = $this->config->getConfigurationBlocks();
        self::assertEquals(1, count($blocks));
        $block = $blocks[0];
        self::assertTrue($block instanceof PlusMinusIxBlockPvc);
    }

    public function testPatterns() : void
    {
        $patternArray = [
            '#,##0.###',
            '+#,##0.###;-#,##0.###',
            '#,##0.###+;#,##0.###-',
            "#,##0.###;(#,##0.###)",
        ];

        $nfConfigArray = $this->config->getNumberFormatterConfiguration();
        $i = 0;
        foreach ($nfConfigArray as $nfConfig) {
            $frmtr = $this->config->getNumberFormatter($this->locale);
            $nfConfig->configure($frmtr);
            self::assertEquals($patternArray[$i++], $frmtr->getPattern());
        }
    }
}
