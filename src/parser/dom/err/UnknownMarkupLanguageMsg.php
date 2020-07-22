<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\dom\err;

use pvc\msg\UserMsg;

/**
 * Class MarkupLanguageException
 */
class UnknownMarkupLanguageMsg extends UserMsg
{
    public function __construct()
    {
        $msgText = 'Markup Language for document not specified and unable to discover language through inspection.';
        parent::__construct([], $msgText);
    }
}
