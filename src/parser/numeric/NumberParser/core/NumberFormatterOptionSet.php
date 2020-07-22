<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core;

use Countable;
use NumberFormatter;

class NumberFormatterOptionSet extends ConfigurationSet implements Countable
{
    /**
     * @var NumberFormatterOption[]
     */
    protected array $optionArray;

    /**
     * NumberFormatterOptionSet constructor.
     */
    public function __construct()
    {
        $this->optionArray = [];
    }

    /**
     * @function addOption
     * @param NumberFormatterOption $option
     */
    public function addOption(NumberFormatterOption $option) : void
    {
        $this->optionArray[] = $option;
    }

    /**
     * @function getOption
     * @return array|NumberFormatterOption[]
     */
    public function getOption(): array
    {
        return $this->optionArray;
    }

    /**
     * @function configureOptions
     * @param NumberFormatter $frmtr
     * @return bool
     * @throws err\OptionConfigurationException
     */
    public function configureOptions(NumberFormatter $frmtr): bool
    {
        $result = true;
        foreach ($this->optionArray as $option) {
            $result = $result && $option->configure($frmtr);
        }
        return $result;
    }

    /**
     * @function getAllSymbols
     * @return array
     */
    public function getAllSymbols(): array
    {
        return [];
    }

    /**
     * @function count
     * @return int
     */
    public function count(): int
    {
        return count($this->optionArray);
    }
}
