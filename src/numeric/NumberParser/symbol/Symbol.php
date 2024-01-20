<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\symbol;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolFromCharException;
use pvc\parser\numeric\NumberParser\symbol\err\SetSymbolTypeException;
use pvc\parser\numeric\NumberParser\symbol\err\UnsetSymbolValueException;

/**
 * The Symbol class and its children allow oyu to use a symbol in a pattern and have it quoted.
 *
 * It is far more typical that symbols do not need to be quoted, and so the quoting feature will rarely be used.
 * But it is possible to 'escape' the typical use case.
 *
 * Let's say you want to use the '+' symbol at the beginning of the pattern to signify a positive number.
 * If it is quoted (as in '+'#,##0.###), it is treated as a literal.  If it is not, then it is treated as a proxy for
 * whatever the value of $frmtr->getSymbol(\NumberFormatter::PLUS_SIGN_SYMBOL) is, and in theory
 * that value can change based on the locale.  That is the more typical use case since it is
 * locale-aware.  But if you set the $quoted parameter to true, you can set the symbol as
 * a literal.  The quoted parameter has no effect if the symbol being set is not a reserved symbol since
 * symbols which are not reserved are always treated as literals.  Also, you cannot 'set the symbol value'
 * (the equivalent of $frmtr->setSymbol(\NumberFormatter::PLUS_SIGN_SUMBOL, '&'), for example) if the
 * symbol being set is quoted because it is thereby a literal and there can be no substitution via setSymbol.
 *
 * Class Symbol
 */
class Symbol
{
    public const LITERAL = -1;

    /**
     * represented by one of the formatter constants PLUS_SIGN_SYMBOL, MINUS_SIGN_SYMBOL, etc. Can also be
     * the LITERAL constant defined above.
     *
     * @var int $symbolType .
     */
    protected int $symbolType;

    /**
     * string value, as in $frmtr->setSymbol($symbolType, $symbolValue), which will override any value which is
     * determined by the locale.  This is also the value of the pattern char if the symbol type is literal.
     *
     * @var string $symbolValue
     */
    protected string $symbolValue = '';

    /**
     * @var bool $quoted
     */
    protected bool $quoted = false;

    /**
     * @var string[]
     */
    protected array $patternReservedCharsMap = [
        NumberFormatter::PLUS_SIGN_SYMBOL => '+',
        NumberFormatter::MINUS_SIGN_SYMBOL => '-',
        NumberFormatter::CURRENCY_SYMBOL => '¤',
        NumberFormatter::INTL_CURRENCY_SYMBOL => '¤¤',
        NumberFormatter::PERCENT_SYMBOL => '%',
        NumberFormatter::PERMILL_SYMBOL => '‰',
        NumberFormatter::DECIMAL_SEPARATOR_SYMBOL => '.',
        NumberFormatter::MONETARY_SEPARATOR_SYMBOL => '.',
        NumberFormatter::GROUPING_SEPARATOR_SYMBOL => ',',
        NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL => ','
    ];

    /**
     * @var int[]
     */
    protected array $positionalChars = [
        self::LITERAL,
        NumberFormatter::PLUS_SIGN_SYMBOL,
        NumberFormatter::MINUS_SIGN_SYMBOL,
        NumberFormatter::CURRENCY_SYMBOL,
        NumberFormatter::INTL_CURRENCY_SYMBOL,
        NumberFormatter::PERCENT_SYMBOL,
        NumberFormatter::PERMILL_SYMBOL
    ];

    /**
     * @var int[]
     */
    protected array $decimalChars = [
        NumberFormatter::DECIMAL_SEPARATOR_SYMBOL,
        NumberFormatter::MONETARY_SEPARATOR_SYMBOL
    ];

    /**
     * @var int[]
     */
    protected array $groupingChars = [
        NumberFormatter::GROUPING_SEPARATOR_SYMBOL,
        NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL
    ];

    /**
     * @var int[]
     */
    protected array $monetaryChars = [
        NumberFormatter::MONETARY_SEPARATOR_SYMBOL,
        NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL
    ];

    /**
     * @function getSymbolType
     * @return int
     */
    public function getSymbolType(): int
    {
        return $this->symbolType;
    }

    /**
     * @function setSymbolType
     * @param int $symbolType
     * @param bool $quoted
     * @throws SetSymbolTypeException
     */
    public function setSymbolType(int $symbolType, bool $quoted = false): void
    {
        if ($symbolType == self::LITERAL || in_array($symbolType, array_keys($this->patternReservedCharsMap))) {
            $this->symbolType = $symbolType;
        } else {
            throw new SetSymbolTypeException($symbolType);
        }
        // quoted flag is ignored if symbolType is LITERAL.  Could throw a warning.....
        if ($symbolType != self::LITERAL) {
            $this->setQuoted($quoted);
        }
    }

    /**
     * @function setSymbolFromChar
     * @param string $char
     * @throws SetSymbolFromCharException
     * @throws SetSymbolTypeException
     */
    public function setSymbolFromChar(string $char) : void
    {
        if (1 != strlen($char)) {
            throw new SetSymbolFromCharException($char);
        }
        if (false === ($type = array_search($char, $this->patternReservedCharsMap))) {
            $this->setSymbolType(self::LITERAL);
            $this->setSymbolValue($char);
        } else {
            /* phpstan does not know that the keys of $patternReservedCharsMap are integers */
            /** @phpstan-ignore-next-line */
            $this->setSymbolType($type);
        }
    }

    /**
     * @function isLiteral
     * @return bool
     */
    public function isLiteral(): bool
    {
        return ($this->symbolType == self::LITERAL);
    }

    /**
     * @function isPositional
     * @return bool
     */
    public function isPositional(): bool
    {
        return in_array($this->symbolType, $this->positionalChars);
    }

    /**
     * @function isDecimal
     * @return bool
     */
    public function isDecimal(): bool
    {
        return in_array($this->symbolType, $this->decimalChars);
    }

    /**
     * @function isGrouping
     * @return bool
     */
    public function isGrouping(): bool
    {
        return in_array($this->symbolType, $this->groupingChars);
    }

    /**
     * @function isMonetary
     * @return bool
     */
    public function isMonetary(): bool
    {
        return in_array($this->symbolType, $this->monetaryChars);
    }

    /**
     * @function setQuoted
     * @param bool $quoted
     */
    public function setQuoted(bool $quoted) : void
    {
        $this->quoted = $quoted;
    }

    /**
     * @function getQuoted
     * @return bool
     */
    public function getQuoted(): bool
    {
        return $this->quoted;
    }

    /**
     * @function getPatternChar
     * @return string
     * @throws UnsetSymbolValueException
     */
    public function getPatternChar(): string
    {
        if ($this->getSymbolType() == self::LITERAL) {
            if (empty($this->getSymbolValue())) {
                throw new UnsetSymbolValueException();
            }
            return $this->getSymbolValue();
        }
        $char = $this->patternReservedCharsMap[$this->getSymbolType()];
        return $this->quoted ? "'" . $char . "'" : $char;
    }

    /**
     * @function getSymbolValue
     * @return string
     */
    public function getSymbolValue(): string
    {
        return $this->symbolValue;
    }

    /**
     * @function setSymbolValue
     * @param string $symbolValue
     */
    public function setSymbolValue(string $symbolValue): void
    {
        $this->symbolValue = $symbolValue;
    }
}
