<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\dom\err;

use pvc\err\throwable\ErrorExceptionConstants as ec;
use pvc\err\throwable\exception\stock_rebrands\Exception;
use pvc\msg\ErrorExceptionMsg;

/**
 * Class InvalidMarkupLanguageException
 */
class InvalidMarkupLanguageException extends Exception
{
    /**
     * InvalidMarkupLanguageException constructor.
     */
    public function __construct()
    {
        $msgText = 'Invalid markup language specified.';
        $msg = new ErrorExceptionMsg([], $msgText);
        $code = ec::INVALID_MARKUP_LANGUAGE_EXCEPTION;
        $previous = null;
        parent::__construct($msg, $code, $previous);
    }
}
