<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\parser;

use pvc\interfaces\msg\MsgInterface;
use pvc\interfaces\parser\ParserInterface;

/**
 * Parser creates a few default implementation methods for child classes that implement ParserInterface.
 *
 * Class Parser
 * @template DataType
 */
abstract class Parser implements ParserInterface
{
    /**
     * @var MsgInterface
     */
    protected MsgInterface $msg;

    /**
     * @var DataType
     */
    protected $parsedValue;


    public function __construct(MsgInterface $msg)
    {
        $this->setMsg($msg);
    }

    /**
     * getMsg
     * @return MsgInterface
     */
    public function getMsg(): MsgInterface
    {
        return $this->msg;
    }

    /**
     * setMsg
     * @param MsgInterface $msg
     */
    public function setMsg(MsgInterface $msg): void
    {
        $this->msg = $msg;
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
     * @param string $data
     * @return bool
     */
    public function parse(string $data): bool
    {
        /**
         * clear the message content and the parsedValue attribute so that subsequent iterations of this same parser
         * does not leave a message or a parsed value leftover from a prior iteration.
         */
        $this->getMsg()->clearContent();
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
            $this->setMsgContent($this->getMsg());
            return false;
        }

        return true;
    }

    public function getMsgDomain(): string
    {
        return 'Parser';
    }

    abstract protected function parseValue(string $data): bool;

    abstract protected function setMsgContent(MsgInterface $msg): void;
}
