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
use pvc\parser\err\DuplicateColumnHeadingException;
use pvc\parser\err\InvalidColumnHeadingException;
use pvc\parser\err\InvalidEscapeCharacterException;
use pvc\parser\err\InvalidFieldDelimiterException;
use pvc\parser\err\InvalidFieldEnclosureCharException;
use pvc\parser\err\NonExistentColumnHeadingException;
use pvc\parser\err\OpenFileException;
use stdClass;

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
        $this->fixturesDir = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
    }

    /**
     * makeFixtureFilePath
     * @param string $filename
     * @return string
     */
    protected function makeFixtureFilePath(string $filename): string
    {
        return $this->fixturesDir . DIRECTORY_SEPARATOR . $filename;
    }

    /**
     * testSetColumnsHeadingsFailsWithControlCharactersInHeadings
     * @throws DuplicateColumnHeadingException
     * @throws InvalidColumnHeadingException
     * @covers \pvc\parser\csv\CsvParser::setColumnHeadings
     */
    public function testSetColumnsHeadingsFailsWithControlCharactersInHeadings(): void
    {
        /**
         * strings are OK but because the string containing backslashes is in double quotes, PHP interprets
         * \f and \b as control characters, which are non-printable and the test fails
         */
        $columnHeadings = ['foo', "pvcTests\parser\php\fixtures\bar", 'baz'];
        self::expectException(InvalidColumnHeadingException::class);
        $this->parser->setColumnHeadings($columnHeadings);
    }

    /**
     * testSetColumnHeadingFailsWithEmptyArray
     * @throws DuplicateColumnHeadingException
     * @throws InvalidColumnHeadingException
     * @covers \pvc\parser\csv\CsvParser::setColumnHeadings
     */
    public function testSetColumnHeadingFailsWithEmptyArray(): void
    {
        self::expectException(NonExistentColumnHeadingException::class);
        $this->parser->setColumnHeadings([]);
    }

    /**
     * testSetColumnHeadingsFailsWithNonStringArrayElements
     * @throws DuplicateColumnHeadingException
     * @throws InvalidColumnHeadingException
     * @covers \pvc\parser\csv\CsvParser::setColumnHeadings
     */
    public function testSetColumnHeadingsFailsWithNonStringArrayElements(): void
    {
        $columnHeadings = [5, true, [1, 2], new stdClass()];
        self::expectException(InvalidColumnHeadingException::class);
        $this->parser->setColumnHeadings($columnHeadings);
    }

    /**
     * testSetColumnHeadingsWithDuplicateColumnsHeadingsFails
     * @throws DuplicateColumnHeadingException
     * @throws InvalidColumnHeadingException
     * @covers \pvc\parser\csv\CsvParser::setColumnHeadings
     */
    public function testSetColumnHeadingsWithDuplicateColumnsHeadingsFails(): void
    {
        $columnHeadings = ['foo', 'bar', 'foo', 'baz'];
        self::expectException(DuplicateColumnHeadingException::class);
        $this->parser->setColumnHeadings($columnHeadings);
    }

    /**
     * testSetGetColumnHeadingsSucceeds
     * @throws InvalidColumnHeadingException
     * @covers \pvc\parser\csv\CsvParser::setColumnHeadings
     * @covers \pvc\parser\csv\CsvParser::getColumnHeadings
     */
    public function testSetGetColumnHeadingsSucceeds(): void
    {
        /**
         * strings OK.  But note that if the string containing backslashes was in double quotes, PHP would interpret
         * \f and \b as control characters, which are non-printable and the test would fail!
         */
        $columnHeadings = ['foo', 'pvcTests\parser\php\fixtures\bar', 'baz'];
        $this->parser->setColumnHeadings($columnHeadings);
        self::assertEquals($columnHeadings, $this->parser->getColumnHeadings());
    }

    /**
     * testSetGetFieldDelimiterChar
     * @throws InvalidFieldDelimiterException
     * @covers \pvc\parser\csv\CsvParser::setFieldDelimiterChar
     * @covers \pvc\parser\csv\CsvParser::getFieldDelimiterChar
     * @covers \pvc\parser\csv\CsvParser::isSingleVisibleCharacter
     */
    public function testSetGetFieldDelimiterChar(): void
    {
        $fdc = ';';
        $this->parser->setFieldDelimiterChar($fdc);
        self::assertEquals($fdc, $this->parser->getFieldDelimiterChar());

        // can be no more than one character
        $fdc = "'a'";
        self::expectException(InvalidFieldDelimiterException::class);
        $this->parser->setFieldDelimiterChar($fdc);
    }

    /**
     * testSetGetFieldEnclosureChar
     * @throws InvalidFieldEnclosureCharException
     * @covers \pvc\parser\csv\CsvParser::setFieldEnclosureChar
     * @covers \pvc\parser\csv\CsvParser::getFieldEnclosureChar
     * @covers \pvc\parser\csv\CsvParser::isSingleVisibleCharacter
     */
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

    /**
     * testSetGetEscapeCharacter
     * @throws InvalidEscapeCharacterException
     * @covers \pvc\parser\csv\CsvParser::setEscapeChar
     * @covers \pvc\parser\csv\CsvParser::getEscapeChar
     * @covers \pvc\parser\csv\CsvParser::isSingleVisibleCharacter
     */
    public function testSetGetEscapeCharacter(): void
    {
        $ec = '/';
        $this->parser->setEscapeChar($ec);
        self::assertEquals($ec, $this->parser->getEscapeChar());

        // can be no more than one character
        $ec = '//';
        self::expectException(InvalidEscapeCharacterException::class);
        $this->parser->setEscapeChar($ec);
    }

    /**
     * testSetGetFirstRowContainsColumnHeadings
     * @covers \pvc\parser\csv\CsvParser::setFirstRowContainsColumnHeadings
     * @covers \pvc\parser\csv\CsvParser::getFirstRowContainsColumnHeadings
     */
    public function testSetGetFirstRowContainsColumnHeadings(): void
    {
        $value = true;
        $this->parser->setFirstRowContainsColumnHeadings($value);
        self::assertEquals($value, $this->parser->getFirstRowContainsColumnHeadings());

        $value = false;
        $this->parser->setFirstRowContainsColumnHeadings($value);
        self::assertEquals($value, $this->parser->getFirstRowContainsColumnHeadings());
    }

    /**
     * testParserThrowsExceptionWithNonExistentFile
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testParserThrowsExceptionWithNonExistentFile(): void
    {
        $fileName = 'foo';
        $badFixture = $this->makeFixtureFilePath($fileName);
        self::expectException(OpenFileException::class);
        $this->parser->parse($badFixture);
    }

    /**
     * testWithNoColumnHeadings
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testWithNoColumnHeadings(): void
    {
        $testFile = 'application.csv';
        $fixture = $this->makeFixtureFilePath($testFile);

        self::assertTrue($this->parser->parse($fixture));
        self::assertIsArray($this->parser->getParsedValue());
        // fixture has 6 rows of data
        self::assertEquals(6, count($this->parser->getParsedValue()));

        // the 6th row data should be
        $expectedResult = ['activity+json', 'application/activity+json', '[W3C][Benjamin_Goering]'];
        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[5];
        self::assertEquals($expectedResult, $actualResult);
    }

    /**
     * makeTestWithColumnsHeadings
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testWithColumnsHeadings(): void
    {
        $testFile = 'audio.csv';
        $fixture = $this->makeFixtureFilePath($testFile);
        $this->parser->setFirstRowContainsColumnHeadings(true);

        self::assertTrue($this->parser->parse($fixture));
        self::assertIsArray($this->parser->getParsedValue());
        // fixture has 10 rows - the first are columns headings, the remaining 9 are data
        self::assertEquals(9, count($this->parser->getParsedValue()));

        // the first row data should be
        $expectedResult = [
            'Name' => '1d-interleaved-parityfec',
            'Template' => 'audio/1d-interleaved-parityfec',
            'Reference' => '[RFC6015]',
            3 => 'an extra fourth column',
        ];

        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[0];
        self::assertEquals($expectedResult, $actualResult);
    }

    /**
     * testWithEmptyFirstRowAndEmptyLastRow
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testWithEmptyFirstRowAndEmptyLastRow(): void
    {
        $testFile = 'emptyfirstandlastrow.csv';
        $fixture = $this->makeFixtureFilePath($testFile);

        $this->parser->setFirstRowContainsColumnHeadings(false);

        self::assertTrue($this->parser->parse($fixture));
        self::assertIsArray($this->parser->getParsedValue());
        // fixture has 6 rows that contain actual data
        self::assertEquals(6, count($this->parser->getParsedValue()));

        // the first row data should be
        $expectedResult = [
            '1d-interleaved-parityfec',
            'application/1d-interleaved-parityfec',
            '[RFC6015]',
        ];

        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[0];
        self::assertEqualsCanonicalizing($expectedResult, $actualResult);
    }

    /**
     * testFileWhileSettingColumnHeadingsManually
     * @throws InvalidColumnHeadingException
     * @covers \pvc\parser\csv\CsvParser::setColumnHeadings
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testSettingColumnHeadingsManually(): void
    {
        $testFile = 'application.csv';
        $fixture = $this->makeFixtureFilePath($testFile);

        /**
         * file has three columns - third column should have a numeric index in the resulting array
         * because we have only supplied 2 columns headings
         */
        $columnHeadings = ['foo', 'bar'];
        $this->parser->setColumnHeadings($columnHeadings);

        self::assertTrue($this->parser->parse($fixture));

        // the first row data should be
        $expectedResult = [
            'foo' => '1d-interleaved-parityfec',
            'bar' => 'application/1d-interleaved-parityfec',
            2 => '[RFC6015]'
        ];

        $parsedValue = $this->parser->getParsedValue();
        $actualResult = $parsedValue[0];
        self::assertEqualsCanonicalizing($expectedResult, $actualResult);
    }

    /**
     * testExcelFormattedFile
     * @covers \pvc\parser\csv\CsvParser::parseValue
     */
    public function testExcelFormattedFile(): void
    {
        $testFile = 'excelformat.csv';
        $fixture = $this->makeFixtureFilePath($testFile);
        $this->parser->setFirstRowContainsColumnHeadings(true);

        self::assertTrue($this->parser->parse($fixture));
        $parsedValue = $this->parser->getParsedValue();
        self::assertIsArray($parsedValue);

        // the first row data should be
        $expectedResult = [
            'Name' => '1d-interleaved-parityfec',
            'Template' => 'audio/1d-interleaved-parityfec',
            'Reference' => '[RFC6015]',
            3 => 'an extra fourth column',
        ];

        $actualResult = $parsedValue[0];
        self::assertEqualsCanonicalizing($expectedResult, $actualResult);
    }
}
