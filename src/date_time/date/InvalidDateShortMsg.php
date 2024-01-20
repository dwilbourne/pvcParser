<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\date_time\date;

use pvc\msg\UserMsg;

/**
 * Class InvalidDateShortMsg
 */
class InvalidDateShortMsg extends UserMsg
{
    /**
     * InvalidDateShortMsg constructor.
     * @param string $dateString
     */
    public function __construct(string $dateString)
    {
        $vars = [$dateString];
        $msgText = '%s is not a valid short date.';
        parent::__construct($vars, $msgText);
    }
}
