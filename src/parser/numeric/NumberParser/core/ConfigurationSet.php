<?php declare(strict_types = 1); /** @noinspection PhpUndefinedClassInspection */

/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core;

use \NumberFormatter;
use pvc\parser\numeric\NumberParser\core\err\DuplicateSymbolValueException;
use pvc\parser\numeric\NumberParser\symbol\Symbol;

abstract class ConfigurationSet
{
    abstract public function configureOptions(NumberFormatter $frmtr): bool;

    abstract public function getAllSymbols(): array;

    public function createSymbolMap(array $symbolArray): array
    {
        $result = [];
        foreach ($symbolArray as $symbol) {
            // each symbol can have only one substitute value
            $type = $symbol->getSymbolType();
            $value = $symbol->getSymbolValue();

            if (($type != Symbol::LITERAL) && (!empty($value))) {
                if (isset($result[$type]) && ($result[$type] != $value)) {
                    throw new DuplicateSymbolValueException($symbol);
                }
                if (!empty($value)) {
                    $result[$type] = $value;
                }
            }
        }
        return $result;
    }

    public function createSymbolOptionSet(array $symbolMap): NumberFormatterOptionSet
    {
        $optionSet = new NumberFormatterOptionSet();
        foreach ($symbolMap as $type => $value) {
            $option = new NumberFormatterOption('setSymbol', $type, $value);
            $optionSet->addOption($option);
        }
        return $optionSet;
    }

    public function configureSymbolOptionSet(NumberFormatter $frmtr): bool
    {
        $symbolArray = $this->getAllSymbols();
        $symbolMap = $this->createSymbolMap($symbolArray);
        $optionSet = $this->createSymbolOptionSet($symbolMap);
        return $optionSet->configureOptions($frmtr);
    }
}
