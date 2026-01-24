<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\parser\numeric;

use NumberFormatter;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\parser\err\InvalidReturnTypeException;
use pvc\parser\Parser;

/**
 * Class NumericParser
 * @extends Parser<DataType>
 * @template DataType
 *
 * This is a wrapper for php's NumberFormatter class.  The child classes of this class simply set up different
 * flavors of NumberFormatter for some common scenarios.  You can access the underlying NumberFormatter object and
 * further manipulate it to accommodate whatever special use case you have.
 *
 * Unfortunately, the underlying NumberFormatter class is created with a non-optional locale argument which is
 * immutable.  This class exhibits the same behavior.  The reason is that if we were to create new NumberFormatters
 * each time a user changed the locale of this class, all other changes that were made to the old formatter would be
 * lost.
 */
abstract class NumericParser extends Parser
{
    protected NumberFormatter $frmtr;

    /**
     * the locale attribute is 'de-normalized' in the sense that it is stored as an attribute but also embedded
     * in the formatter at constrfuction time.  It was tempting to just remove the attribute and have the getter
     * return the locale from the formatter.  BUT, we want to set and get a Locale object (pvc\intl\Locale), NOT a
     * string, so we keep the object handy.
     */

    protected LocaleInterface $locale;

    protected int $returnType = NumberFormatter::TYPE_INT64;

    /**
     * @var array <int>
     * in this day and age we are not listing the 32 bit return types as being valid.  Also, as of php 8.3. the
     * currency type is deprecated.  The only choices should be integer or float
     */
    protected array $validReturnTypes = [
        NumberFormatter::TYPE_INT64,
        NumberFormatter::TYPE_DOUBLE,
    ];

    public function __construct(MsgInterface $msg, LocaleInterface $locale, NumberFormatter $frmtr)
    {
        parent::__construct($msg);
        $this->frmtr = $frmtr;
        $this->locale = $locale;
        /**
         * grouping separator is allowed but not required
         */
        $this->allowGroupingSeparator(true);
    }

    /**
     * allowGroupingSeparator
     * @param bool $allowed
     */
    public function allowGroupingSeparator(bool $allowed): void
    {
        if (!$allowed) {
            $this->getFrmtr()->setAttribute(NumberFormatter::GROUPING_USED, 0);
        } else {
            $this->getFrmtr()->setAttribute(NumberFormatter::GROUPING_USED, 1);
        }
    }

    public function getFrmtr(): NumberFormatter
    {
        return $this->frmtr;
    }

    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }

    public function isGroupingSeparatorAllowed(): bool
    {
        return (bool)$this->getFrmtr()->getAttribute(NumberFormatter::GROUPING_USED);
    }

    /**
     * @function parseValue
     * @param string $data
     * @return bool
     */
    public function parseValue(string $data): bool
    {
        $pos = 0;
        $expectedPos = strlen($data);
        /** @var DataType $result */
        $result = $this->frmtr->parse($data, $this->getReturnType(), $pos);

        /**
         * NumberFormatter 'fails gracefully' if it successfully parses a part of a string, e.g. it will not throw
         * an exception if it parses the first x characters of the string into the return type and can't parse any
         * more from the x + 1 character to the end of the string.  The $pos variable holds the offset of the last
         * character successfully parsed.
         */
        if (($pos == $expectedPos) && ($result !== false)) {
            $this->parsedValue = $result;
            return true;
        } else {
            return false;
        }
    }

    /**
     * getReturnType
     * @return int
     * make integer the default return type
     */
    public function getReturnType(): int
    {
        return $this->returnType;
    }

    public function setReturnType(int $returnType): void
    {
        if (!in_array($returnType, $this->validReturnTypes)) {
            throw new InvalidReturnTypeException();
        }
        $this->returnType = $returnType;
    }
}
