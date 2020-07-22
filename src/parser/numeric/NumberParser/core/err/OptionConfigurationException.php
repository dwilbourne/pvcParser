<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core\err;

use pvc\msg\ErrorExceptionMsg;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\err\throwable\Throwable;
use pvc\parser\numeric\NumberParser\core\NumberFormatterOption;

class OptionConfigurationException extends Exception implements Throwable
{
    public function __construct(NumberFormatterOption $option, \Throwable $previous = null)
    {
        $msgText = '';
        $msgText .= 'Error in the composition of the NumberFormatter option.  ';
        $msgText .= 'Method, attribute and/or value are incorrect or incompatible';
        $msgText .= 'method = %s; attribute = %s; value = %s.';
        $vars = [$option->getMethod(), $option->getAttribute(), $option->getValue()];
        $msg = new ErrorExceptionMsg($vars, $msgText);

        $code = ec::OPTION_CONFIGURATION_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
