<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\url;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\http\err\InvalidQuerystringException;
use pvc\interfaces\http\QueryStringInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\InvalidQuerystringSeparatorException;
use pvc\parser\url\ParserQueryString;

class ParserQueryStringTest extends TestCase
{
    protected MsgInterface $msg;
    protected QueryStringInterface|MockObject $qstr;

    protected ParserQueryString $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->qstr = $this->createMock(QueryStringInterface::class);
        $this->parser = new ParserQueryString($this->msg, $this->qstr);
    }

    /**
     * testSetGetQueryString
     * @covers \pvc\parser\url\ParserQueryString::setQueryString()
     * @covers \pvc\parser\url\ParserQueryString::getQueryString()
     */
    public function testSetGetQueryString(): void
    {
        $qstr = $this->createMock(QueryStringInterface::class);
        $this->parser->setQueryString($qstr);
        self::assertEquals($qstr, $this->parser->getQueryString());
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
     * testParseSucceedsIfThereIsNoEqualsSignOrParameterValue
     * @covers \pvc\parser\url\ParserQueryString::parseValue
     */
    public function testParseSucceedsIfThereIsNoEqualsSignOrParameterValue(): void
    {
        $qstrWithNoParamValuePair = 'this string kind of looks like an array [';
        self::assertTrue($this->parser->parse($qstrWithNoParamValuePair));
    }

    /**
     * testParseFailsIfThereIsNoValue
     * @covers \pvc\parser\url\ParserQueryString::parseValue
     */
    public function testParseSucceedsIfThereIsAnEqualsSignButNoValue(): void
    {
        $qstrWithNoValue = 'foo=';
        self::assertTrue($this->parser->parse($qstrWithNoValue));
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

    /**
     * testParseFailsWhenQuerystringSetParamsFails
     * @covers \pvc\parser\url\ParserQueryString::parseValue
     * @covers \pvc\parser\url\ParserQueryString::setMsgContent
     */
    public function testParseFailsWhenQuerystringSetParamsFails(): void
    {
        $testQstr = 'foo=good&bar=8';

        /**
         * the try throw catch in ParserQueryString catches any exception.  In real life, the QueryString object
         * throws an InvalidQueryStringException, but it's not important to this test.
         */
        $this->qstr->method('setParams')
                   ->willThrowException(new InvalidQuerystringException());
        $this->msg->expects($this->once())->method('setContent');
        self::assertFalse($this->parser->parse($testQstr));
    }
}
