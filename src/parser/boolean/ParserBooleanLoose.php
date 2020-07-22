<?php declare(strict_types = 1);

namespace pvc\parser\boolean;

use pvc\msg\UserMsg;
use pvc\parser\Parser;
use pvc\parser\ParserInterface;

/**
 * ParserBoolean will take a string and try to convert it into a semantically appropriate boolean value
 *
 * Class ParserBooleanLoose
 */
class ParserBooleanLoose extends Parser implements ParserInterface
{


    /**
     *
     * Using filter_var, parse will try and be smart about interpreting Yes, no, true, false, etc
     * appropriately.  Note that the FILTER_NULL_ON_FAILURE flag is critical to this working right because
     * FILTER_VALIDATE_BOOLEAN is actually a sanitizer and is returning the (typecast) value of $value, NOT whether
     * in fact $value can be interpreted as boolean or not. There is
     * no flag FILTER_SANITIZE_BOOLEAN.  It seems to be a complaint in the PHP community...
     *
     * @function parse
     * @param string $fileContents
     * @return bool
     */
    public function parse(string $fileContents): bool
    {
        $result = filter_var($fileContents, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($result)) {
            $msgText = 'Value is not a (loose) boolean: must be true / false, yes / no, etc). Value provided = %s';
            $vars = [$fileContents];
            $msg = new UserMsg($vars, $msgText);
            $this->setErrmsg($msg);
            return false;
        }
        $this->setParsedValue($result);
        $this->setErrmsg(null);
        return true;
    }
}
