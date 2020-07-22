<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\boolean;

use pvc\msg\UserMsg;
use pvc\parser\Parser;
use pvc\parser\ParserInterface;

/**
 * Input must be either a 1 or a 0 to parse correctly.
 *
 * Class ParserBooleanOneZero
 */
class ParserBooleanOneZero extends Parser implements ParserInterface
{
    /**
     * @function parse
     * @param string $input
     * @return bool
     */
    public function parse(string $input): bool
    {
        if ($input == '1') {
            $this->setParsedValue(true);
            $this->setErrmsg(null);
            return true;
        }
        if ($input == '0') {
            $this->setParsedValue(false);
            $this->setErrmsg(null);
            return true;
        } else {
            $msgText = 'value is not boolean (strict) - must be either 0 or 1. Value provided = %s';
            $vars = [$input];
            $msg = new UserMsg($vars, $msgText);
            $this->setErrmsg($msg);
            return false;
        }
    }
}
