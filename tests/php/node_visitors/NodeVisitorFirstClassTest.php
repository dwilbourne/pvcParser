<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvcTests\parser\php\node_visitors;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use pvc\parser\php\node_visitors\NodeVisitorFirstClass;

class NodeVisitorFirstClassTest extends TestCase
{
    /**
     * testNodeVisitorFirstClassWithClassNode
     * @covers \pvc\parser\php\node_visitors\NodeVisitorFirstClass::enterNode
     * @covers \pvc\parser\php\node_visitors\NodeVisitorFirstClass::getClassNode
     */
    public function testNodeVisitorFirstClassWithClassNode(): void
    {
        $node = $this->createMock(Class_::class);
        $visitor = new NodeVisitorFirstClass();
        $expectedResult = NodeTraverser::STOP_TRAVERSAL;
        self::assertEquals($expectedResult, $visitor->enterNode($node));
        self::assertSame($node, $visitor->getClassNode());
    }

    /**
     * testNodeVisitorFirstClassWithOtherNode
     * @covers \pvc\parser\php\node_visitors\NodeVisitorFirstClass::enterNode
     */
    public function testNodeVisitorFirstClassWithOtherNode(): void
    {
        $node = $this->createMock(Interface_::class);
        $visitor = new NodeVisitorFirstClass();
        self::assertNull($visitor->enterNode($node));
    }
}
