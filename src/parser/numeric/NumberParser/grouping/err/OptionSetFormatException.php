<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\grouping\err;

use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\Throwable;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;

/**
 * Class InvalidLocaleException
 */
class OptionSetFormatException extends InvalidArgumentException implements Throwable
{
    public function __construct(string $msgText)
    {
        $vars = [];
        $msg = new ErrorExceptionMsg($vars, $msgText);

        $code = ec::OPTION_SET_FORMAT_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
