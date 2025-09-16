<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\csv;

use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\CsvParserException;
use pvc\parser\err\DuplicateColumnHeadingException;
use pvc\parser\err\InvalidColumnHeadingException;
use pvc\parser\err\InvalidEscapeCharacterException;
use pvc\parser\err\InvalidFieldDelimiterException;
use pvc\parser\err\InvalidFieldEnclosureCharException;
use pvc\parser\err\NonExistentColumnHeadingException;
use pvc\parser\err\OpenFileException;
use pvc\parser\Parser;
use Throwable;

/**
 * Class CsvParser.  This class restricts record termination characters to be either LF or CRLF (windows).  PHP is
 * supposed to automatically detect line endings with its verbs that extract lines from a file. This class uses fgetscsv
 * and detects the presence of byte order marks at the beginning of the file
 *
 * @extends Parser<array>
 */
class CsvParser extends Parser
{
    /**
     * @var string
     */
    protected string $filePath;

    /**
     * @var array <string>
     */
    protected array $columnHeadings;
    /**
     * @var non-empty-string
     */
    protected string $fieldDelimiterChar = ',';

    /**
     * @var string
     */
    protected string $fieldEnclosureChar = "\"";

    /**
     * @var string
     */
    protected string $escapeChar = "\\";

    /**
     * @var bool
     */
    protected bool $firstRowContainsColumnHeadings = false;

    /**
     * setColumnHeadings
     * @param array<string> $columnHeadings
     * @throws InvalidColumnHeadingException|DuplicateColumnHeadingException
     */
    public function setColumnHeadings(array $columnHeadings) : void
    {
        if (empty($columnHeadings)) {
            throw new NonExistentColumnHeadingException();
        }

        /**
         * re-initialize the attribute so that successive calls to the parser work properly
         */
        $this->columnHeadings = [];

        foreach ($columnHeadings as $columnHeading) {
            /**
             * must be a string
             *
             * @phpstan-ignore-next-line
             */
            if (!is_string($columnHeading)) {
                throw new InvalidColumnHeadingException();
            }

            /**
             * characters in the column heading must all be visible
             */
            if (!ctype_graph($columnHeading)) {
                throw new InvalidColumnHeadingException();
            }

            /**
             * no duplicate column headings since they become indices into an array
             */
            if (in_array($columnHeading, $this->columnHeadings)) {
                throw new DuplicateColumnHeadingException($columnHeading);
            }

            $this->columnHeadings[] = $columnHeading;
        }
    }

    /**
     * getColumnHeadings
     * @return array<string>
     */
    public function getColumnHeadings() : array
    {
        return $this->columnHeadings;
    }

    protected function isSingleVisibleCharacter(string $char): bool
    {
        return (ctype_graph($char) && (strlen($char) == 1));
    }

    /**
     * setFieldDelimiterChar
     * @param non-empty-string $delimiterChar
     * @throws InvalidFieldDelimiterException
     */
    public function setFieldDelimiterChar(string $delimiterChar) : void
    {
        /** field delimiter must be a single visible character */
        if (!$this->isSingleVisibleCharacter($delimiterChar)) {
            throw new InvalidFieldDelimiterException();
        }
        $this->fieldDelimiterChar = $delimiterChar;
    }

    /**
     * getFieldDelimiterChar
     * @return string
     */
    public function getFieldDelimiterChar() : string
    {
        return $this->fieldDelimiterChar;
    }

    /**
     * setFieldEnclosureChar
     * @param string $enclosureChar
     * @throws InvalidFieldEnclosureCharException
     */
    public function setFieldEnclosureChar(string $enclosureChar) : void
    {
        /** field enclosure must be a single visible character  */
        if (!$this->isSingleVisibleCharacter($enclosureChar)) {
            throw new InvalidFieldEnclosureCharException();
        }
        $this->fieldEnclosureChar = $enclosureChar;
    }

    /**
     * getFieldEnclosureChar
     * @return string
     */
    public function getFieldEnclosureChar() : string
    {
        return $this->fieldEnclosureChar;
    }

    /**
     * setEscapeChar
     * @param string $escapeChar
     * @throws InvalidEscapeCharacterException
     */
    public function setEscapeChar(string $escapeChar) : void
    {
        /** escape character must be a single visible character */
        if (!$this->isSingleVisibleCharacter($escapeChar)) {
            throw new InvalidEscapeCharacterException();
        }
        $this->escapeChar = $escapeChar;
    }

    /**
     * getEscapeChar
     * @return string
     */
    public function getEscapeChar() : string
    {
        return $this->escapeChar;
    }

    public function setFirstRowContainsColumnHeadings(bool $value) : void
    {
        $this->firstRowContainsColumnHeadings = $value;
    }

    public function getFirstRowContainsColumnHeadings() : bool
    {
        return $this->firstRowContainsColumnHeadings;
    }

    /**
     * parse
     * @param string $data
     * @return bool
     * @throws NonExistentColumnHeadingException
     */
    protected function parseValue(string $data): bool
    {
        try {
            $handle = fopen($data, 'r');
        } catch (Throwable $e) {
            throw new OpenFileException($data, $e);
        }
        assert($handle !== false);
        $this->filePath = $data;

        $bom = "\xef\xbb\xbf";
        if (fgets($handle, 4) !== $bom) {
            // BOM not found - rewind pointer to start of file.
            rewind($handle);
        }

        $rows = [];
        while (false !== ($line = (fgetcsv(
                $handle,
                null,
            $this->getFieldDelimiterChar(),
            $this->getFieldEnclosureChar(),
            $this->getEscapeChar()
        )))) {
            /**
             * fgetcsv returns an array with a single element consisting of a null value if the line is empty
             */
            if (!is_null($line[0])) {
                $rows[] = $line;
            }
        }

        /**
         * if we are not at the end of the file then fgetcsv returned false because it could not parse a line.
         */
        if (!feof($handle)) {
            throw new CsvParserException($data);
        }
        fclose($handle);

        /**
         * array_combine would automatically convert invalid array keys to strings, but it will not check
         * for duplicate column names or verify that all the characters are graphic (e.g. visible), so the
         * setColumnHeadings method ensures those things.  Also, array_combine fails if the number of headings does
         * not match the number of elements in each and every row of data.  It is certainly possible to ensure the
         * shapes match and reshape as necessary, but it's about as much trouble as handling each row manually......
         */

        if ($this->getFirstRowContainsColumnHeadings()) {
            /** @var array<string> $firstRow */
            $firstRow = array_shift($rows);
            if ($firstRow) {
                $this->setColumnHeadings($firstRow);
            }

            foreach ($rows as $row) {
                $newRow = [];
                foreach ($row as $index => $element) {
                    if (isset($this->columnHeadings[$index])) {
                        $newRow[$this->columnHeadings[$index]] = $element;
                    } else {
                        $newRow[$index] = $element;
                    }
                }
                $this->parsedValue[] = $newRow;
            }
        } else {
            $this->parsedValue = $rows;
        }

        return true;
    }

    public function setMsgContent(MsgInterface $msg): void
    {
    }
}
