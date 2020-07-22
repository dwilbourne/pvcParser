<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\csv;

use pvc\parser\csv\err\InvalidColumnHeadingException;
use pvc\parser\csv\err\InvalidEscapeCharacterException;
use pvc\parser\csv\err\InvalidFieldDelimiterException;
use pvc\parser\csv\err\InvalidFieldEnclosureException;
use pvc\parser\csv\err\InvalidLineTerminationException;
use pvc\parser\csv\err\LineTerminationDetectionException;
use pvc\parser\csv\err\NonExistentColumnHeadingException;
use pvc\parser\Parser;

/**
 * Class CsvParser.  This class restricts record termination characters to be either LF or CRLF (windows).
 *
 * @package pvc\parser\file\csv
 */
class CsvParser extends Parser
{

    /**
     * @var array
     */
    protected array $columnHeadings;

    /**
     * @var string
     */
    protected string $lineTermination;

    /**
     * @var string
     */
    protected string $fieldDelimiterChar;

    /**
     * @var string
     */
    protected string $fieldEnclosureChar;

    /**
     * @var string
     */
    protected string $escapeChar;

    /**
     * @var bool
     */
    protected bool $firstRowContainsColumnHeadings;

    /**
     * CsvParser constructor.
     * @param string $lineTermination
     * @throws InvalidEscapeCharacterException
     * @throws InvalidFieldDelimiterException
     * @throws InvalidFieldEnclosureException
     * @throws InvalidLineTerminationException
     */
    public function __construct(bool $firstRowContainsColumnHeadings=false)
    {
        $this->setFieldDelimiterChar(",");
        $this->setFieldEnclosureChar("\"");
        $this->setEscapeChar("\\");
        $this->setColumnHeadings([]);
        $this->setFirstRowContainsColumnHeadings($firstRowContainsColumnHeadings);
    }

    /**
     * setColumnHeadings
     * @param mixed[] $columnHeadings
     * @throws InvalidColumnHeadingException
     */
    public function setColumnHeadings(array $columnHeadings) : void
    {
        foreach ($columnHeadings as $columnHeading) {
            if (!$this->isValidColumnHeading($columnHeading)) {
                throw new InvalidColumnHeadingException();
            }
        }
        $this->columnHeadings = $columnHeadings;
    }

    /**
     * isValidColumnHeading
     * @param mixed $columnHeading
     * @return bool
     */
    protected function isValidColumnHeading($columnHeading) : bool
    {
        return is_string($columnHeading) || is_int($columnHeading);
    }

    /**
     * getColumnHeadings
     * @return array
     */
    public function getColumnHeadings() : array
    {
        return $this->columnHeadings;
    }

    /**
     * setLineTermination
     * @param string $lineTerminator
     * @throws InvalidLineTerminationException
     */
    public function setLineTermination(string $lineTerminator) : void
    {
        $validLineTerminators = ["\n", "\r\n"];
        if (!in_array($lineTerminator, $validLineTerminators)) {
            throw new InvalidLineTerminationException();
        } else {
            $this->lineTermination = $lineTerminator;
        }
    }

    /**
     * getLineTermination
     * @return string
     */
    public function getLineTermination() : string
    {
        return $this->lineTermination;
    }

    /**
     * setFieldDelimiterChar
     * @param string $delimiterChar
     * @throws InvalidFieldDelimiterException
     */
    public function setFieldDelimiterChar(string $delimiterChar) : void
    {
        if (strlen($delimiterChar) > 1) {
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
     * @throws \pvc\parser\csv\err\InvalidFieldEnclosureException
     */
    public function setFieldEnclosureChar(string $enclosureChar) : void
    {
        if (strlen($enclosureChar) > 1) {
            throw new InvalidFieldEnclosureException();
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
     * @throws \pvc\parser\csv\err\InvalidEscapeCharacterException
     */
    public function setEscapeChar(string $escapeChar) : void
    {
        if (strlen($escapeChar) > 1) {
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


    /** if every occurrence of CR is followed by a LF then it is a windows file and set the line termination
     * to CRLF.
     * If there are no carriage returns but there are line feeds, then set it to LF.
     * if there are no line feeds at all, then return false unless the string is empty.
     */

    public function detectLineTermination(string $csvData) : bool
    {
        if (empty($csvData)) {
            // there can be no line termination character
            return true;
        }

        $windows = false;
        $everyoneElse = false;

        for ($i = 0; $i < strlen($csvData) - 1; $i++) {
            if ($csvData[$i] == "\n") {
                $everyoneElse = true;
            }

            if ($csvData[$i] == "\r" && $csvData[$i + 1] == "\n") {
                $windows = true;
                $i++;
            }
        }

        if ($windows && !$everyoneElse) {
            $this->setLineTermination("\r\n");
            return true;
        }

        if ($everyoneElse && !$windows) {
            $this->setLineTermination("\n");
            return true;
        }

        // return false if both are true or both are false
        return ($windows !==  $everyoneElse);
    }

    protected function setColumnHeadingsFromFirstRow(string $firstRow) : void
    {
        if (empty($firstRow)) {
            throw new NonExistentColumnHeadingException();
        }
        $columnHeadings = str_getcsv(
            $firstRow,
            $this->getFieldDelimiterChar(),
            $this->getFieldEnclosureChar(),
            $this->getEscapeChar()
        );
        $this->setColumnHeadings($columnHeadings);
    }

    protected function parseRow(string $row) : array
    {
        $data = str_getcsv(
            $row,
            $this->getFieldDelimiterChar(),
            $this->getFieldEnclosureChar(),
            $this->getEscapeChar()
        );
        $columnHeadings = $this->getColumnHeadings();
        // although it is tempting to try and use array_combine, array_combine will fail if the
        // row of data parses into a different number of fields than are contained in $columnHeadings.
        $newRow = [];
        for ($i = 0; $i < count($data); $i++) {
            if (isset($columnHeadings[$i])) {
                $newRow[$columnHeadings[$i]] = $data[$i];
            } else {
                $newRow[] = $data[$i];
            }
        }
        return $newRow;
    }

    /**
     * parse
     * @param string $csvData
     * @return bool
     * @throws NonExistentColumnHeadingException
     */
    public function parse(string $csvData): bool
    {
        // unfortunately, str_getcsv returns an empty row of data when passed an empty string.....
        if (empty($csvData)) {
            $this->setParsedValue([]);
            return true;
        }

        // if line termination is not already set and we cannot detect it then bail out
        if (!isset($this->lineTermination) && (!$this->detectLineTermination($csvData))) {
            throw new LineTerminationDetectionException();
        }

        // trim the line termination off the end of the string otherwise explode will create
        // an extra empty record at the end of the record set
        $rows = explode($this->getLineTermination(), trim($csvData));

        if ($this->getFirstRowContainsColumnHeadings()) {
            $this->setColumnHeadingsFromFirstRow($rows[0]);
            array_shift($rows);
        }

        foreach ($rows as $index => $row) {
            $rows[$index] = $this->parseRow($row);
        }
        $this->setParsedValue($rows);
        return true;
    }
}
