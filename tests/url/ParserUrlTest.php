<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\url;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\http\url\Url;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\url\ParserUrl;

/**
 * Class ParserUrlTest
 */
class ParserUrlTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected ParserUrl $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new ParserUrl($this->msg);
    }

    /**
     * testParseUrlStringBasic
     * @covers \pvc\parser\url\ParserUrl::parseValue
     */
    public function testParseUrlStringBasic(): void
    {
        $urlString = 'http://username:password@hostname:9090/some/path?arg=value#anchor';
        $values = [
            'scheme' => 'http',
            'host' => 'hostname',
            'port' => '9090',
            'user' => 'username',
            'pass' => 'password',
            'path' => '/some/path',
            'query' => 'arg=value',
            'fragment' => 'anchor'
        ];
        self::assertTrue($this->parser->parse($urlString));
        self::assertInstanceOf(Url::class, $this->parser->getParsedValue());
    }

    /**
     * testMissingUrlParts
     * @covers \pvc\parser\url\ParserUrl::parseValue
     */
    public function testMissingUrlParts(): void
    {
        $urlString = '//www.example.com/path?googleguy=googley';
        self::assertTrue($this->parser->parse($urlString));
        $url = $this->parser->getParsedValue();

        $expectedHost = 'www.example.com';
        $expectedPath = '/path';
        $expectedQuery = 'googleguy=googley';

        self::assertNull($url->getScheme());
        self::assertEquals($expectedHost, $url->getHost());
        self::assertEquals($expectedPath, $url->getPath());
        self::assertEquals($expectedQuery, $url->getQuery());
    }

    /**
     * testBadlyFormedUrls
     * @covers \pvc\parser\url\ParserUrl::parseValue
     * @covers \pvc\parser\url\ParserUrl::setMsgContent
     */
    public function testBadlyFormedUrls(): void
    {
        $this->msg->expects($this->exactly(3))->method('setContent');
        $badUrls = ['http:///example.com', 'http://:80', 'http://user@:80'];
        foreach ($badUrls as $badUrl) {
            self::assertFalse($this->parser->parse($badUrl));
        }
    }

    /**
     * testReservedChars
     * @covers \pvc\parser\url\ParserUrl::parseValue
     */
    public function testReservedChars(): void
    {
        $reservedChars = ['!', '*', '\'', '(', ')', ':', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']'];

        // reserved chars in the path component
        $urlString = 'http://www.somehost.com/' . implode($reservedChars);

        self::assertTrue($this->parser->parse($urlString));
        $url = $this->parser->getParsedValue();

        self::assertEquals('http', $url->getScheme());
        self::assertEquals('www.somehost.com', $url->getHost());

        // the '?' which is four characters from the end of the reserved characters array is interpreted as the
        // delimiter for a querystring.

        $expected = '/' . implode(array_slice($reservedChars, 0, array_search('?', $reservedChars)));
        self::assertEquals($expected, $url->getPath());
    }


    /**
     * testMultibyteCharInPath
     * @covers \pvc\parser\url\ParserUrl::parseValue
     *
     * not all parsers can deal with multibyte characters in the string.
     */
    public function testMultibyteCharInPath(): void
    {
        // \u{263A} is a smiley face.  Note that in Windows you MUST use double quotes (not single quotes)
        // as the string delimiter in order to use unicode codepoints
        $pathWithMultibyteCharSmileyFace = '/Hello/World' . "\u{263A}";

        // the path has 13 characters: 12 plus the smiley face
        self::assertEquals(13, mb_strlen($pathWithMultibyteCharSmileyFace));

        $urlString = 'http://www.nowhere.com' . $pathWithMultibyteCharSmileyFace;
        self::assertTrue($this->parser->parse($urlString));
        $url = $this->parser->getParsedValue();
        self::assertEquals($pathWithMultibyteCharSmileyFace, $url->getPath());
    }

    /**
     * testIllegalCharacters
     * @covers \pvc\parser\url\ParserUrl::parseValue
     */
    public function testIllegalCharacters(): void
    {
        $scheme = 'http://';
        $hostFirstPart = 'no';
        $hostLastPart = 'where.com';
        // "BELL" control character - illegal
        $illegalChar = chr(0x7);

        $urlString = $scheme . $hostFirstPart . $illegalChar . $hostLastPart;

        // documentation on parse_url says illegal characters are replaced by '_'.
        $expectedHost = $hostFirstPart . '_' . $hostLastPart;

        self::assertTrue($this->parser->parse($urlString));
        $url = $this->parser->getParsedValue();
        self::assertEquals($expectedHost, $url->getHost());
    }
}
