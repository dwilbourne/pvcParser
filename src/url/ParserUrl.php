<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\url;

use pvc\http\url\Url;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\Parser;

/**
 * Class UrlParser
 */
class ParserUrl extends Parser
{
    /**
     * parseValue
     * @param string $data
     * @return bool
     */
    public function parseValue(string $data): bool
    {
        $parsedResult = parse_url($data);

        if (false === $parsedResult) {
            return false;
        } else {
            $this->parsedValue = new Url($parsedResult);
            return true;
        }
    }

    /**
     * setMsgContent
     * @param MsgInterface $msg
     */
    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'invalid_url';
        $msgParameters = [];
        $domain = 'Parser';
        $this->msg->setContent($domain, $msgId, $msgParameters);
    }
}
