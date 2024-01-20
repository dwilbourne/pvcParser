<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks;

use pvc\parser\numeric\NumberParser\prefix_suffix\IxBlock;
use pvc\parser\numeric\NumberParser\prefix_suffix\IxSet;

/**
 * Class PermillIxBlock
 */
class PermillIxBlockPvc extends IxBlock
{
    public function __construct()
    {
        $ixSet = new IxSet();
        $ixSet->setFormat('', '‰', '', '‰');
        $this->addConfigurationSet($ixSet);
    }
}
