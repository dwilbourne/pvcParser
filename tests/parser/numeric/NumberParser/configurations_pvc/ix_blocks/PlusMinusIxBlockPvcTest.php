<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations_pvc\ix_blocks;

use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PlusMinusIxBlockPvc;
use PHPUnit\Framework\TestCase;

class PlusMinusIxBlockPvcTest extends TestCase
{
    public function testConstruct() : void
    {
        $plusMinusIxBlockPvc = new PlusMinusIxBlockPvc();
        self::assertEquals(4, count($plusMinusIxBlockPvc));
    }
}
