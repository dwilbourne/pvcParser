<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\php\node_visitors;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use pvc\parser\php\node_visitors\NodeVisitorFirstClass;
use pvc\parser\php\node_visitors\NodeVisitorNamespace;
use Throwable;

class NodeVisitorIntegrationTest extends TestCase
{
    protected ?array $nodes;

    public function setUp(): void
    {
        // parse myself
        $code = file_get_contents(__FILE__) ?: '';
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $this->nodes = $parser->parse($code);
        } catch (Throwable $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }
    }

    public function testNodeVisitorNamespace(): void
    {
        $traverser = new NodeTraverser();
        $namespaceVisitor = new NodeVisitorNamespace();
        $traverser->addVisitor($namespaceVisitor);
        /* phpstan complains that $this->nodes could be null */
        /** @phpstan-ignore-next-line */
        $traverser->traverse($this->nodes);
        self::assertEquals(__NAMESPACE__, (string)$namespaceVisitor->getNamespace()->name);
    }

    public function testNodeVisitorFirstClass(): void
    {
        $traverser = new NodeTraverser();
        $nameResolver = new NameResolver();
        $classVisitor = new NodeVisitorFirstClass();
        $traverser->addVisitor($nameResolver);
        $traverser->addVisitor($classVisitor);
        /* phpstan complains that $this->nodes could be null */
        /** @phpstan-ignore-next-line */
        $traverser->traverse($this->nodes);
        /** @phpstan-ignore-next-line */
        self::assertEquals(__CLASS__, (string)$classVisitor->getClassNode()->namespacedName);
    }
}
