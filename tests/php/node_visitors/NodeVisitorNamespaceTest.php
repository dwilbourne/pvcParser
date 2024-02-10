<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace php\node_visitors;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use pvc\parser\php\node_visitors\NodeVisitorNamespace;

class NodeVisitorNamespaceTest extends TestCase
{
    /**
     * testNodeVisitorNamespaceWithNamespaceNode
     * @covers \pvc\parser\php\node_visitors\NodeVisitorNamespace::enterNode
     * @covers \pvc\parser\php\node_visitors\NodeVisitorNamespace::getNamespace
     */
    public function testNodeVisitorNamespaceWithNamespaceNode(): void
    {
        $node = $this->createMock(Namespace_::class);
        $visitor = new NodeVisitorNamespace();
        $expectedResult = NodeTraverser::STOP_TRAVERSAL;
        self::assertEquals($expectedResult, $visitor->enterNode($node));
        self::assertSame($node, $visitor->getNamespace());
    }

    /**
     * testNodeVisitorNamespaceWithOtherNode
     * @covers \pvc\parser\php\node_visitors\NodeVisitorNamespace::enterNode
     */
    public function testNodeVisitorNamespaceWithOtherNode(): void
    {
        $node = $this->createMock(Class_::class);
        $visitor = new NodeVisitorNamespace();
        self::assertNull($visitor->enterNode($node));
    }
}
