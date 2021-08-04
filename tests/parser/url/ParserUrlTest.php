<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\parser\url;

use PHPUnit\Framework\TestCase;
use pvc\parser\url\ParserUrl;
use pvc\url\Url;

/**
 * Class ParserUrlTest
 * @package tests\parser\url
 */
class ParserUrlTest extends TestCase
{
    protected ParserUrl $parser;

    public function setUp(): void
    {
        $this->parser = new ParserUrl();
    }

    /**
     * testParseUrlStringBasic
     */
    public function testParseUrlStringBasic(): void
    {
        $urlString = 'http://username:password@hostname:9090/some/path?arg=value#anchor';
        $values = array(
            'scheme' => 'http',
            'host' => 'hostname',
            'port' => '9090',
            'user' => 'username',
            'pass' => 'password',
            'path' => '/some/path',
            'query' => 'arg=value',
            'fragment' => 'anchor'
        );
        $expectedResult = new Url($values);
        $result = $this->parseUrl($urlString);
        self::assertEquals($expectedResult, $result);
    }

    private function parseUrl(string $url): Url
    {
        self::assertTrue($this->parser->parse($url));
        return $this->parser->getParsedValue();
    }

    public function testMissingUrlParts(): void
    {
        $urlString = '//www.example.com/path?googleguy=googley';
        $url = $this->parseUrl($urlString);

        $expectedHost = 'www.example.com';
        $expectedPath = '/path';
        $expectedQuery = ['googleguy' => 'googley'];

        self::assertNull($url->getScheme());
        self::assertEquals($expectedHost, $url->getHost());
        self::assertEquals($expectedPath, $url->getPath());
        self::assertEquals($expectedQuery, $url->getQuery());
    }

    public function testBadlyFormedUrls(): void
    {
        $badUrls = ["http:///example.com", "http://:80", "http://user@:80"];
        foreach ($badUrls as $badUrl) {
            self::assertFalse($this->parser->parse($badUrl));
        }
    }

    public function testReservedChars(): void
    {
        $reservedChars = ['!', '*', '\'', '(', ')', ':', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']'];

        // reserved chars in the path component
        $urlString = 'http://www.somehost.com/' . implode($reservedChars);

        $result = parse_url($urlString);

        self::assertEquals('http', $result['scheme']);
        self::assertEquals('www.somehost.com', $result['host']);

        // the '?' which is four characters from the end of the reserved characters array is interpreted as the
        // delimiter for a querystring.

        $expected = '/' . implode(array_slice($reservedChars, 0, array_search('?', $reservedChars)));
        self::assertEquals($expected, parse_url($urlString, PHP_URL_PATH));
    }


    /**
     * testMultibyteCharInPath
     * not all parsers can deal with multibyte characters in the string.
     */
    public function testMultibyteCharInPath(): void
    {
        // \u{263A} is a smiley face.  Note that in Windows you MUST use double quotes (not single quotes)
        // as the string delimiter in order to use unicode codepoints
        $pathWithMultibyteCharSmileyFace = "/Hello/World" . "\u{263A}";

        // the path has 13 characters: 12 plus the smiley face
        self::assertEquals(13, mb_strlen($pathWithMultibyteCharSmileyFace));

        $urlString = 'http://www.nowhere.com' . $pathWithMultibyteCharSmileyFace;
        $result = $this->parseUrl($urlString);
        self::assertEquals($pathWithMultibyteCharSmileyFace, $result->getPath());
    }

    public function testIllegalCharacters(): void
    {
        $scheme = 'http://';
        $hostFirstPart = 'no';
        $hostLastPart = 'where.com';
        // "BELL" control character - illegal
        $illegalChar = chr(0x7);

        $illegalUrl = $scheme . $hostFirstPart . $illegalChar . $hostLastPart;

        // documentation on parse_url says illegal characters are replaced by '_'.
        $expectedHost = $hostFirstPart . '_' . $hostLastPart;
        $url = $this->parseUrl($illegalUrl);
        self::assertEquals($expectedHost, $url->getHost());
    }


}
