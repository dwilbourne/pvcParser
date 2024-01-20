<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\date_time;

use pvc\intl\Locale;
use pvc\parser\Parser;
use pvc\parser\ParserInterface;

/**
 * Class ParserDateTime
 */
abstract class ParserDateTime extends Parser implements ParserInterface
{
    /**
     * @var Locale
     */
    protected Locale $locale;

    /**
     * ParserDateTime constructor.
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->setLocale($locale);
    }

    /**
     * @function setLocale
     * @param Locale $locale
     */
    public function setLocale(Locale $locale) : void
    {
        $this->locale = $locale;
    }

    /**
     * @function getLocale
     * @return Locale
     */
    public function getLocale(): Locale
    {
        return $this->locale;
    }
}
