<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\parser\csv;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\csv\CsvParser;
use pvc\parser\err\CsvParserException;

/**
 * Class FgetCsvFailsTest
 * @runTestsInSeparateProcesses
 */
class FgetCsvFailsTest extends TestCase
{
    /**
     * @var MsgInterface|MockObject
     */
    protected MsgInterface|MockObject $msg;

    /**
     * @var CsvParser
     */
    protected CsvParser $parser;

    /**
     * @var string
     */
    protected string $fixturesDir;


    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new CsvParser($this->msg);
        $this->fixturesDir = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
    }

    /**
     * testParserThrowsExceptionWithNonExistentFile
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testParserThrowsExceptionWhenFgetcsvVerbFails(): void
    {
        $fileName = 'application.csv';
        $fixture = $this->fixturesDir . DIRECTORY_SEPARATOR . $fileName;
        uopz_set_return('fgetcsv', false);
        self::expectException(CsvParserException::class);
        $this->parser->parse($fixture);
        uopz_unset_return('fgetcsv');
    }


}
