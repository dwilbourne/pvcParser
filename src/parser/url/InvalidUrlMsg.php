<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\url;

use pvc\msg\Msg;

/**
 * Class InvalidUrlMsg
 * @package pvc\parser\url
 */
class InvalidUrlMsg extends Msg
{
    /**
     * InvalidUrlMsg constructor.
     * @param string $badUrl
     */

    public function __construct(string $badUrl)
    {
        $vars = [$badUrl];
        $msgText = '%s is not a url and cannot be be parsed.';
        parent::__construct($vars, $msgText);
    }
}
