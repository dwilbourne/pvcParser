<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\boolean;

use pvc\interfaces\msg\MsgInterface;
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
     * setMsgContent
     * @param MsgInterface $msg
     */
    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'not_boolean_one_zero';
        $msgParameters = [];
        $domain = 'Parser';
        $msg->setContent($msgId, $msgParameters, $domain);
    }
}
