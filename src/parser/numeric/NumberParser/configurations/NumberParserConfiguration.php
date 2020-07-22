<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations;

use NumberFormatter;
use pvc\array_utils\CartesianProduct\CartesianProduct;
use pvc\intl\Locale;
use pvc\parser\numeric\NumberParser\core\ConfigurationBlock;
use pvc\parser\numeric\NumberParser\core\NumberFormatterConfiguration;

/**
 * Class NumberParserConfiguration
 */
class NumberParserConfiguration
{
    /**
     * @var int
     */
    protected int $formatterStyle;
    protected int $formatterType;

    protected array $configurationBlocks = [];


    public function __construct(int $frmtrStyle, int $frmtrType)
    {
        $this->setFormatterStyle($frmtrStyle);
        $this->setFormatterType($frmtrType);
    }

    public function setFormatterStyle(int $style): void
    {
        $this->formatterStyle = $style;
    }

    public function getFormatterStyle(): int
    {
        return $this->formatterStyle;
    }

    public function getFormatterConstructionStyle(): int
    {
        return ($this->getFormatterStyle(
        ) == NumberFormatter::CURRENCY ? NumberFormatter::DECIMAL : $this->getFormatterStyle());
    }

    public function setFormatterType(int $type): void
    {
        $this->formatterType = $type;
    }

    public function getFormatterType(): int
    {
        return $this->formatterType;
    }

    // currency symbols are explicit in the option sets so the base pattern should not include any currency symbol.
    // so although we keep the style handy (in particular so we can identify the correct decimal and grouping
    // separators), we return a formatter with the type of decimal when the desired type is currency.
    public function getNumberFormatter(Locale $locale): NumberFormatter
    {
        return new NumberFormatter((string) $locale, $this->getFormatterConstructionStyle());
    }

    public function addConfigurationBlock(ConfigurationBlock $cb) : void
    {
        $this->configurationBlocks[] = $cb;
    }

    public function getConfigurationBlocks(): array
    {
        return $this->configurationBlocks;
    }

    public function getNumberFormatterConfiguration(): array
    {
        $result = [];

        // the CartesianProduct object implements Iterator and so it behaves like
        // an array, meaning we can run it through a foreach loop to get configurations

        $configSetsArray = new CartesianProduct($this->configurationBlocks);
        foreach ($configSetsArray as $configSets) {
            $nfc = new NumberFormatterConfiguration();
            $nfc->addConfigSets($configSets);
            $result[] = $nfc;
        }
        return $result;
    }
}
