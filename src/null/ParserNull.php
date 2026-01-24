<?php

namespace pvc\parser\null;

use pvc\parser\Parser;

/**
 * Input must be an empty string to parse correctly.
 *
 * @extends Parser<null>
 */
class ParserNull extends Parser
{

    /**
     * parseValue
     *
     * @param  string  $data
     *
     * @return bool
     */
    protected function parseValue(string $data): bool
    {
        if ($data == '') {
            $this->parsedValue = null;
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getMsgId(): string
    {
        return 'null';
    }

    /**
     * @inheritDoc
     */
    protected function getMsgParameters(): array
    {
        return [];
    }
}