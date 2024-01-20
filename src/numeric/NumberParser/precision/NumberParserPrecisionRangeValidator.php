<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\precision;

use pvc\msg\UserMsg;
use pvc\validator\base\ValidatorInterface;

/**
 * Class NumberParserPrecisionRangeValidator
 */
class NumberParserPrecisionRangeValidator implements ValidatorInterface
{
    /**
     * @function validate
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool
    {
        return (is_int($data) && ($data >= -1));
    }

    /**
     * @function getErrMsg
     * @return UserMsg
     */
    public function getErrMsg(): UserMsg
    {
        $vars = [];
        $msgText = 'Range elements must be integers greater than or equal to -1.';
        return new UserMsg($vars, $msgText);
    }
}
