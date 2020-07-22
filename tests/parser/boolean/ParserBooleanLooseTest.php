<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\boolean;

use Exception;
use PHPUnit\Framework\TestCase;
use pvc\parser\boolean\ParserBooleanLoose;

/**
 * Class ParserBooleanLooseTest
 */
class ParserBooleanLooseTest extends TestCase
{

    protected ParserBooleanLoose $parser;

    public function setUp(): void
    {
        $this->parser = new ParserBooleanLoose();
    }

    /**
     * @function testParser
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
