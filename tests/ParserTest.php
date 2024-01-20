<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\parser;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\Parser;

class ParserTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected Parser $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = $this->getMockForAbstractClass(Parser::class, [$this->msg]);
    }

    /**
     * testConstruct
     * @covers \pvc\parser\Parser::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Parser::class, $this->parser);
    }

    /**
     * testSetGetMsg
     * @covers \pvc\parser\Parser::setMsg
     * @covers \pvc\parser\Parser::getMsg
     */
    public function testSetGetMsg(): void
    {
        $msg = $this->createMock(MsgInterface::class);
        $this->parser->setMsg($msg);
        self::assertEquals($msg, $this->parser->getMsg());
    }

    /**
     * testGetParsedValueReturnsNullByDefault
     * @covers \pvc\parser\Parser::getParsedValue
     */
    public function testGetParsedValueReturnsNullByDefault(): void
    {
        self::assertNull($this->parser->getParsedValue());
    }

    /**
     * testParseClearsMsgAndReturnsTrueWithEmptyString
     * @covers \pvc\parser\Parser::parse
     */
    public function testParseClearsMsgAndReturnsTrueWithEmptyString(): void
    {
        $emptyString = '';
        $this->msg->expects($this->once())->method('clearContent');
        self::assertTrue($this->parser->parse($emptyString));
        self::assertNull($this->parser->getParsedValue());
    }

    /**
     * testParseSucceeds
     * @covers \pvc\parser\Parser::parse
     */
    public function testParseSucceeds(): void
    {
        $mockInput = 'foo';
        $expectedResult = true;
        $this->msg->expects($this->once())->method('clearContent');
        $this->parser->expects($this->once())->method('parseValue')->with('foo')->willReturn($expectedResult);
        $this->parser->expects($this->never())->method('setMsgContent');
        self::assertEquals($expectedResult, $this->parser->parse($mockInput));
    }

    /**
     * testParseFails
     * @covers \pvc\parser\Parser::parse
     */
    public function testParseFails(): void
    {
        $mockInput = 'foo';
        $expectedResult = false;
        $this->msg->expects($this->once())->method('clearContent');
        $this->parser->expects($this->once())->method('parseValue')->with('foo')->willReturn($expectedResult);
        $this->parser->expects($this->once())->method('setMsgContent');
        self::assertEquals($expectedResult, $this->parser->parse($mockInput));
    }
}
