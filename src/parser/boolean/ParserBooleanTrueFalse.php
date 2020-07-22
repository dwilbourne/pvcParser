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
 * Parse either the word 'true' or 'false' into a boolean (case insensitive).
 *
 * Class ParserBooleanTrueFalse.  Input must be either 'true' or 'false' (not case sensitive)
 */
class ParserBooleanTrueFalse extends Parser implements ParserInterface
{
    /**
     * @function parse
     * @param string $input
     * @return bool
     */
    public function parse(string $input): bool
    {
        $v = strtolower($input);
        if ($v == 'true') {
            $this->setParsedValue(true);
            $this->setErrmsg(null);
            return true;
        }
        if ($v == 'false') {
            $this->setParsedValue(false);
            $this->setErrmsg(null);
            return true;
        } else {
            $msgText = 'value is not boolean (strict) - must be either true or false (not case sensitive).  ';
            $msgText .= 'Value provided = %s';
            $vars = [$input];
            $msg = new UserMsg($vars, $msgText);
            $this->setErrmsg($msg);
            return false;
        }
    }
}
