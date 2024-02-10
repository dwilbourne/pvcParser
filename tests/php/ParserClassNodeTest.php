<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\php;

use PHPUnit\Framework\TestCase;
use pvc\parser\err\NonExistentFilePathException;
use pvc\parser\php\ParserClassNode;
use pvcTests\parser\php\fixtures\baz;

class ParserClassNodeTest extends TestCase
{
    protected ParserClassNode $parser;

    public function setUp(): void
    {
        $this->parser = new ParserClassNode();
    }

    /**
     * testParseBadFilename
     * @throws NonExistentFilePathException
     * @covers \pvc\parser\php\ParserClassNode::parse
     */
    public function testParseBadFilename(): void
    {
        $filename = 'foo.php';
        self::expectException(NonExistentFilePathException::class);
        // suppress the E_WARNING that comes back when the file is not found
        $node = @$this->parser->parse($filename);
    }

    /**
     * testParseFileWithNoClass
     * @throws NonExistentFilePathException
     * @covers \pvc\parser\php\ParserClassNode::parse
     */
    public function testParseFileWithNoClass(): void
    {
        $fixture = __DIR__ . '/fixtures/foo.php';
        self::assertNull($this->parser->parse($fixture));
    }

    /**
     * testParseFileClassWithNoNamespace
     * @throws NonExistentFilePathException
     * @covers \pvc\parser\php\ParserClassNode::parse
     * test to make sure the 'namespacedName' attribute is still set for a class that has no namespace.
     */
    public function testParseFileClassWithNoNamespace(): void
    {
        $fixture = __DIR__ . '/fixtures/bar.php';
        $node = $this->parser->parse($fixture);
        if (!is_null($node)) {
            self::assertEquals('pvcTests\parser\php\fixtures\bar', $node->namespacedName);
        }
    }

    /**
     * testParseFileClassWithNamespace
     * @throws NonExistentFilePathException
     * @covers \pvc\parser\php\ParserClassNode::parse
     */
    public function testParseFileClassWithNamespace(): void
    {
        $fixture = __DIR__ . '/fixtures/baz.php';
        $node = $this->parser->parse($fixture);
        if (!is_null($node)) {
            self::assertEquals(baz::class, $node->namespacedName);
        }
    }

    /**
     * testParseFileWithNoNodes
     * @throws NonExistentFilePathException
     * @covers \pvc\parser\php\ParserClassNode::parse
     */
    public function testParseFileWithNoNodes(): void
    {
        $fixture = __DIR__ . '/fixtures/null.php';
        self::assertNull($this->parser->parse($fixture));
    }
}
