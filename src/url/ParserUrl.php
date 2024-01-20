<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\url;

use pvc\msg\MsgRetrievalInterface;
use pvc\parser\ParserInterface;
use pvc\url\Url;

/**
 * Class UrlParser
 * @package pvc\parser\url
 */
class ParserUrl implements ParserInterface
{
    protected Url $url;
    protected InvalidUrlMsg $errMsg;

    public function parse(string $data): bool
    {
        $parsedResult = parse_url($data);

        if (false === $parsedResult) {
            $this->errMsg = new InvalidUrlMsg($data);
            return false;
        }

        $this->url = new Url();
        $this->url->setAttributesFromArray($parsedResult);
        return true;
    }

    public function getParsedValue()
    {
        return $this->url;
    }

    public function getErrmsg(): ?MsgRetrievalInterface
    {
        return $this->errMsg ?? null;
    }
}
