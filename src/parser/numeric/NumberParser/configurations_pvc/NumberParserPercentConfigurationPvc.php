<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations_pvc;

use pvc\parser\numeric\NumberParser\configurations\NumberParserCurrencyConfiguration;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PercentIxBlockPvc;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PlusMinusIxBlockPvc;

/**
 * Class NumberParserCurrencyConfigurationPvc
 */
class NumberParserPercentConfigurationPvc extends NumberParserCurrencyConfiguration
{
    public function __construct()
    {
        parent::__construct();

        $plusMinusBlock = new PlusMinusIxBlockPvc();
        $percentBlock = new PercentIxBlockPvc();
        $plusMinusBlock->merge($percentBlock);

        $this->addConfigurationBlock($plusMinusBlock);
    }
}
