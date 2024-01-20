<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvcTests\parser\boolean;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\boolean\ParserBooleanLoose;

/**
 * Class ParserBooleanLooseTest
 */
class ParserBooleanLooseTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected ParserBooleanLoose $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new ParserBooleanLoose($this->msg);
    }

    /**
     * @function testParseValue
     * @param string $input
     * @param bool $expectedResult
     * @param bool|null $parsedValue
     * @dataProvider dataProvider
     * @covers       \pvc\parser\boolean\ParserBooleanLoose::parseValue
     * @covers       \pvc\parser\boolean\ParserBooleanLoose::setMsgContent()
     */
    public function testParseValue(string $input, bool $expectedResult, bool $parsedValue = null): void
    {
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
            "'1' is OK and evaluates to true" => ['1', true, true],
            "'0' is OK and evaluates to false" => ['0', true, false],
            "'2' is bad and evaluates to null" => ['2', false],
            "'-1' is bad and evaluates to null" => ['-1', false],
            "'any other bad string' is bad and evaluates to null" => ['2', false],
            "'yes' is OK and evaluates to true" => ['yes', true, true],
            "'Yes' is OK and evaluates to true" => ['Yes', true, true],
            "'YeS' is OK (not case sensitive) and evaluates to true" => ['Yes', true, true],
            "'no' is OK and evaluates to false" => ['no', true, false],
            "'No' is OK and evaluates to false" => ['No', true, false],
            "'NO' is OK (not case sensitive) and evaluates to false" => ['NO', true, false],
            "'true' is OK and evaluates to true" => ['true', true, true],
            "'TrUe' is OK (not case sensitive) and evaluates to true" => ['TrUe', true, true],
            "'false' is OK and evaluates to false" => ['false', true, false],
            "'FaLsE' is OK (not case sensitive) and evaluates to false" => ['FaLsE', true, false],
            "missplellings are right out" => ['FLasE', false]

        ];
    }
}
