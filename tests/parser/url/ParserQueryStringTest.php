<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\parser\url;

use PHPUnit\Framework\TestCase;
use pvc\parser\url\ParserQueryString;

/**
 * since the class being tested is a very simple encapsulation of parse_str, this test will not illustrate
 * all the behaviors that parse_str exhibits.  See the pvc language test suite for those illustrations
 */

class ParserQueryStringTest extends TestCase
{
    protected ParserQueryString $parser;

    public function setUp(): void
    {
        $this->parser = new ParserQueryString();
    }

    public function testParse(): void
    {
        // the parse method never fails - the test string could literally be any string you can imagine
        self::assertTrue($this->parser->parse(''));
    }

    public function testGetParsedValue(): void
    {
        self::assertTrue($this->parser->parse('this string kind of looks like an array ['));
        self::assertTrue(is_array($this->parser->getParsedValue()));
        self::assertEquals(1, sizeof($this->parser->getParsedValue()));
    }

    public function testErrorMessageIsAlwaysNull(): void
    {
        self::assertNull($this->parser->getErrmsg());
    }

}
