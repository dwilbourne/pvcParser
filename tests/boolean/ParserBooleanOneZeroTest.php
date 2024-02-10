<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\boolean;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\boolean\ParserBooleanOneZero;

/**
 * Class RegexBooleanOneZeroTest
 */
class ParserBooleanOneZeroTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected ParserBooleanOneZero $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new ParserBooleanOneZero($this->msg);
    }

    /**
     * @function testParseValue
     * @param string $input
     * @param bool $expectedResult
     * @param bool|null $parsedValue
     * @dataProvider dataProvider
     * @covers       \pvc\parser\boolean\ParserBooleanOneZero::parseValue
     * @covers       \pvc\parser\boolean\ParserBooleanOneZero::setMsgContent()
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
            "'1' is OK" => ['1', true, true],
            "'0' is OK" => ['0', true, false],
            "'other strings' is bad and evaluates to null" => ['other strings', false],
            "'true' is bad and evaluates to null" => ['true', false],
            "'no' is bad and evaluates to null" => ['no', false]
        ];
    }
}
