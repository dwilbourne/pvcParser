<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\prefix_suffix;

use pvc\parser\numeric\NumberParser\core\NumberFormatterOptionSet;
use pvc\parser\numeric\NumberParser\prefix_suffix\IxBlock;
use PHPUnit\Framework\TestCase;
use pvc\parser\numeric\NumberParser\prefix_suffix\IxSet;

class IxBlockTest extends TestCase
{
    protected IxBlock $ixBlockA;
    protected IxBlock $ixBlockB;

    public function setUp(): void
    {
        $pmSet1 = new IxSet();
        $pmSet1->setFormat('+', '', '-', '');

        $pmSet2 = new IxSet();
        $pmSet2->setFormat('', '+', '', '-');

        $this->ixBlockA = new IxBlock();
        $this->ixBlockA->addConfigurationSet($pmSet1);
        $this->ixBlockA->addConfigurationSet($pmSet2);

        $pmSet1 = new IxSet();
        $pmSet1->setFormat('¤', '', '¤', '');

        $pmSet2 = new IxSet();
        $pmSet2->setFormat('', '¤¤', '', '¤¤');

        $this->ixBlockB = new IxBlock();
        $this->ixBlockB->addConfigurationSet($pmSet1);
        $this->ixBlockB->addConfigurationSet($pmSet2);
    }

    public function testValidateConfigurationSet() : void
    {
        $block = new IxBlock();

        // these are both instances of ConfigurationSet but not of IxSet
        $goodSet = new IxSet();
        self::assertTrue($block->validateConfigurationSet($goodSet));

        $badSet = new NumberFormatterOptionSet();
        self::assertFalse($block->validateConfigurationSet($badSet));
    }

    public function testMerge() : void
    {
        $this->ixBlockA->merge($this->ixBlockB);
        self::assertEquals(4, count($this->ixBlockA));

        $ixSets = $this->ixBlockA->getConfigurationSet();

        $ixSetA = $ixSets[0];
        self::assertEquals('+¤', $ixSetA->getPositivePrefix()->getIx());
        self::assertEquals('-¤', $ixSetA->getNegativePrefix()->getIx());

        $ixSetB = $ixSets[3];
        self::assertEquals('¤¤+', $ixSetB->getPositiveSuffix()->getIx());
        self::assertEquals('¤¤-', $ixSetB->getNegativeSuffix()->getIx());
    }
}
