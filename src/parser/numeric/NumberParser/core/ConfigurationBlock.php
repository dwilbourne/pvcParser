<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\numeric\NumberParser\core;

use Countable;
use Iterator;
use pvc\parser\numeric\NumberParser\core\err\InvalidConfigurationSetException;

/**
 * Class NumberFormatterOptionSetCollection
 */
abstract class ConfigurationBlock implements Iterator, Countable
{
    /**
     * @var array
     */
    protected array $configurationSetArray = [];

    /**
     * @var int
     */
    private int $pos = 0;

    /**
     * @function count
     * @return int
     */
    public function count(): int
    {
        return count($this->configurationSetArray);
    }

    /**
     * @function validateConfigurationSet
     * @param ConfigurationSet $cs
     * @return bool
     */
    abstract public function validateConfigurationSet(ConfigurationSet $cs): bool;

    /**
     * @function addConfigurationSet
     * @param ConfigurationSet $cs
     * @throws InvalidConfigurationSetException
     */
    public function addConfigurationSet(ConfigurationSet $cs) : void
    {
        if (!$this->validateConfigurationSet($cs)) {
            throw new InvalidConfigurationSetException();
        }
        $this->configurationSetArray[] = $cs;
    }

    /**
     * @function getConfigurationSet
     * @return array
     */
    public function getConfigurationSet(): array
    {
        return $this->configurationSetArray;
    }

    /**
     * @inheritDoc
     */
    public function current(): ?ConfigurationSet
    {
        return $this->configurationSetArray[$this->pos];
    }

    /**
     * @inheritDoc
     */
    public function next() : void
    {
        $this->pos++;
    }

    /**
     * @inheritDoc
     */
    public function key() : int
    {
        return $this->pos;
    }

    /**
     * @inheritDoc
     */
    public function valid() : bool
    {
        return isset($this->configurationSetArray[$this->pos]);
    }

    /**
     * @inheritDoc
     */
    public function rewind() : void
    {
        $this->pos = 0;
    }
}
