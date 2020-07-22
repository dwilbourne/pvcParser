<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\configurations_pvc;

use pvc\parser\numeric\NumberParser\configurations\NumberParserCurrencyConfiguration;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\CurrencyIxBlockPvc;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PlusMinusIxBlockPvc;
use pvc\parser\numeric\NumberParser\core\err\InvalidConfigurationSetException;

/**
 * Class NumberParserCurrencyConfigurationPvc
 */
class NumberParserCurrencyConfigurationPvc extends NumberParserCurrencyConfiguration
{

    /**
     * NumberParserCurrencyConfigurationPvc constructor.
     * @param bool $allowPureDecimals
     * @throws InvalidConfigurationSetException $allowPureDecimals controls whether a number without any
     * currency symbol at all can be parsed as currency
     * (e.g. is '123' ok?  $allowPureDecimals=true makes it OK).
     * @throws \pvc\array_utils\CartesianProduct\CartesianProductException
     */
    public function __construct(bool $allowPureDecimals = false)
    {
        parent::__construct();

        $plusMinusBlock = new PlusMinusIxBlockPvc();
        $currencyBlock = new CurrencyIxBlockPvc();
        $plusMinusBlock->merge($currencyBlock);

        if ($allowPureDecimals) {
            $decimals = new PlusMinusIxBlockPvc();
            foreach ($decimals as $ixSet) {
                /* somehow phpstan sees $ixset as possibly being null..? */
                /** @phpstan-ignore-next-line */
                $plusMinusBlock->addConfigurationSet($ixSet);
            }
        }

        $this->addConfigurationBlock($plusMinusBlock);
    }
}
