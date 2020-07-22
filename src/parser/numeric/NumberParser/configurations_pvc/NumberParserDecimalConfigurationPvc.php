<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations_pvc;

use pvc\parser\numeric\NumberParser\configurations\NumberParserDecimalConfiguration;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PlusMinusIxBlockPvc;

/**
 * Class ParserDecimalConfiguration
 */
class NumberParserDecimalConfigurationPvc extends NumberParserDecimalConfiguration
{
    public function __construct()
    {
        parent::__construct();

        $ixBlock = new PlusMinusIxBlockPvc();
        $this->addConfigurationBlock($ixBlock);

        /*
         * The stock configuration for NumberFormatter has GROUPING_USED set to 1 (grouping is allowed)
         * and LENIENT_PARSE is set to 0 (off).  It wouldn't hurt anything to add the format below, but it
         * is not necessary.
         *

        $allowGroupingSeparator = true;
        $allowLenientParse = false;
        $optionSet = new GroupingOptionSet();
        $optionSet->addFormat($allowGroupingSeparator, $allowLenientParse);

        $cb = new ConfigurationBlock();
        $cb->addConfigurationSet($optionSet);

        $this->addConfigurationBlock($cb);

        */
        /*
         * If you wanted to change the decimal separator symbol, this is where you would add that option as well,
         * e.g. something odd like
         *

        $optionSet = new DecimalSymbolSet();

        $symbol = new DecimalSymbol();
        $symbol->setValue('*'); // set the separator symbol to an asterisk
        $optionSet->setDecimalSymbol($symbol);

        $cb = new ConfigurationBlock();
        $cb->addConfigurationSet($optionSet);

        $this->addConfigurationBlock($cb);

         *
         */
    }
}
