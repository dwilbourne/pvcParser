<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @noinspection PhpCSValidationInspection
 */
declare(strict_types=1);

namespace pvc\parser\err;

use pvc\err\XDataAbstract;

/**
 * Class _ParserXData
 */
class _ParserXData extends XDataAbstract
{

    public function getLocalXCodes(): array
    {
        return [
            InvalidMsgIdException::class => 1000,
            InvalidColumnHeadingException::class => 1001,
            DuplicateColumnHeadingException::class => 1002,
            InvalidLineTerminationException::class => 1003,
            InvalidFieldDelimiterException::class => 1004,
            InvalidFieldEnclosureCharException::class => 1005,
            InvalidEscapeCharacterException::class => 1006,
            NonExistentColumnHeadingException::class => 1007,
            NonExistentFilePathException::class => 1008,
            InvalidReturnTypeException::class => 1009,
            InvalidQuerystringSeparatorException::class => 1010,
            InvalidDateTimeTypeException::class => 1011,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            InvalidMsgIdException::class => 'msgid ${msgId} not found in messages file / array',
            InvalidColumnHeadingException::class => 'Invalid column heading.  Must be printable characters',
            DuplicateColumnHeadingException::class => 'Column headings must be unique - duplicated value \'${heading}\'',
            InvalidLineTerminationException::class => 'Invalid line termination character(s) specified.  Should be CR or CRLF (Windows).',
            InvalidFieldDelimiterException::class => 'field delimiter must be a single character and be visible',
            InvalidFieldEnclosureCharException::class => 'Field enclosure character must be a single character and be visible',
            InvalidEscapeCharacterException::class => 'Escape character must be a single character',
            NonExistentColumnHeadingException::class => 'Flag set to get column headings from first row of data but data is empty.',
            NonExistentFilePathException::class => 'Cannot open file ${filePath}',
            InvalidDateTimeTypeException::class => 'Invalid Date Type / Time Type specified - see documentation for the php IntlDateFormatter class',
            InvalidReturnTypeException::class => 'Invalid return type, must be either NumberFormatter::TYPE_INT64, or NumberFormatter::TYPE_DOUBLE',
            InvalidQuerystringSeparatorException::class => 'Querystring separator cannot be empty.',
        ];
    }
}