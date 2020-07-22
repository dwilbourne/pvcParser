<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\prefix_suffix;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\core\ConfigurationSet;
use pvc\parser\numeric\NumberParser\prefix_suffix\err\InvalidFormatException;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolFromCharException;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;

/**
 * Class IxSet
 */
class IxSet extends ConfigurationSet
{
    protected array $ixArray = [];

    /**
     * IxSet constructor.
     */
    public function __construct()
    {
        for ($i = 0; $i < 4; $i++) {
            if ($i % 2 == 0) {
                $ix = new Prefix();
            } else {
                $ix = new Suffix();
            }
            $this->ixArray[$i] = $ix;
        }
    }

    /**
     * @function getIx
     * @return array
     */
    public function getIx(): array
    {
        return $this->ixArray;
    }

    /**
     * @function setFormat
     * @param string $positivePrefix
     * @param string $positiveSuffix
     * @param string $negativePrefix
     * @param string $negativeSuffix
     * @throws InvalidFormatException
     * @throws SetSymbolFromCharException
     * @throws SetSymbolTypeException
     * @throws err\InvalidSymbolException
     */
    public function setFormat(
        string $positivePrefix,
        string $positiveSuffix,
        string $negativePrefix,
        string $negativeSuffix
    ) : void {
        // at least one argument must be non-null
        $argv = func_get_args();
        $valid = false;
        foreach ($argv as $arg) {
            if (!empty($arg)) {
                $valid = true;
            }
        }
        if (!$valid) {
            throw new InvalidFormatException();
        }

        foreach ($argv as $index => $arg) {
            if ($index % 2 == 0) {
                $ix = new Prefix();
            } else {
                $ix = new Suffix();
            }
            $ix->addSymbolsFromString($arg);
            $this->ixArray[$index] = $ix;
        }
    }

    /**
     * @function setPositivePrefix
     * @param Prefix $prefix
     */
    public function setPositivePrefix(Prefix $prefix) : void
    {
        $this->ixArray[0] = $prefix;
    }

    /**
     * @function setPositiveSuffix
     * @param Suffix $suffix
     */
    public function setPositiveSuffix(Suffix $suffix) : void
    {
        $this->ixArray[1] = $suffix;
    }

    /**
     * @function setNegativePrefix
     * @param Prefix $prefix
     */
    public function setNegativePrefix(Prefix $prefix) : void
    {
        $this->ixArray[2] = $prefix;
    }

    /**
     * @function setNegativeSuffix
     * @param Suffix $suffix
     */
    public function setNegativeSuffix(Suffix $suffix) : void
    {
        $this->ixArray[3] = $suffix;
    }

    /**
     * @function getPositivePrefix
     * @return Prefix
     */
    public function getPositivePrefix(): Prefix
    {
        return $this->ixArray[0];
    }

    /**
     * @function getPositivePrefixString
     * @return string
     */
    public function getPositivePrefixString(): string
    {
        $ix = $this->ixArray[0];
        return $ix->getIx();
    }

    /**
     * @function getPositiveSuffix
     * @return Suffix
     */
    public function getPositiveSuffix(): Suffix
    {
        return $this->ixArray[1];
    }

    /**
     * @function getPositiveSuffixString
     * @return string
     */
    public function getPositiveSuffixString(): string
    {
        $ix = $this->ixArray[1];
        return $ix->getIx();
    }

    /**
     * @function getNegativePrefix
     * @return Prefix
     */
    public function getNegativePrefix(): Prefix
    {
        return $this->ixArray[2];
    }

    /**
     * @function getNegativePrefixString
     * @return string
     */
    public function getNegativePrefixString(): string
    {
        $ix = $this->ixArray[2];
        return $ix->getIx();
    }

    /**
     * @function getNegativeSuffix
     * @return Suffix
     */
    public function getNegativeSuffix(): Suffix
    {
        return $this->ixArray[3];
    }

    /**
     * @function getNegativeSuffixString
     * @return string
     */
    public function getNegativeSuffixString(): string
    {
        $ix = $this->ixArray[3];
        return $ix->getIx();
    }

    /**
     * @function getAllSymbols
     * @return array
     */
    public function getAllSymbols(): array
    {
        $result = [];
        foreach ($this->ixArray as $ix) {
            $result = array_merge($result, $ix->getSymbols());
        }
        return $result;
    }

    /**
     * @function merge
     * @param IxSet $ixset
     */
    public function merge(IxSet $ixset) : void
    {
        $x = $ixset->getIx();
        for ($i = 0; $i < 4; $i++) {
            $ix = $this->ixArray[$i];
            $ix->merge($x[$i]);
        }
    }

    /**
     * @function configureOptions
     * @param NumberFormatter $frmtr
     * @return bool
     */
    public function configureOptions(NumberFormatter $frmtr): bool
    {
        // configure any symbol substitutions
        $valid = $this->configureSymbolOptionSet($frmtr);

        // just use the positive subpattern to create both the positive and negative subpatterns
        // with the new prefixes / suffixes
        $pattern = explode(';', $frmtr->getPattern());
        $positiveSubpattern = $this->getPositivePrefixString() . $pattern[0] . $this->getPositiveSuffixString();
        $negativeSubpattern = $this->getNegativePrefixString() . $pattern[0] . $this->getNegativeSuffixString();

        $newPattern = $positiveSubpattern . ';' . $negativeSubpattern;
        $valid = $valid && $frmtr->setPattern($newPattern);

        return $valid;
    }
}
