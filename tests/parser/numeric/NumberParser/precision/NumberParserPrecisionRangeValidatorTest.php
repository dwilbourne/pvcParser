<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\precision;

use pvc\msg\Msg;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeValidator;
use PHPUnit\Framework\TestCase;

class NumberParserPrecisionRangeValidatorTest extends TestCase
{
    protected NumberParserPrecisionRangeValidator $validator;

    public function setUp(): void
    {
        $this->validator = new NumberParserPrecisionRangeValidator();
    }

    /**
     * @function testValidate
     * @param int $value
     * @param bool $successful
     * @dataProvider numberProvider
     */
    public function testValidate(int $value, bool $successful) : void
    {
        self::assertEquals($successful, $this->validator->validate($value));
    }

    public function numberProvider() : array
    {
        return [
            '12345' => [12345, true],
            '1' => [1, true],
            '0' => [0, true],
            '-1' => [-1, true],
            '-2' => [-2, false],
            '-100' => [-100, false],
        ];
    }

    public function testGetErrMsg() : void
    {
        self::assertTrue($this->validator->getErrMsg() instanceof Msg);
    }
}
