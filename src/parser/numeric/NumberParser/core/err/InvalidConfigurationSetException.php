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

/**
 * Class InvalidConfigurationSetException
 */
class InvalidConfigurationSetException extends Exception implements Throwable
{
    public function __construct(\Throwable $previous = null)
    {
        $vars = [];
        $msgText = 'Error trying to add invalid configuration set to configuration block.';
        $msg = new ErrorExceptionMsg($vars, $msgText);
        $code = ec::INVALID_CONFIGURATION_SET_EXCEPTION;
        parent::__construct($msg, $code, $previous);
    }
}
