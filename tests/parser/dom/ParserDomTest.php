<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\dom;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use pvc\msg\MsgRetrievalInterface;
use pvc\parser\dom\err\InvalidMarkupLanguageException;
use pvc\parser\dom\err\UnknownMarkupLanguageMsg;

class ParserDomTest extends TestCase
{

    protected \pvc\parser\dom\ParserDOM $parser;
    protected string $fixtures_dir = __DIR__ . "/fixtures";

    public function setUp(): void
    {
        $this->parser = new \pvc\parser\dom\ParserDOM();
    }

    public function testSetGetMarkupLanguage(): void
    {
        $this->parser->setMarkupLanguage('xml');
        self::assertEquals('xml', $this->parser->getMarkupLanguage());
        $this->parser->setMarkupLanguage('html');
        self::assertEquals('html', $this->parser->getMarkupLanguage());
    }

    public function testSetInvalidMarkupLanguage(): void
    {
        self::expectException(InvalidMarkupLanguageException::class);
        $this->parser->setMarkupLanguage('foo');
    }

    public function testSetGetFailureThreshold(): void
    {
        self::assertEquals(LIBXML_ERR_WARNING, $this->parser->getFailureThreshold());
        $this->parser->setFailureThreshold(LIBXML_ERR_ERROR);
        self::assertEquals(LIBXML_ERR_ERROR, $this->parser->getFailureThreshold());
    }

    public function testUnknownMarkupLanguageException(): void
    {
        $filename = $this->fixtures_dir . "/figure.svg";
        $string = (file_get_contents($filename) ?: '');
        self::assertFalse($this->parser->parse($string));
        self::assertTrue($this->parser->getErrmsg() instanceof UnknownMarkupLanguageMsg);
    }

    public function testGoodHTML4AndGetMediaType(): void
    {
        $filename = $this->fixtures_dir . "/document-valid-html4.html";
        $string = (file_get_contents($filename) ?: '');
        // loading the document indicates it is "reasonably well-formed" as the HTML parser is more forgiving
        // than the XML parser
        self::assertTrue($this->parser->parse($string));
        self::assertNull($this->parser->getErrmsg());
        self::assertEquals('text/html; charset=us-ascii', $this->parser->getMediaType());
        self::assertEquals('UTF-8', (string)$this->parser->getCharset());
    }

    /**
     * This one has a closing span tag that was never opened as well as an unclosed list item and div tag.  These are
     * recoverable errors so the file is "parsed successfully" but it has error messagesFilter.
     *
     */
    public function testParseBadHTML4(): void
    {
        $filename = $this->fixtures_dir . "/document-invalid-html4.html";
        $string = (file_get_contents($filename) ?: '');

        // default setting is to fail on any sort of warning or error
        static::assertFalse($this->parser->parse($string));
        self::assertInstanceOf(MsgRetrievalInterface::class, $this->parser->getErrmsg());
        self::assertNull($this->parser->getParsedValue());

        // now set failureThreshold to fatal
        $handler = $this->parser->getErrorHandler();
        $handler->setFailureThreshold(LIBXML_ERR_FATAL);
        static::assertTrue($this->parser->parse($string));
        self::assertTrue($this->parser->getParsedValue() instanceof DOMDocument);
    }

    public function testParseGoodXML(): void
    {
        $filename = $this->fixtures_dir . "/xml-valid-basic-no-schema-reference.xml";
        $string = (file_get_contents($filename) ?: '');
        $handler = $this->parser->getErrorHandler();

        // no fatal errors
        $handler->setFailureThreshold(LIBXML_ERR_FATAL);
        self::assertTrue($this->parser->parse($string));

        // no errors
        $handler->setFailureThreshold(LIBXML_ERR_FATAL);
        self::assertTrue($this->parser->parse($string));

        // no warnings
        $handler->setFailureThreshold(LIBXML_ERR_WARNING);
        self::assertTrue($this->parser->parse($string));
    }

    public function testParseBadXML(): void
    {
        $filename = $this->fixtures_dir . "/xml-invalid-basic.xml";
        $string = (file_get_contents($filename) ?: '');

        static::assertFalse($this->parser->parse($string));

        // inspect the error
        $handler = $this->parser->getErrorHandler();
        $errors = $handler->getErrors();
        self::assertEquals(1, count($errors));
        $error = $errors[0];
        self::assertStringStartsWith('Opening and ending tag mismatch:', $error->message);
    }
}
