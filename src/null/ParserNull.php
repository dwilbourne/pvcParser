<?php

namespace pvc\parser\null;

use pvc\interfaces\msg\MsgInterface;
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

    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'null';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}