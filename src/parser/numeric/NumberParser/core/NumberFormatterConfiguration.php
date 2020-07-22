<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core;

use NumberFormatter;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\parser\numeric\NumberParser\grouping\GroupingOptionSet;
use pvc\parser\numeric\NumberParser\prefix_suffix\IxSet;
use pvc\parser\numeric\NumberParser\symbol\DecimalSymbol;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * Class NumberFormatterConfiguration
 */
class NumberFormatterConfiguration
{
    /**
     * @var array
     */
    protected array $configSetArray = [];

    /**
     * @function getConfigSet
     * @return array
     */
    public function getConfigSet(): array
    {
        return $this->configSetArray;
    }

    /**
     * @function addConfigSet
     * @param ConfigurationSet $configSet
     */
    public function addConfigSet(ConfigurationSet $configSet) : void
    {
        $this->configSetArray[] = $configSet;
    }

    /**
     * @function addConfigSets
     * @param ConfigurationSet[] $configSets
     */
    public function addConfigSets(array $configSets) : void
    {
        foreach ($configSets as $configSet) {
            $this->addConfigSet($configSet);
        }
    }

    /**
     * @function configure
     * @param NumberFormatter $frmtr
     * @return bool
     */
    public function configure(NumberFormatter &$frmtr): bool
    {
        $result = true;
        foreach ($this->configSetArray as $config) {
            $result = $result && $config->configureOptions($frmtr);
        }
        return $result;
    }
}
