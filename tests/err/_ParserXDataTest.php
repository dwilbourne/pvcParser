<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\parser\err;

use pvc\err\XDataTestMaster;
use pvc\validator\err\_ValidatorXData;

/**
 * Class _ValidatorXDataTest
 */
class _ParserXDataTest extends XDataTestMaster
{
    /**
     * testValidatorXData
     * @throws \ReflectionException
     * @covers \pvc\validator\err\_ValidatorXData::getLocalXCodes
     * @covers \pvc\validator\err\_ValidatorXData::getXMessageTemplates
     * @covers \pvc\validator\err\SetMaxException
     * @covers \pvc\validator\err\SetMinException
     * @covers \pvc\validator\err\InvalidLabelException
     * @covers \pvc\validator\err\InvalidMsgIdException
     */
    public function testValidatorXData(): void
    {
        $xData = new _ValidatorXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}