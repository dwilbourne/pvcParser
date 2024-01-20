<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvcTests\parser\boolean;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\boolean\ParserBooleanTrueFalse;

/**
 * Class ParserBooleanStrictTest
 */
class ParserBooleanTrueFalseTest extends TestCase
{
    protected MsgInterface|MockObject $msg;

    protected ParserBooleanTrueFalse $parser;

    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new ParserBooleanTrueFalse($this->msg);
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
    public function testParseValue(
        string $input,
        bool $isCaseSensitive,
        bool $expectedResult,
        string $comment,
        bool $parsedValue = null
    ): void {
        $this->parser->setCaseSensitive($isCaseSensitive);

        if ($expectedResult === true) {
            $this->msg->expects($this->never())->method('setContent');
        } else {
            $this->msg->expects($this->once())->method('setContent');
        }

        self::assertEquals($expectedResult, $this->parser->parse($input), $comment);

        if ($expectedResult) {
            self::assertEquals($parsedValue, $this->parser->getParsedValue());
        } else {
            self::assertNull($this->parser->getParsedValue());
        }
    }

    public function dataProvider(): array
    {
        return [
            /**
             * order of array elements is input / case-sensitive / expected result / comment / parsed value
             *
             * the parsed value element is not present if the expected result of the parsing is false
             */
            ['true', true, true, "'true' is OK", true],
            ['TRUe', false, true, "'TrUe' is OK (not case sensitive", true],
            ['TRUe', true, false, "'TrUe' is not OK (is case sensitive)"],
            ['false', true, true, "'false' is OK", false],
            ['FaLSe', false, true, "'FaLSe' is OK (not case sensitive", false],
            ['FaLSe', true, false, "'FaLSe' is not OK (is case sensitive"],
            ['other strings', false, false, "'other strings' is not ok"],
            ['0', false, false, "'0' is not ok"],
            ['yes', false, false, "'yes' is not ok"]
        ];
    }
}
