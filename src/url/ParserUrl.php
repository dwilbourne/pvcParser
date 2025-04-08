<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\url;

use pvc\interfaces\http\UrlInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\interfaces\validator\ValTesterInterface;
use pvc\parser\Parser;

/**
 * Class UrlParser
 * @extends Parser<UrlInterface>
 */
class ParserUrl extends Parser
{
    protected UrlInterface $url;

    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $urlTester;

    /**
     * @param MsgInterface $msg
     * @param UrlInterface $url
     * @param ValTesterInterface<string> $urlTester
     */
    public function __construct(MsgInterface $msg, UrlInterface $url, ValTesterInterface $urlTester)
    {
        parent::__construct($msg);
        $this->url = $url;
        $this->urlTester = $urlTester;
    }

    /**
     * parseValue
     * @param string $data
     * @return bool
     */
    public function parseValue(string $data): bool
    {
        /**
         * parse_url will happily mangle the results of urls which are not well-formed, so we have to validate the
         * url first
         */
        if (!$this->urlTester->testValue($data)) {
            return false;
        }

        if (false === ($urlParts = parse_url($data))) {
            return false;
        }

        $this->url->setAttributesFromArray($urlParts);
        $this->parsedValue = $this->url;
        return true;
    }

    /**
     * setMsgContent
     * @param MsgInterface $msg
     */
    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'invalid_url';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}
