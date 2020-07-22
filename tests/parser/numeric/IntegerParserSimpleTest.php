<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric;

use pvc\parser\numeric\IntegerParserSimple;
use PHPUnit\Framework\TestCase;

class IntegerParserSimpleTest extends TestCase
{
    protected IntegerParserSimple $parser;

    public function setUp(): void
    {
        $this->parser = new IntegerParserSimple();
    }

    /**
     * @function testParse
     * @param string $input
     * @param int|bool $expectedResult
     * @dataProvider numberProvider
     */
    public function testParse(string $input, $expectedResult) : void
    {
        $actualResult = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function numberProvider() : array
    {
        return [
            'basic test 1' => ['12345', 12345],
            'basic test 1.5' => ['0', 0],
            'basic test 2 includes decimal point' => ['12345.0040', false],
            'basic test 3 fails with alpha char' => ['123K45', false],
            'basic test 4 fails with decimal point' => ['12345.', false],
            'basic test 5 fails with decimal point and trailing zeros' => ['12345.000', false],
            'basic test 6 fails with grouping separator' => ['12,345', false],
            'preceding negative sign OK' => ['-50', -50],
            'succeeding negative sign fails' => ['50-', false],
            'succeeding negative sign 2 fails' => ['0-', false],
        ];
    }

    public function testBasic() : void
    {
        $input = '12345';
        $expectedResult = 12345;
        $actualResult = ($this->parser->parse($input) ? $this->parser->getParsedValue() : false);
        self::assertEquals($expectedResult, $actualResult);
    }
}
