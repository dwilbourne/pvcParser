<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\parsers\err;

use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use Throwable;

/**
 * Class CalcNumDecimalPlacesException
 */
class CalcNumDecimalPlacesException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        $msgVars = [];
        $msgText = 'Separator symbol can appear only once in the pattern.';
        $msg = new ErrorExceptionMsg($msgVars, $msgText);
        $code = ec::CALC_NUM_DECIMAL_PLACES_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
