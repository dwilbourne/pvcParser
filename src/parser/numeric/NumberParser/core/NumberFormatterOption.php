<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core;

use NumberFormatter;
use pvc\parser\numeric\NumberParser\core\err\OptionConfigurationException;
use Throwable;

/**
 * Class ConfigOption
 *
 * TODO: clean up the typing by separating setAttribute from setTextAttribute
 */
class NumberFormatterOption
{
    /**
     * @var string
     */
    protected string $method;

    /**
     * @var int|string
     */
    protected $attribute;

    /**
     * @var int|string|bool|null
     */
    protected $value;


    /**
     * NumberFormatterOption constructor.
     * @param string $method
     * @param int|string $attribute
     * @param int|string|bool|null $value
     */
    public function __construct(string $method, $attribute, $value = null)
    {
        $this->setMethod($method);
        $this->setAttribute($attribute);
        $this->setValue($value);
    }


    /**
     * @function getMethod
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @function setMethod
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @function getAttribute
     * @return int|string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @function setAttribute
     * @param int|string $attribute
     */
    public function setAttribute($attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @function getValue
     * @return int|string|bool|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @function setValue
     * @param int|string|bool|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @function configure
     * @param NumberFormatter $frmtr
     * @return bool
     * @throws OptionConfigurationException
     */
    public function configure(NumberFormatter $frmtr): bool
    {
        try {
            $method = $this->getMethod();
            $attribute = $this->getAttribute();
            $value = $this->getValue();
            return (is_null($this->getValue()) ? $frmtr->$method($attribute) : $frmtr->$method($attribute, $value));
        } catch (Throwable $e) {
            throw new OptionConfigurationException($this, $e);
        }
    }
}
