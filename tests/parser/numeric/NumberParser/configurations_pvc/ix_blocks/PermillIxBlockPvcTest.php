<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\configurations_pvc\ix_blocks;

use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\configurations_pvc\ix_blocks\PermillIxBlockPvc;

class PermillIxBlockPvcTest extends TestCase
{
    public function testConstruct() : void
    {
        $permillIxBlockPvc = new PermillIxBlockPvc();
        self::assertEquals(1, count($permillIxBlockPvc));
    }
}
