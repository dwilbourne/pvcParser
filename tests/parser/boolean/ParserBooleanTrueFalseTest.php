<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\boolean;

use Exception;
use PHPUnit\Framework\TestCase;
use pvc\parser\boolean\ParserBooleanTrueFalse;

/**
 * Class ParserBooleanStrictTest
 */
class ParserBooleanTrueFalseTest extends TestCase
{

    protected ParserBooleanTrueFalse $parser;

    public function setUp(): void
    {
        $this->parser = new ParserBooleanTrueFalse();
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
            "'true' is OK" => ['true', true, true],
            "'TrUe' is OK (not case sensitive" => ['TRUe', true, true],
            "'false' is OK" => ['false', true, false],
            "'FaLSe' is OK (not case sensitive" => ['FaLSe', true, false],
            "'other strings' is bad and evaluates to null" => ['other strings', false],
            "'0' is bad and evaluates to null" => ['0', false],
            "'yes' is bad and evaluates to null" => ['yes', false]
        ];
    }
}
