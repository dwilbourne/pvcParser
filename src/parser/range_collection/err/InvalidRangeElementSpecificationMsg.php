<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\range_collection\err;

use pvc\msg\Msg;

/**
 * Class NumberRangeException
 */
class InvalidRangeElementSpecificationMsg extends Msg
{
    public function __construct(string $patternDescription, $providedRangeSpec)
    {
        $msgText = 'Invalid range specification:  pattern must be %s.  Spec provided = %s';
        $vars = [$patternDescription, $providedRangeSpec];
        parent::__construct($vars, $msgText);
    }
}
