<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\prefix_suffix\err;

use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use Throwable;

/**
 * Class InvalidFormatException
 */
class InvalidFormatException extends InvalidArgumentException
{
    /**
     * InvalidFormatException constructor.
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        $msgText = 'At least one argument to the setFormat method must be non-null.';
        $vars = [];
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::INVALID_FORMAT_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
