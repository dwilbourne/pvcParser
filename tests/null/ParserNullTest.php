<?php

namespace pvcTests\parser\null;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\null\ParserNull;

class ParserNullTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected ParserNull $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new ParserNull($this->msg);
    }

    /**
     * testParseValue
     *
     * @param  string  $input
     * @param  bool  $expectedResult
     * @param  bool|null  $parsedValue
     *
     * @dataProvider dataProvider
     * @covers       \pvc\parser\null\ParserNull::parseValue
     * @covers       \pvc\parser\null\ParserNull::setMsgContent
     * @return void
     */
    public function testParseValue(
        string $input,
        bool $expectedResult,
        bool $parsedValue = null
    ): void {
        if ($expectedResult === true) {
            $this->msg->expects($this->never())->method('setContent');
        } else {
            $this->msg->expects($this->once())->method('setContent');
        }

        self::assertEquals($expectedResult, $this->parser->parse($input));

        if ($expectedResult) {
            self::assertEquals($parsedValue, $this->parser->getParsedValue());
        } else {
            self::assertNull($this->parser->getParsedValue());
        }
    }

    public function dataProvider(): array
    {
        return [
            "'' is OK and evaluates to true"                      => [
                '',
                true,
                null
            ],
            "'null' is bad and evaluates to null"                 => [
                '-1',
                false
            ],
            "'any other bad string' is bad and evaluates to null" => [
                '2',
                false
            ],
        ];
    }


}
