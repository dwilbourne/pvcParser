<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\parsers;

use pvc\msg\UserMsg;

/**
 * Class NumberParserIntegerDecimal.  This class will parse a number like 123.0 as an integer.  In other words,
 * if it evals to an integer, then it is one.  The return type is an integer.
 */
class NumberParserIntegerDecimal extends NumberParserDecimal
{
    public function parse(string $input): bool
    {
        if (!parent::parse($input)) {
            return false;
        }
        return (floor($this->getParsedValue()) == $this->getParsedValue());
    }

    public function createPrecisionErrmsg(): void
    {
        $msgText = 'Value could not be parsed into an integer.';
        $vars = [];
        $msg = new UserMsg($vars, $msgText);
        $this->setErrmsg($msg);
    }
}
