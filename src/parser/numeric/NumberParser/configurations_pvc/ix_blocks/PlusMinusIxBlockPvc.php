<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks;

use pvc\parser\numeric\NumberParser\prefix_suffix\IxBlock;
use pvc\parser\numeric\NumberParser\prefix_suffix\IxSet;
use pvc\parser\numeric\NumberParser\prefix_suffix\Prefix;
use pvc\parser\numeric\NumberParser\prefix_suffix\Suffix;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * Class PlusMinusOptionBlockPvc
 */
class PlusMinusIxBlockPvc extends IxBlock
{
    public function __construct()
    {
        $ixSet = new IxSet();
        $ixSet->setFormat('', '', '-', '');
        $this->addConfigurationSet($ixSet);

        $ixSet = new IxSet();
        $ixSet->setFormat('+', '', '-', '');
        $this->addConfigurationSet($ixSet);

        $ixSet = new IxSet();
        $ixSet->setFormat('', '+', '', '-');
        $this->addConfigurationSet($ixSet);

        $leftParen = new Symbol();
        $leftParen->setSymbolType(Symbol::LITERAL);
        $leftParen->setSymbolValue('(');
        $prefix = new Prefix();
        $prefix->addSymbol($leftParen);

        $rightParen = new Symbol();
        $rightParen->setSymbolType(Symbol::LITERAL);
        $rightParen->setSymbolValue(')');
        $suffix = new Suffix();
        $suffix->addSymbol($rightParen);

        $ixSet = new IxSet();
        $ixSet->setNegativePrefix($prefix);
        $ixSet->setNegativeSuffix($suffix);
        $this->addConfigurationSet($ixSet);
    }
}
