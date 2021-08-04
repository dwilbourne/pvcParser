<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\url;

use pvc\msg\Msg;

class InvalidQueryStringMessage extends Msg
{
    public function __construct(string $badParamPair)
    {
        $vars = [$badParamPair];
        $msgText = '%s is not a valid querystring parameter name / value pair and cannot be be parsed.';
        parent::__construct($vars, $msgText);
    }
}
