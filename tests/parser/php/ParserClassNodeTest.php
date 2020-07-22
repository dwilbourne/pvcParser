<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\php;

use PHPUnit\Framework\TestCase;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\parser\php\ParserClassNode;
use tests\parser\file\php\fixtures\baz;

class ParserClassNodeTest extends TestCase
{
    protected \pvc\parser\php\ParserClassNode $parser;

    public function setUp() : void
    {
        $this->parser = new ParserClassNode();
    }

    public function testParseBadFilename() : void
    {
        $filename = 'foo.php';
        self::expectException(InvalidArgumentException::class);
        // suppress the E_WARNING that comes back when the file is not found
        $node = @$this->parser->parse($filename);
    }

    public function testParseFileWithNoClass() : void
    {
        $fixture = __DIR__ . '/fixtures/foo.php';
        self::assertNull($this->parser->parse($fixture));
    }

    /**
     * test to make sure the 'namespacedName' attribute is still set for a class that has no namespace.
     */
    public function testParseFileClassWithNoNamespace() : void
    {
        $fixture = __DIR__ . '/fixtures/bar.php';
        $node = $this->parser->parse($fixture);
        if (!is_null($node)) {
            self::assertEquals('bar', $node->namespacedName);
        }
    }

    public function testParseFileClassWithNamespace() : void
    {
        $fixture = __DIR__ . '/fixtures/baz.php';
        $node = $this->parser->parse($fixture);
        if (!is_null($node)) {
            self::assertEquals(baz::class, $node->namespacedName);
        }
    }

    public function testParseFileWithNoNodes() : void
    {
        $fixture = __DIR__ . '/fixtures/null.php';
        self::assertNull($this->parser->parse($fixture));
    }
}
