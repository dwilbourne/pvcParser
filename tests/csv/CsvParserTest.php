<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\parser\csv;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\csv\CsvParser;
use pvc\parser\err\InvalidColumnHeadingException;
use pvc\parser\err\InvalidEscapeCharacterException;
use pvc\parser\err\InvalidFieldDelimiterException;
use pvc\parser\err\InvalidFieldEnclosureCharException;
use pvc\parser\err\InvalidLineTerminationException;

/**
 * Class CsvParserTest
 * @package tests\parser\file\csv
 */
class CsvParserTest extends TestCase
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

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->msg = $this->createMock(MsgInterface::class);
        $this->parser = new CsvParser($this->msg);
        $this->fixturesDir = __DIR__ . DIRECTORY_SEPARATOR . "fixtures";
    }

    /**
     * testSetGetLineTerminationChars
     * @throws InvalidLineTerminationException
     * @covers \pvc\parser\csv\CsvParser::setLineTermination
     * @covers \pvc\parser\csv\CsvParser::getLineTermination
     */
    public function testSetGetLineTerminationChars(): void
    {
        $ltc = "\n";
        $this->parser->setLineTermination($ltc);
        self::assertEquals($ltc, $this->parser->getLineTermination());

        $ltc = "\r\n";
        $this->parser->setLineTermination($ltc);
        self::assertEquals($ltc, $this->parser->getLineTermination());

        $ltc = "badChars";
        self::expectException(InvalidLineTerminationException::class);
        $this->parser->setLineTermination($ltc);
    }

    public function testSetGetColumnHeadings(): void
    {
        // strings OK
        $columnHeadings = ["foo", "pvcTests\parser\php\fixtures\bar", "baz"];
        $this->parser->setColumnHeadings($columnHeadings);
        self::assertEquals($columnHeadings, $this->parser->getColumnHeadings());

        // numerics OK
        $columnHeadings = [3, 4, 5];
        $this->parser->setColumnHeadings($columnHeadings);
        self::assertEquals($columnHeadings, $this->parser->getColumnHeadings());

        // others not ok
        $columnHeadings = [true, [1, 2], new \stdClass()];
        self::expectException(InvalidColumnHeadingException::class);
        $this->parser->setColumnHeadings($columnHeadings);
    }

    public function testSetGetFieldDelimiterChar(): void
    {
        $fdc = ";";
        $this->parser->setFieldDelimiterChar($fdc);
        self::assertEquals($fdc, $this->parser->getFieldDelimiterChar());

        // can be no more than one character
        $fdc = "'a'";
        self::expectException(InvalidFieldDelimiterException::class);
        $this->parser->setFieldDelimiterChar($fdc);
    }

    public function testSetGetFieldEnclosureChar(): void
    {
        $fec = "\"";
        $this->parser->setFieldEnclosureChar($fec);
        self::assertEquals($fec, $this->parser->getFieldEnclosureChar());

        // can be no more than one character
        $fec = "\\/";
        self::expectException(InvalidFieldEnclosureCharException::class);
        $this->parser->setFieldEnclosureChar($fec);
    }

    public function testSetGetEscapeCharacter(): void
    {
        $ec = "/";
        $this->parser->setEscapeChar($ec);
        self::assertEquals($ec, $this->parser->getEscapeChar());

        // can be no more than one character
        $ec = "//";
        self::expectException(InvalidEscapeCharacterException::class);
        $this->parser->setEscapeChar($ec);
    }

    public function testSetGetFirstRowContainsColumnHeadings(): void
    {
        $value = true;
        $this->parser->setFirstRowContainsColumnHeadings($value);
        self::assertEquals($value, $this->parser->getFirstRowContainsColumnHeadings());

        $value = false;
        $this->parser->setFirstRowContainsColumnHeadings($value);
        self::assertEquals($value, $this->parser->getFirstRowContainsColumnHeadings());
    }

    public function testDetectLineTerminationEmptyString(): void
    {
        $testString = "";
        self::assertTrue($this->parser->detectLineTermination($testString));
        self::expectException(\Error::class);
        $this->parser->getLineTermination();
    }

    public function testDetectLineTerminationWindows(): void
    {
        $testString = "somedata 1\r\nsomedata 2\r\nsomedata 3\r\n";
        self::assertTrue($this->parser->detectLineTermination($testString));
        self::assertEquals("\r\n", $this->parser->getLineTermination());
    }

    public function testDetectLineTerminationEveryoneElse(): void
    {
        $testString = "somedata 1\nsomedata 2\nsomedata 3\n";
        self::assertTrue($this->parser->detectLineTermination($testString));
        self::assertEquals("\n", $this->parser->getLineTermination());
    }

    public function testDetectLineTerminationMixedUp(): void
    {
        $testString = "somedata 1\nsomedata 2\r\nsomedata 3\n";
        self::assertFalse($this->parser->detectLineTermination($testString));
        self::expectException(\Error::class);
        $this->parser->getLineTermination();
    }

    public function testDetectLineTerminationNoTerminator(): void
    {
        $testString = "somedata 1somedata 2somedata 3";
        self::assertFalse($this->parser->detectLineTermination($testString));
        self::expectException(\Error::class);
        $this->parser->getLineTermination();
    }

    /**
     * testEmptyString
     */
    public function testEmptyString(): void
    {
        $csvData = "";
        self::assertTrue($this->parser->parse($csvData));
        self::assertIsArray($this->parser->getParsedValue());
        self::assertEmpty($this->parser->getParsedValue());
    }

    /**
     * testFileNoColumnHeadings
     */
    public function testNoColumnHeadings(): void
    {
        // encapsulate the test so we can us it later to test successive calls to the SUT without
        // reinitializing / recreating the SUT
        $this->makeTestWithNoColumnHeadings();
    }

    /**
     * makeTestWithNoColumnHeadings
     */
    protected function makeTestWithNoColumnHeadings(): void
    {
        $testFile = 'application.csv';
        $fixture = $this->makeFilePath($testFile);
        $fileAccess = new FileAccess();
        $fileAccess->openFile($fixture, 'r');
        $csvData = $fileAccess->readFile();

        self::assertTrue($this->parser->parse($csvData));
        self::assertIsArray($this->parser->getParsedValue());
        // fixture has 6 rows of data
        self::assertEquals(6, count($this->parser->getParsedValue()));

        // the 6th row data should be
        $expectedResult = ['activity+json', 'application/activity+json', '[W3C][Benjamin_Goering]'];
        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[5];
        self::assertEquals($expectedResult, $actualResult);

        $fileAccess->closeFile();
    }

    /**
     * makeFilePath
     * @param string $filename
     * @return string
     */
    protected function makeFilePath(string $filename): string
    {
        return $this->fixturesDir . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * testFileWithColumnHeadings
     */
    public function testFileWithColumnHeadings(): void
    {
        // encapsulate the test so we can us it later to test succssive calls to the SUT without
        // reinitializing / recreating the SUT
        $this->makeTestWithColumnsHeadings();
    }

    /**
     * makeTestWithColumnsHeadings
     * @throws \pvc\filesys\err\FileAccessException
     */
    protected function makeTestWithColumnsHeadings(): void
    {
        $testFile = 'audio.csv';
        $fixture = $this->makeFilePath($testFile);
        $fileAccess = new FileAccess();
        $fileAccess->openFile($fixture, 'r');
        $csvData = $fileAccess->readFile();

        $this->parser->setFirstRowContainsColumnHeadings(true);

        self::assertTrue($this->parser->parse($csvData));
        self::assertIsArray($this->parser->getParsedValue());
        // fixture has 10 rows - the first are columns headings, the remaining 9 are data
        self::assertEquals(9, count($this->parser->getParsedValue()));

        // the first row data should be
        $expectedResult = [
            'Name' => '1d-interleaved-parityfec',
            'Template' => 'audio/1d-interleaved-parityfec',
            'Reference' => '[RFC6015]'
        ];

        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[0];
        self::assertEquals($expectedResult, $actualResult);

        $fileAccess->closeFile();
    }

    /**
     * testFileWhileSettingColumnHeadingsManually
     * @throws \pvc\parser\err\InvalidColumnHeadingException
     */
    public function testSettingColumnHeadingsManually(): void
    {
        $testFile = 'application.csv';
        $fixture = $this->makeFilePath($testFile);
        $fileAccess = new FileAccess();
        $fileAccess->openFile($fixture, 'r');
        $csvData = $fileAccess->readFile();

        // file has three columns - third column should have a numeric index in the resulting array
        // because we have only supplied 2 columns headings
        $columnHeadings = ['foo', 'bar'];
        $this->parser->setColumnHeadings($columnHeadings);

        self::assertTrue($this->parser->parse($csvData));

        // the first row data should be
        $expectedResult = [
            'foo' => '1d-interleaved-parityfec',
            'pvcTests\parser\php\fixtures\bar' => 'application/1d-interleaved-parityfec',
            0 => '[RFC6015]'
        ];

        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[0];
        self::assertEquals($expectedResult, $actualResult);

        $fileAccess->closeFile();
    }
}
