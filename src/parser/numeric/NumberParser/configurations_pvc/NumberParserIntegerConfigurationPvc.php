<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations_pvc;

use pvc\parser\numeric\NumberParser\configurations\NumberParserIntegerConfiguration;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PlusMinusIxBlockPvc;

/**
 * Class NumberParserIntegerConfigurationPvc
 */
class NumberParserIntegerConfigurationPvc extends NumberParserIntegerConfiguration
{
    public function __construct()
    {
        parent::__construct();
        $block = new PlusMinusIxBlockPvc();
        $this->addConfigurationBlock($block);
    }
}
