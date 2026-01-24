<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\boolean;

use pvc\parser\Parser;

/**
 * Parse either the word 'true' or 'false' into a boolean.
 *
 * Class ParserBooleanTrueFalse.
 * @extends Parser<bool>
 */
class ParserBooleanTrueFalse extends Parser
{
    protected bool $caseSensitive = false;

    public function isCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function setCaseSensitive(bool $caseSensitive): void
    {
        $this->caseSensitive = $caseSensitive;
    }

    /**
     * @function parse
     * @param string $data
     * @return bool
     */
    protected function parseValue(string $data): bool
    {
        if (!$this->isCaseSensitive()) {
            $data = strtolower($data);
        }

        if ($data === 'true') {
            $this->parsedValue = true;
            return true;
        }

        if ($data === 'false') {
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
        return [($this->isCaseSensitive() ? '' : 'not ').'case-sensitive'];
    }
}
