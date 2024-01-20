<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\csv;

use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\DuplicateColumnHeadingException;
use pvc\parser\err\InvalidColumnHeadingException;
use pvc\parser\err\InvalidEscapeCharacterException;
use pvc\parser\err\InvalidFieldDelimiterException;
use pvc\parser\err\InvalidFieldEnclosureCharException;
use pvc\parser\err\InvalidLineTerminationException;
use pvc\parser\err\NonExistentColumnHeadingException;
use pvc\parser\Parser;

/**
 * Class CsvParser.  This class restricts record termination characters to be either LF or CRLF (windows).
 * @extends Parser<array>
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
    protected string $lineTermination = "\n";

    /**
     * @var non-empty-string
     */
    protected string $fieldDelimiterChar = ",";

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

    protected bool $autoDetectLineTermination = true;

    /**
     * setColumnHeadings
     * @param array<string> $columnHeadings
     * @throws \pvc\parser\err\InvalidColumnHeadingException
     */
    public function setColumnHeadings(array $columnHeadings) : void
    {
        foreach ($columnHeadings as $columnHeading) {
            /** characters in the column heading must all be printable */
            if (!ctype_print($columnHeading)) {
                throw new InvalidColumnHeadingException();
            }
            /** no duplicate column headings since they become indices into an array */
            if (in_array($columnHeading, $this->columnHeadings)) {
                throw new DuplicateColumnHeadingException($columnHeading);
            }
            $this->columnHeadings[] = $columnHeading;
        }
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

    protected function isSingleVisibleCharacter(string $char): bool
    {
        return (ctype_graph($char) && (strlen($char) == 1));
    }

    /**
     * setFieldDelimiterChar
     * @param string $delimiterChar
     * @throws \pvc\parser\err\InvalidFieldDelimiterException
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
     * @throws \pvc\parser\err\InvalidFieldEnclosureCharException
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
     * @throws \pvc\parser\err\InvalidEscapeCharacterException
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

    public function getAutoDetectLineTermination(): bool
    {
        return $this->autoDetectLineTermination;
    }

    public function setAutoDetectLineTermination(bool $autoDetectLineTermination): void
    {
        $this->autoDetectLineTermination = $autoDetectLineTermination;
    }


    /**
     * try to detect line termination.  Inevitably, you have to make a choice about how to handle weird cases where
     * there are CRs and no succeeding LFs or a mix of CRLFs and plain LFs.  In this algorithm,
     * if *every* occurrence of LF is preceded by a CR then it is a windows file.
     *
     * If there is *any* instance of an LF which is not preceded by a CR then it is non-windows.
     *
     * if there are no line feeds at all, then return false unless the string is empty.
     */

    public function detectLineTermination(string $csvData) : bool
    {
        $strlen = strlen($csvData);

        if ($strlen == 0) {
            // there can be no line termination character
            return true;
        }

        /**
         * are there any LFs in the file at all?
         */
        $linefeedsExist = false;
        for ($i = 0; $i < strlen($csvData) - 1; $i++) {
            if ($csvData[$i] == "\n") {
                $linefeedsExist = true;
                /** no need to run the loop any further */
                break;
            }
        }

        /**
         * if there are no line feeds, termination character is not detectable via this algorithm
         */
        if (!$linefeedsExist) {
            return false;
        }

        /**
         * is every LF preceded by a CR?
         */
        $allLFsPrecededByCR = true;

        if ($strlen == 1) {
            /** if the length of the file is 1 then there is no possibility of a two character CRLF sequence */
            $allLFsPrecededByCR = false;
        } else {
            for ($i = 1; $i < $strlen; $i++) {
                if (($csvData[$i] == "\n") && ($csvData[$i - 1] != "\r")) {
                    $allLFsPrecededByCR = false;
                    /** no need to run the loop further */
                    break;
                }
            }
        }

        /**
         * set the line termination character and return true
         */
        if ($allLFsPrecededByCR) {
            $this->setLineTermination("\r\n");
        } else {
            $this->setLineTermination("\n");
        }

        return true;
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
     * @param string $data
     * @return bool
     * @throws \pvc\parser\err\NonExistentColumnHeadingException
     */
    protected function parseValue(string $data): bool
    {
        // unfortunately, str_getcsv returns an empty row of data when passed an empty string.....
        if (empty($data)) {
            $this->parsedValue = [];
            return true;
        }

        if ($this->autoDetectLineTermination) {
            $this->detectLineTermination($data);
        }

        // trim the line termination off the end of the string otherwise explode will create
        // an extra empty record at the end of the record set
        $rows = explode($this->getLineTermination(), trim($data, $this->getLineTermination()));

        if ($this->getFirstRowContainsColumnHeadings()) {
            $this->setColumnHeadingsFromFirstRow($rows[0]);
            array_shift($rows);
        }

        foreach ($rows as $index => $row) {
            $rows[$index] = $this->parseRow($row);
        }
        $this->parsedValue = $rows;
        return true;
    }

    protected function setMsgContent(MsgInterface $msg): void
    {
        /**
         * no message to set because parseValue in this class always returns true
         */
    }
}
