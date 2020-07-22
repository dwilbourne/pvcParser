<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\grouping;

use Countable;
use NumberFormatter;
use pvc\parser\numeric\NumberParser\core\ConfigurationSet;
use pvc\parser\numeric\NumberParser\core\NumberFormatterOption;
use pvc\parser\numeric\NumberParser\core\NumberFormatterOptionSet;
use pvc\parser\numeric\NumberParser\grouping\err\OptionSetFormatException;
use pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidSymbolException;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

/**
 * Class GroupingConfigurator
 */
class GroupingOptionSet extends ConfigurationSet implements Countable
{
    /**
     * @var bool
     */
    protected bool $monetary;

    /**
     * @var Symbol
     */
    protected Symbol $groupingSeparatorSymbol;

    /**
     * @var NumberFormatterOptionSet
     */
    protected NumberFormatterOptionSet $optionSet;

    /**
     * GroupingOptionSet constructor.
     * @param bool $monetary
     */
    public function __construct(bool $monetary)
    {
        $this->optionSet = new NumberFormatterOptionSet();
        $this->setMonetary($monetary);
    }

    /**
     * @function setMonetary
     * @param bool $monetary
     */
    public function setMonetary(bool $monetary) : void
    {
        $this->monetary = $monetary;
    }

    /**
     * @function getMonetary
     * @return bool
     */
    public function getMonetary(): bool
    {
        return $this->monetary;
    }

    /**
     * @function setGroupingSeparatorSymbol
     * @param Symbol $symbol
     * @throws InvalidSymbolException
     * @throws \pvc\parser\numeric\NumberParser\symbol\err\UnsetSymbolValueException
     */
    public function setGroupingSeparatorSymbol(Symbol $symbol) : void
    {
        if (!$symbol->isGrouping()) {
            throw new InvalidSymbolException('grouping', $symbol);
        }
        if ($symbol->isMonetary() != $this->monetary) {
            throw new InvalidSymbolException('monetary', $symbol);
        }
        $this->groupingSeparatorSymbol = $symbol;
    }

    /**
     * @function getGroupingSeparatorSymbol
     * @return Symbol
     */
    public function getGroupingSeparatorSymbol(): Symbol
    {
        return $this->groupingSeparatorSymbol;
    }

    /**
     * @function getAllSymbols
     * @return Symbol[]
     */
    public function getAllSymbols(): array
    {
        return isset($this->groupingSeparatorSymbol) ? [$this->groupingSeparatorSymbol] : [];
    }

    /**
     * @function getOptionSet
     * @return NumberFormatterOptionSet
     */
    public function getOptionSet(): NumberFormatterOptionSet
    {
        return $this->optionSet;
    }

    /**
     * @function count
     * @return int
     */
    public function count(): int
    {
        return count($this->optionSet);
    }

    /**
     * @function setFormat
     * @param bool $allowGroupingSeparator
     * @param bool|null $lenientParse
     * @param int|null $groupingSize
     * @param int|null $secondaryGroupingSize
     * @throws OptionSetFormatException
     */
    public function setFormat(
        bool $allowGroupingSeparator,
        bool $lenientParse = null,
        int $groupingSize = null,
        int $secondaryGroupingSize = null
    ) : void {
        $argv = func_get_args();

        $groupingUsedValue = $allowGroupingSeparator ? 1 : 0;
        $option = new NumberFormatterOption('setAttribute', NumberFormatter::GROUPING_USED, $groupingUsedValue);
        $this->optionSet->addOption($option);

        if (!$allowGroupingSeparator) {
            for ($i = 1; $i < count($argv); $i++) {
                if (!is_null($argv[$i])) {
                    $msg = 'if grouping separator is not allowed, ';
                    $msg .= 'then all following parameters must be null or not present.';
                    throw new OptionSetFormatException($msg);
                }
            }
            return;
        }

        if (is_null($lenientParse)) {
            $msg = 'lenientParse parameter cannot be null if a grouping separator is allowed.';
            throw new OptionSetFormatException($msg);
        }

        $option = new NumberFormatterOption('setAttribute', NumberFormatter::LENIENT_PARSE, $lenientParse);
        $this->optionSet->addOption($option);

        if ($lenientParse) {
            for ($i = 2; $i < count($argv); $i++) {
                if (!is_null($argv[$i])) {
                    $msg = 'if lenient parsing is set, ';
                    $msg .= 'then grouping size and secondary grouping size must be null or not present.';
                    throw new OptionSetFormatException($msg);
                }
            }
        }

        if (!is_null($groupingSize)) {
            if ($groupingSize > pow(2, 30) || $groupingSize < 1) {
                $msg = 'grouping size must be a positive integer less than 2 ^ 30.';
                throw new OptionSetFormatException($msg);
            }
            $option = new NumberFormatterOption('setAttribute', NumberFormatter::GROUPING_SIZE, $groupingSize);
            $this->optionSet->addOption($option);
        }

        if (!is_null($secondaryGroupingSize)) {
            if ($secondaryGroupingSize > pow(2, 30) || $secondaryGroupingSize < 1) {
                $msg = 'secondary grouping size must be a positive integer less than 2 ^ 30.';
                throw new OptionSetFormatException($msg);
            }
            $option = new NumberFormatterOption(
                'setAttribute',
                NumberFormatter::SECONDARY_GROUPING_SIZE,
                $secondaryGroupingSize
            );
            $this->optionSet->addOption($option);
        }
    }

    /**
     * @function configureOptions
     * @param NumberFormatter $frmtr
     * @return bool
     * @throws \pvc\parser\numeric\NumberParser\core\err\OptionConfigurationException
     */
    public function configureOptions(NumberFormatter $frmtr): bool
    {

        // configure any symbol substitutions
        $valid = $this->configureSymbolOptionSet($frmtr);

        // configure any additional grouping options
        $valid = $valid && $this->optionSet->configureOptions($frmtr);

        return $valid;
    }
}
