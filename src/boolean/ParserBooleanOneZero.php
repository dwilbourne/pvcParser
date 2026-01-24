<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\boolean;

use pvc\parser\Parser;

/**
 * Input must be either a 1 or a 0 to parse correctly.
 *
 * Class ParserBooleanOneZero
 * @extends  Parser<bool>
 */
class ParserBooleanOneZero extends Parser
{
    /**
     * @function parseValue
     * @param string $data
     * @return bool
     */
    protected function parseValue(string $data): bool
    {
        if ($data == '1') {
            $this->parsedValue = true;
            return true;
        }
        if ($data == '0') {
            $this->parsedValue = false;
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getMsgId(): string
    {
        return 'not_boolean_one_zero';
    }

    /**
     * @inheritDoc
     */
    protected function getMsgParameters(): array
    {
        return [];
    }
}
