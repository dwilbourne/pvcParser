<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\parser\err;

use pvc\err\XDataTestMaster;
use pvc\parser\err\_ParserXData;
use ReflectionException;

/**
 * Class _ValidatorXDataTest
 */
class _ParserXDataTest extends XDataTestMaster
{
    /**
     * testValidatorXData
     * @throws ReflectionException
     * @covers \pvc\parser\err\_ParserXData::getLocalXCodes
     * @covers \pvc\parser\err\_ParserXData::getXMessageTemplates
     * @covers \pvc\parser\err\DuplicateColumnHeadingException
     * @covers \pvc\parser\err\InvalidColumnHeadingException
     * @covers \pvc\parser\err\InvalidDateTimeTypeException
     * @covers \pvc\parser\err\InvalidEscapeCharacterException
     * @covers \pvc\parser\err\InvalidFieldDelimiterException
     * @covers \pvc\parser\err\InvalidFieldEnclosureCharException
     * @covers \pvc\parser\err\InvalidLineTerminationException
     * @covers \pvc\parser\err\InvalidMsgIdException
     * @covers \pvc\parser\err\InvalidQuerystringSeparatorException
     * @covers \pvc\parser\err\InvalidReturnTypeException
     * @covers \pvc\parser\err\NonExistentColumnHeadingException
     * @covers \pvc\parser\err\NonExistentFilePathException
     */
    public function testParserXData(): void
    {
        $xData = new _ParserXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}