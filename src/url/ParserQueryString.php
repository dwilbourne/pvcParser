<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\url;

use pvc\err\stock\Exception;
use pvc\http\err\InvalidQuerystringParamNameException;
use pvc\http\url\QueryString;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\InvalidQuerystringSeparatorException;
use pvc\parser\Parser;

/**
 * Class ParserQueryString
 *
 * @extends Parser<Querystring>
 *
 * parse_str has some issues, like it will mangle a parameter name in order to get it to conform to a PHP
 * variable name.  This class does not do that.  The parameter names end up as indices in an associative array.
 *
 * See the discussion in the QueryString class for more information, but note that this class mirrors the behavior of
 * http_build_query in some ways.  Just as http_build_query cannot generate a querystring with duplicate parameter
 * names (e.g. it cannot produce '?a=4&a=5', this class cannot parse duplicate parameters as separates.  If it runs
 * into '?a=4&a=5', the value of a will be 5.
 *
 */
class ParserQueryString extends Parser
{
    protected string $separator = '&';

    protected QueryString $qstr;

    public function __construct(MsgInterface $msg, QueryString $qstr)
    {
        parent::__construct($msg);
        $this->qstr = $qstr;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setSeparator(string $separator): void
    {
        if (empty($separator)) {
            throw new InvalidQuerystringSeparatorException();
        }
        $this->separator = $separator;
    }

    /**
     * parseValue
     * @param string $data
     * @return bool
     * @throws InvalidQuerystringParamNameException
     *
     */
    public function parseValue(string $data): bool
    {
        $params = [];
        $data = trim($data, '?');

        $paramStrings = explode($this->getSeparator(), $data);

        foreach ($paramStrings as $paramString) {
            $array = explode('=', $paramString);

            /**
             * cannot have a string like 'a' or 'a=1=2'
             */
            if (count($array) != 2) {
                return false;
            }

            $paramName = $array[0];
            $paramValue = $array[1];

            /**
             * parameter value cannot be null, that means you had a string like 'a='
             */
            if (empty($paramValue)) {
                return false;
            }

            /**
             * if the parameter name is duplicated in the querystring, this results in the last value being used
             */
            $params[$paramName] = $paramValue;
        }

        try {
            $this->qstr->setParams($params);
            $this->parsedValue = $this->qstr;
            return true;
        } catch (Exception $e) {
            /** swallow the exception - parsers just return false if they fail */
            return false;
        }
    }

    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'invalid_querystring';
        $msgParameters = [];
        $domain = 'Parser';
        $msg->setContent($domain, $msgId, $msgParameters);
    }
}
