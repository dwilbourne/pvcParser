<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\parsers;

use pvc\msg\Msg;
use pvc\msg\UserMsg;

/**
 * Class ParserCurrency
 */
class NumberParserCurrency extends NumberParser
{
    public function createParseErrMsg(string $value): void
    {
        $msgText = 'Unable to parse value = %s into a currency value.';
        $vars = [$value];
        $msg = new UserMsg($vars, $msgText);
        $this->setErrmsg($msg);
    }
}
