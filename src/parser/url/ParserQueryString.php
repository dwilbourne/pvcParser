<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\url;

use pvc\msg\MsgRetrievalInterface;
use pvc\parser\ParserInterface;

/**
 * This class is designed to encapsulate the functionality available in parse_str.  parse_str has some behaviors
 * that are not compliant with CGI standards (overwriting array entries defined in the querystring) and it
 * mangles variable names.  Encapsulation allows you to modify these behaviors if you want.  See the pvc php language
 * tests for an illustration of the behaviors.
 */

class ParserQueryString implements ParserInterface
{
    protected array $parsedValues = [];

    /**
     * parse
     * @param string $data
     * @return bool
     */
    public function parse(string $data): bool
    {
        // parse_str never fails
        parse_str($data, $this->parsedValues);
        return true;
    }

    public function getParsedValue(): array
    {
        return $this->parsedValues;
    }

    public function getErrmsg(): ?MsgRetrievalInterface
    {
        return null;
    }
}
