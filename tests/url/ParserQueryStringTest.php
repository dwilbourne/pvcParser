<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\url;

use PHPUnit\Framework\TestCase;
use pvc\http\err\InvalidQuerystringParamNameException;
use pvc\http\url\QueryString;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\InvalidQuerystringSeparatorException;
use pvc\parser\url\ParserQueryString;

class ParserQueryStringTest extends TestCase
{
    protected MsgInterface $msg;
    protected QueryString $qstr;

    protected ParserQueryString $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->qstr = $this->createMock(QueryString::class);
        $this->parser = new ParserQueryString($this->msg, $this->qstr);
    }

    /**
     * testDefaultSeparatorExists
     * @covers \pvc\parser\url\ParserQueryString::__construct
     */
    public function testDefaultSeparatorExists(): void
    {
        self::assertIsString($this->parser->getSeparator());
    }

    /**
     * testSetGetSeparator
     * @covers \pvc\parser\url\ParserQueryString::setSeparator
     * @covers \pvc\parser\url\ParserQueryString::getSeparator
     */
    public function testSetGetSeparator(): void
    {
        $newSeparator = ';';
        $this->parser->setSeparator($newSeparator);
        self::assertEquals($newSeparator, $this->parser->getSeparator());
    }

    /**
     * testSeparatorCannotBeEmpty
     * @throws InvalidQuerystringSeparatorException
     * @covers \pvc\parser\url\ParserQueryString::setSeparator
     */
    public function testSeparatorCannotBeEmpty(): void
    {
        self::expectException(InvalidQuerystringSeparatorException::class);
        $this->parser->setSeparator('');
    }

    /**
     * testParseFailsIfThereIsNoParameterValuePair
     * @covers \pvc\parser\url\ParserQueryString::parseValue
     */
    public function testParseFailsIfThereIsNoParameterValuePair(): void
    {
        $qstrWithNoParamValuePair = 'this string kind of looks like an array [';
        self::assertFalse($this->parser->parse($qstrWithNoParamValuePair));
    }

    /**
     * testParseFailsIfThereIsNoValue
     * @covers \pvc\parser\url\ParserQueryString::parseValue
     */
    public function testParseFailsIfThereIsNoValue(): void
    {
        $qstrWithNoValue = 'foo=';
        self::assertFalse($this->parser->parse($qstrWithNoValue));
    }

    /**
     * testParseFailsWithMalformedParamValuePair
     * @covers \pvc\parser\url\ParserQueryString::parseValue
     */
    public function testParseFailsWithMalformedParamValuePair(): void
    {
        $qstrWithMalformedPair = 'foo=5=4&bar=9';
        self::assertFalse($this->parser->parse($qstrWithMalformedPair));
    }

    /**
     * testParseSuccess
     * @covers \pvc\parser\url\ParserQueryString::parse
     */
    public function testParseSuccess(): void
    {
        $qstr = 'foo=good&bar=8';
        $setParamArray = ['foo' => 'good', 'bar' => '8'];
        $this->qstr->expects($this->once())->method('setParams')->with($setParamArray);
        self::assertTrue($this->parser->parse($qstr));
        self::assertEquals($this->qstr, $this->parser->getParsedValue());
    }

    public function testParseFailsWhenQuerystringSetParamsFails(): void
    {
        $qstr = 'foo=good&bar=8';
        $setParamArray = ['foo' => 'good', 'bar' => '8'];
        $this->qstr->expects($this->once())
                   ->method('setParams')
                   ->with($setParamArray)
                   ->willThrowException(new InvalidQuerystringParamNameException());

        self::assertFalse($this->parser->parse($qstr));
    }
}
