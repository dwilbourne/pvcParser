<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\prefix_suffix;

/**
 * Class Prefix
 */
class Prefix extends Ix
{
    /**
     * @function merge
     * @param Ix $ix
     * @return mixed|void
     */
    public function merge(Ix $ix)
    {
        $this->symbols = array_merge($this->symbols, $ix->getSymbols());
    }
}
