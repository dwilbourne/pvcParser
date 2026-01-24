<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\boolean;

use pvc\parser\Parser;

/**
 * ParserBoolean will take a string and try to convert it into a semantically appropriate boolean value
 *
 * Class ParserBooleanLoose
 * @extends Parser<bool>
 */
class ParserBooleanLoose extends Parser
{
    /**
     *
     * Using filter_var, parse will try and be smart about interpreting Yes, no, true, false, etc
     * appropriately.  Note that the FILTER_NULL_ON_FAILURE flag is critical to this working right because
     * FILTER_VALIDATE_BOOLEAN is actually a sanitizer and is returning the (typecast) value of $value, NOT whether
     * in fact $value can be interpreted as boolean or not. There is
     * no flag FILTER_SANITIZE_BOOLEAN.  It seems to be a complaint in the PHP community...
     *
     * @function parseValue
     * @param string $data
     * @return bool
     *
     */
    protected function parseValue(string $data): bool
    {
        $result = filter_var($data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($result)) {
            return false;
        }
        $this->parsedValue = $result;
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getMsgId(): string
    {
        return 'not_boolean_loose';
    }

    /**
     * @inheritDoc
     */
    protected function getMsgParameters(): array
    {
        return [];
    }

}
