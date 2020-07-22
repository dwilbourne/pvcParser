<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\boolean;

use Exception;
use PHPUnit\Framework\TestCase;
use pvc\parser\boolean\ParserBooleanOneZero;

/**
 * Class RegexBooleanOneZeroTest
 */
class ParserBooleanOneZeroTest extends TestCase
{

    protected ParserBooleanOneZero $parser;

    public function setUp(): void
    {
        $this->parser = new ParserBooleanOneZero();
    }

    /**
     * @function testRegex
     * @param string $input
     * @param bool $parsedSuccessfully
     * @param bool|null $parsedValue
     * @dataProvider dataProvider
     */

    public function testParser(string $input, bool $parsedSuccessfully, bool $parsedValue = null) : void
    {
        static::assertEquals($parsedSuccessfully, $this->parser->parse($input));
        static::assertEquals($parsedValue, $this->parser->getParsedValue());
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
