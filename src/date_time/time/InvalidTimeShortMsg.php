<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\date_time\time;

use pvc\msg\UserMsg;

/**
 * Class InvalidDateShortMsg
 */
class InvalidTimeShortMsg extends UserMsg
{
    public function __construct(string $timeString)
    {
        $vars = [$timeString];
        $msgText = '%s is not a valid short time (hours : minutes or hours : minutes : am/pm.)';
        parent::__construct($vars, $msgText);
    }
}
