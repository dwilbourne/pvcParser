<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvc\parser\messages;

return [
    'not_boolean_loose' => 'Value is not a (loose) boolean: must be true/false, yes/no, 1/0, on/off',
    'not_boolean_one_zero' => 'Value must be either 1 or 0',
    'not_true_or_false' => 'Value must be either 1 or 0, ${caseSensitive}',
    'csv_parser_failure' => 'csv parser unable to parse ${filePath}',
    'not_short_date' => 'value is not a short date',
    'not_short_date_time' => 'value is not a short date / time',
    'not_integer' => 'value is not an integer',
    'not_decimal' => 'value is not a decimal number',
    'invalid_url' => 'string could not be parsed into a url',
    'invalid_querystring' => 'invalid querystring could not be processed',
];
