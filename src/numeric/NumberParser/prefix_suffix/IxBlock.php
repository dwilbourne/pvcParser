<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\prefix_suffix;

use pvc\array_utils\CartesianProduct\CartesianProduct;
use pvc\array_utils\CartesianProduct\CartesianProductException;
use pvc\parser\numeric\NumberParser\core\ConfigurationBlock;
use pvc\parser\numeric\NumberParser\core\ConfigurationSet;
use pvc\parser\numeric\NumberParser\core\err\InvalidConfigurationSetException;

/**
 * Class IxBlock
 */
class IxBlock extends ConfigurationBlock
{
    /**
     * @function validateConfigurationSet
     * @param ConfigurationSet $cs
     * @return bool
     */
    public function validateConfigurationSet(ConfigurationSet $cs): bool
    {
        return $cs instanceof IxSet;
    }

    /**
     * @function merge
     * @param IxBlock $block
     * @throws CartesianProductException
     * @throws InvalidConfigurationSetException
     */
    public function merge(IxBlock $block) : void
    {
        // it is important to copy the ixArray because the original is going to be reinitialized in just a second
        $array = [$this->getConfigurationSet(), $block];
        // reinitialize
        $this->configurationSetArray = [];

        $ixSetArrays = new CartesianProduct($array);
        foreach ($ixSetArrays as $ixSetArray) {
            $ixSet = new IxSet();
            foreach ($ixSetArray as $subset) {
                $ixSet->merge($subset);
            }
            $this->addConfigurationSet($ixSet);
        }
    }
}
