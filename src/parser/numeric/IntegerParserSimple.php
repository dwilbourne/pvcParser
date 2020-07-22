<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric;

use NumberFormatter;
use pvc\msg\UserMsg;
use pvc\parser\Parser;
use pvc\parser\ParserInterface;

/**
 * Class IntegerParserSimple
 */
class IntegerParserSimple extends Parser implements ParserInterface
{
    /**
     * @var NumberFormatter
     */
    protected NumberFormatter $frmtr;

    /**
     * IntegerParserSimple constructor.
     */
    public function __construct()
    {
        $this->frmtr = new NumberFormatter('en-US', NumberFormatter::PATTERN_DECIMAL);
        $this->frmtr->setAttribute(NumberFormatter::PARSE_INT_ONLY, 1);
        $this->frmtr->setAttribute(NumberFormatter::GROUPING_USED, 0);
    }

    /**
     * @function parse
     * @param string $fileContents
     * @return bool
     */
    public function parse(string $fileContents): bool
    {
        $pos = 0;
        $expectedPos = strlen($fileContents);
        $result = $this->frmtr->parse($fileContents, NumberFormatter::TYPE_INT64, $pos);

        if ($pos == $expectedPos) {
            $this->setParsedValue($result);
            $this->setErrmsg(null);
            return true;
        } else {
            $msgText = '%s is not a valid integer.';
            $vars = [$fileContents];
            $msg = new UserMsg($vars, $msgText);
            $this->setErrmsg($msg);
            return false;
        }
    }
}
