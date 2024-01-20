<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\dom;

use PHPUnit\Framework\TestCase;
use pvc\parser\dom\MarkupSniffer;

class MarkupSnifferTest extends TestCase
{

    protected string $fixtures_dir = __DIR__ . "/fixtures/";

    /**
     *
     * Strip comments tests.
     *
     */
    public function testStripComments(): void
    {
        $testString = "<!DOCTYPE html><!-- this is a legitimate comment --><html><p>some text</p></html>";
        $ms = new \pvc\parser\dom\MarkupSniffer();
        $expectedResult = "<!DOCTYPE html><html><p>some text</p></html>";
        static::assertSame($expectedResult, $ms->stripComments($testString));

        $testString = "<!-- this appears at the beginning of the string -->";
        $testString .= "<!DOCTYPE html><html><p>some text</p></html>";
        $ms = new MarkupSniffer();
        $expectedResult = "<!DOCTYPE html><html><p>some text</p></html>";
        static::assertSame($expectedResult, $ms->stripComments($testString));

        $testString = "<!DOCTYPE html><html><p>some text</p></html><!-- this appears at the end of the string -->";
        $ms = new MarkupSniffer();
        $expectedResult = "<!DOCTYPE html><html><p>some text</p></html>";
        static::assertSame($expectedResult, $ms->stripComments($testString));

        $testString = "<!DOCTYPE html><html<!-- this wrongly appears in the middle of a tag ";
        $testString .= "but is stripped anyway -->><p>some text</p></html>";
        $ms = new MarkupSniffer();
        $expectedResult = "<!DOCTYPE html><html><p>some text</p></html>";
        static::assertSame($expectedResult, $ms->stripComments($testString));
    }

    /**
     *
     * Correctly discovers doctype based on doctype declaration.
     *
     * Correctly fails to determine doctype if there is no doctype declaration or the declaration does
     * not contain the string 'xml' or 'html'
     *
     */

    /**
     * @function testDiscoverDocType
     * @param string $filename
     * @param string|null $expectedValue
     * @throws \pvc\regex\err\RegexBadPatternException
     * @throws \pvc\regex\err\RegexInvalidMatchIndexException
     * @throws \pvc\regex\err\RegexPatternUnsetException
     * @dataProvider doctypeProvider
     */
    public function testSniffDocType(string $filename, string $expectedValue = null): void
    {
        $fileContents = (file_get_contents($this->fixtures_dir . $filename) ?: '');
        $ms = new \pvc\parser\dom\MarkupSniffer();
        $ms->sniff($fileContents);
        static::assertEquals($expectedValue, $ms->getMarkupLanguage());
    }

    public function doctypeProvider(): array
    {
        return [
            'html is invalid but doctype declaration ok' => ['document-invalid-html4.html', 'html'],
            'valid html4' => ['document-valid-html4-with-remote-DTD-reference.html', 'html'],
            'valid xml' => ['xml-valid-DTD-external-local.xml', 'xml'],
            'no dtd in the xml - get from opening tag' => ['xml-invalid-basic.xml', 'xml'],
            'no dtd in the html - get from opening tag' => ['document-valid-html4-no-doctype.html', 'html'],
            'no doctype no definitive opening tag' => ['document-valid-xml-without-declaration.xml', null],
        ];
    }


    /**
     * @function testSniffCharset
     * @param string $filename
     * @param string $expectedValue
     * @throws \pvc\regex\err\RegexBadPatternException
     * @throws \pvc\regex\err\RegexInvalidMatchIndexException
     * @throws \pvc\regex\err\RegexPatternUnsetException
     * @dataProvider charsetProvider
     */
    public function testSniffCharset(string $filename, string $expectedValue = null): void
    {
        $fileContents = (file_get_contents($this->fixtures_dir . $filename) ?: '');
        $ms = new MarkupSniffer();
        $ms->sniff($fileContents);
        if (is_null($expectedValue)) {
            self::assertSame($expectedValue, $ms->getCharset());
        } else {
            /** @phpstan-ignore-next-line */
            self::assertEquals($expectedValue, $ms->getCharset()->getCharsetString());
        }
    }

    public function charsetProvider(): array
    {
        return [
            'no charset declared' => ['document-invalid-html4.html', null],
            'charset declared via meta tag' => ['document-invalid-utf8-html5.html', 'UTF-8'],
            'another meta tag' => ['document-valid-html4-no-doctype.html', 'UTF-8'],
            'xml document invalid' => ['xml-invalid-basic.xml', 'UTF-8'],
            'another invalid xml document' => ['document-invalid-xml.xml', 'UTF-8'],

        ];
    }
}
