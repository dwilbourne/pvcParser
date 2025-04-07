<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser\url;

use pvc\interfaces\http\QueryStringInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\interfaces\parser\ParserQueryStringInterface;
use pvc\parser\err\InvalidQuerystringSeparatorException;
use pvc\parser\Parser;
use Throwable;

/**
 * Class ParserQueryString
 *
 * @extends Parser<QueryStringInterface>
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
class ParserQueryString extends Parser implements ParserQueryStringInterface
{
    /**
     * @var non-empty-string
     */
    protected string $separator = '&';

    /**
     * @var QueryStringInterface
     */
    protected QueryStringInterface $qstr;

    /**
     * @param MsgInterface $msg
     * @param QueryStringInterface $qstr
     */
    public function __construct(MsgInterface $msg, QueryStringInterface $qstr)
    {
        parent::__construct($msg);
        $this->qstr = $qstr;
    }

    /**
     * getQueryString
     * @return QueryStringInterface
     */
    public function getQueryString(): QueryStringInterface
    {
        return $this->qstr;
    }

    /**
     * setQueryString
     * @param QueryStringInterface $queryString
     */
    public function setQueryString(QueryStringInterface $queryString): void
    {
        $this->qstr = $queryString;
    }

    /**
     * getSeparator
     * @return non-empty-string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * setSeparator
     * @param non-empty-string $separator
     * @throws InvalidQuerystringSeparatorException
     */
    public function setSeparator(string $separator): void
    {
        if (empty($separator)) {
            throw new InvalidQuerystringSeparatorException();
        }
        $this->separator = $separator;
    }

    /**
     * parseValue
     * parses a querystring to the html standard
     * @param string $data
     * @return bool
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
             * cannot have a string like 'a=1=2'.  Need 0 or 1 equals signs.  Zero equals signs is a parameter with no
             * value attached
             */
            if (count($array) > 2) {
                return false;
            }

            $paramName = $array[0];
            $paramValue = $array[1] ?? '';

            /**
             * if the parameter name is duplicated in the querystring, this results in the last value being used
             */
            $params[$paramName] = $paramValue;
        }

        try {
            $this->getQueryString()->setParams($params);
        } catch (Throwable $e) {
            /** swallow the exception - parsers just return false if they fail */
            return false;
        }
        $this->parsedValue = $this->qstr;
        return true;
    }

    protected function setMsgContent(MsgInterface $msg): void
    {
        $msgId = 'invalid_querystring';
        $msgParameters = [];
        $msg->setContent($this->getMsgDomain(), $msgId, $msgParameters);
    }
}
