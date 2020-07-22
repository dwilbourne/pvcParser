<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations_pvc\ix_blocks;

use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\CurrencyIxBlockPvc;

class CurrencyIxBlockPvcTest extends TestCase
{
    public function testConstruct() : void
    {
        $currencyIxBlockPvc = new CurrencyIxBlockPvc();
        self::assertEquals(4, count($currencyIxBlockPvc));
    }
}
