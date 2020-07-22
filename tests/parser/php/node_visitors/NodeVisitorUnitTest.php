<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\php\node_visitors;

use Mockery;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use pvc\parser\php\node_visitors\NodeVisitorFirstClass;

class NodeVisitorUnitTest extends TestCase
{
    public function testNodeVisitorFirstClassWithClassNode() : void
    {
        $node = Mockery::mock(Class_::class);
        $visitor = new \pvc\parser\php\node_visitors\NodeVisitorFirstClass();
        $expectedResult = NodeTraverser::STOP_TRAVERSAL;
        self::assertEquals($expectedResult, $visitor->enterNode($node));
        self::assertSame($node, $visitor->getClassNode());
    }

    public function testNodeVisitorFirstClassWithOtherNode() : void
    {
        $node = Mockery::mock(Interface_::class);
        $visitor = new NodeVisitorFirstClass();
        self::assertNull($visitor->enterNode($node));
    }

    public function testNodeVisitorNamespaceWithNamespaceNode() : void
    {
        $node = Mockery::mock(Namespace_::class);
        $visitor = new \pvc\parser\php\node_visitors\NodeVisitorNamespace();
        $expectedResult = NodeTraverser::STOP_TRAVERSAL;
        self::assertEquals($expectedResult, $visitor->enterNode($node));
        self::assertSame($node, $visitor->getNamespace());
    }

    public function testNodeVisitorNamespaceWithOtherNode() : void
    {
        $node = Mockery::mock(Class_::class);
        $visitor = new \pvc\parser\php\node_visitors\NodeVisitorNamespace();
        self::assertNull($visitor->enterNode($node));
    }
}
