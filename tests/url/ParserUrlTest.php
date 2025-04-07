<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\url;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\http\UrlInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\interfaces\validator\ValTesterInterface;
use pvc\parser\url\ParserUrl;

/**
 * Class ParserUrlTest
 */
class ParserUrlTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected UrlInterface $url;

    protected ParserUrl $parser;

    protected ValTesterInterface|MockObject $urlTester;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->url = $this->createMock(UrlInterface::class);
        $this->urlTester = $this->createMock(ValTesterInterface::class);
        $this->parser = new ParserUrl($this->msg, $this->url, $this->urlTester);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\url\ParserUrl::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ParserUrl::class, $this->parser);
    }

    /**
     * testSetGetUrl
     * @covers \pvc\parser\url\ParserUrl::setUrl
     * @covers \pvc\parser\url\ParserUrl::getUrl
     */
    public function testSetGetUrl(): void
    {
        $url = $this->createMock(UrlInterface::class);
        $this->parser->setUrl($url);
        self::assertEquals($url, $this->parser->getUrl());
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
        $this->url->expects($this->once())->method('setAttributesFromArray')->with($values);
        $this->urlTester->method('testValue')->willReturn(true);
        self::assertTrue($this->parser->parse($urlString));
    }

    /**
     * testMissingUrlParts
     * @covers \pvc\parser\url\ParserUrl::parseValue
     */
    public function testMissingUrlParts(): void
    {
        $urlString = '//www.example.com/path?googleguy=googley';
        $values = [
            'host' => 'www.example.com',
            'path' => '/path',
            'query' => 'googleguy=googley',
        ];
        $this->url->expects($this->once())->method('setAttributesFromArray')->with($values);
        $this->urlTester->method('testValue')->willReturn(true);
        self::assertTrue($this->parser->parse($urlString));
    }

    /**
     * testBadlyFormedUrls
     * @covers \pvc\parser\url\ParserUrl::parseValue
     * @covers \pvc\parser\url\ParserUrl::setMsgContent
     */
    public function testBadlyFormedUrls(): void
    {
        $this->urlTester->method('testValue')->willReturn(false);
        $this->msg->expects($this->once())->method('setContent');
        $badUrl = 'anyvalue';
        self::assertFalse($this->parser->parse($badUrl));
    }

    /**
     * testReservedChars
     * @covers \pvc\parser\url\ParserUrl::parseValue
     */
    public function testReservedChars(): void
    {
        $reservedChars = ['!', '*', '\'', '(', ')', ':', ';', '@', '&', '=', '+', ',', '/', '?', '$', '#', '[', ']'];

        // reserved chars in the path component
        $urlString = 'http://www.somehost.com/' . implode($reservedChars);
        $values = [
            'scheme' => 'http',
            'host' => 'www.somehost.com',

            /**
             * the '?' which is five characters from the end of the reserved characters array is interpreted as the
             * delimiter for a querystring.  The '#', which is three characters from the end, is recognized as the
             * fragment delimiter.
             */
            'path' => '/' . implode(array_slice($reservedChars, 0, array_search('?', $reservedChars))),
            'query' => '$',
            'fragment' => '[]',
        ];
        $this->url->expects($this->once())->method('setAttributesFromArray')->with($values);
        $this->urlTester->method('testValue')->willReturn(true);
        self::assertTrue($this->parser->parse($urlString));
    }


    /**
     * testMultibyteCharInPath
     * @covers \pvc\parser\url\ParserUrl::parseValue
     *
     * not all parsers can deal with multibyte characters in the string.
     */
    public function testMultibyteCharInPath(): void
    {
        /**
         * \u{263A} is a smiley face.  Note that in Windows you MUST use double quotes (not single quotes)
         * as the string delimiter in order to use unicode codepoints
         */
        $pathWithMultibyteCharSmileyFace = '/Hello/World' . "\u{263A}";

        // the path has 13 characters: 12 plus the smiley face
        self::assertEquals(13, mb_strlen($pathWithMultibyteCharSmileyFace));

        $urlString = 'http://www.nowhere.com' . $pathWithMultibyteCharSmileyFace;
        $values = [
            'scheme' => 'http',
            'host' => 'www.nowhere.com',
            'path' => $pathWithMultibyteCharSmileyFace,
        ];
        $this->url->expects($this->once())->method('setAttributesFromArray')->with($values);
        $this->urlTester->method('testValue')->willReturn(true);
        self::assertTrue($this->parser->parse($urlString));
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

        /**
         * "BELL" control character - illegal
         */
        $illegalChar = chr(0x7);
        $host = $hostFirstPart . $illegalChar . $hostLastPart;

        $urlString = $scheme . $host;

        /**
         * look in the pvc filter_var library and you will see this same test showing that the actual
         * FilterVarvalidateUrl class does in fact return false on this example
         */
        $this->urlTester->method('testValue')->willReturn(false);
        self::assertFalse($this->parser->parse($urlString));
    }
}
