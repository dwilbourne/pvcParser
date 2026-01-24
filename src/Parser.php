<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser;

use pvc\interfaces\msg\DomainCatalogLoaderInterface;
use pvc\interfaces\msg\MsgInterface;
use pvc\interfaces\parser\ParserInterface;
use pvc\msg\DomainCatalogFileLoaderPhp;
use pvc\msg\err\NonExistentDomainCatalogDirectoryException;

/**
 * Parser creates a few default implementation methods for child classes that implement ParserInterface.
 *
 * Class Parser
 * @template DataType
 * @phpstan-import-type MsgContent from MsgInterface
 */
abstract class Parser implements ParserInterface
{
    /**
     * @var MsgInterface|null
     */
    protected ?MsgInterface $msg;

    /**
     * @var DomainCatalogLoaderInterface
     */
    protected DomainCatalogLoaderInterface $catalogLoader;

    /**
     * @var DataType
     */
    protected $parsedValue;


    /**
     * @param  MsgInterface|null  $msg
     * @param  DomainCatalogLoaderInterface|null  $catalogLoader
     *
     * @throws NonExistentDomainCatalogDirectoryException
     *
     * $msg can be null and if it is then getMsg returns null.
     * $catalogLoader can be null and if it is, the default is the domain
     * catalog found in this library
     */
    public function __construct(
        ?MsgInterface $msg = null,
        ?DomainCatalogLoaderInterface $catalogLoader = null,
    ) {
        $this->msg = $msg;
        if ($catalogLoader) {
            $this->catalogLoader = $catalogLoader;
        } else {
            $this->catalogLoader = new DomainCatalogFileLoaderPhp();
            $this->catalogLoader->setDomainCatalogDirectory(
                __DIR__.'/messages'
            );
        }
    }

    /**
     * getMsg
     *
     * @return MsgInterface
     */
    public function getMsg(): ?MsgInterface
    {
        return $this->msg;
    }

    public function getDomainCatalogLoader(): DomainCatalogLoaderInterface
    {
        return $this->catalogLoader;
    }

    /**
     * @function getParsedValue
     * @return DataType|null
     * see the comment in the parse method below about parsing empty strings as to why we coalesce to null in this
     * getter.
     */
    public function getParsedValue()
    {
        return ($this->parsedValue ?? null);
    }

    /**
     * parse
     *
     * @param  string  $data
     *
     * @return bool
     */
    public function parse(string $data): bool
    {
        /**
         * clear the message content and the parsedValue attribute so that subsequent iterations of this same parser
         * does not leave a message or a parsed value leftover from a prior iteration.
         */
        $this->getMsg()?->clearContent();
        unset($this->parsedValue);

        /**
         * Usually, parsing a string into a specific data type occurs before the data is fed as a parameter into the
         * model.  Only the model knows whether an empty string (null value) is an acceptable value or not.  That's why
         * the Validator classes all have an 'isRequired' flag.  So the behavior we want is that if the $data argument
         * here is an empty string, then we simply return null.  If we didn't do that, the parser would fail on an
         * empty string and produce a message to the effect that $data could not be parsed into a valid data type.
         */
        if ($data === '') {
            return true;
        }

        /**
         * parse the value and set an appropriate message if the value cannot be parsed
         */
        if (!$this->parseValue($data)) {
            $this->getMsg()?->setContent(
                $this->getMsgDomain(),
                $this->getMsgId(),
                $this->getMsgParameters()
            );
            return false;
        }

        return true;
    }

    public function getMsgDomain(): string
    {
        return 'Parser';
    }

    abstract protected function parseValue(string $data): bool;

    abstract protected function getMsgId(): string;

    /**
     * getMsgParameters
     *
     * @return array<mixed>
     */
    abstract protected function getMsgParameters(): array;

}
