<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\url;

use pvc\interfaces\http\UrlInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\Parser;

/**
 * Class UrlParser
 * @extends Parser<UrlInterface>
 */
class ParserUrl extends Parser
{
    protected UrlInterface $url;

    public function __construct(MsgInterface $msg, UrlInterface $url)
    {
        parent::__construct($msg);
        $this->url = $url;
    }

    public function getUrl(): UrlInterface
    {
        return $this->url;
    }

    public function setUrl(UrlInterface $url): void
    {
        $this->url = $url;
    }

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
            $this->url->setAttributesFromArray($parsedResult);
            $this->parsedValue = $this->url;
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
