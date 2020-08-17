<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\range_collection;

use pvc\msg\Msg;
use pvc\msg\MsgRetrievalInterface;
use pvc\parser\ParserInterface;
use pvc\parser\range_collection\err\InvalidRangeElementSpecificationMsg;
use pvc\struct\range_collection\range_element\RangeElementInterface;
use pvc\struct\range_collection\range_element_factory\RangeElementFactoryInterface;

/**
 * Class ParserRangeElement
 * @package pvc\parser\range_collection
 */
class ParserRangeElement implements Parserinterface
{
    protected Msg $errMsg;
    protected RangeElementFactoryInterface $rangeElementFactory;
    protected RangeElementInterface $parsedValue;

    /**
     * @var string
     */
    protected string $patternDescription;

    /**
     * @var ParserInterface
     *
     * if "1-5" is a range, then 1 and 5 are the range atoms.
     */
    protected ParserInterface $rangeAtomParser;

    /**
     * @return ParserInterface
     */
    public function getRangeAtomParser(): ParserInterface
    {
        return $this->rangeAtomParser;
    }

    /**
     * @param ParserInterface $rangeAtomParser
     */
    public function setRangeAtomParser(ParserInterface $rangeAtomParser): void
    {
        $this->rangeAtomParser = $rangeAtomParser;
    }

    /**
     * @return RangeElementFactoryInterface
     */
    public function getRangeElementFactory(): RangeElementFactoryInterface
    {
        return $this->rangeElementFactory;
    }

    /**
     * @param RangeElementFactoryInterface $rangeElementFactory
     */
    public function setRangeElementFactory(RangeElementFactoryInterface $rangeElementFactory): void
    {
        $this->rangeElementFactory = $rangeElementFactory;
    }

    /**
     * @function getPatternDescription
     * @return string
     */
    public function getPatternDescription(): string
    {
        return $this->patternDescription;
    }

    /**
     * @function setPatternDescription
     * @param string $description
     */
    public function setPatternDescription(string $description): void
    {
        $this->patternDescription = $description;
    }

    public function parse(string $data): bool
    {
        if ($this->rangeAtomParser->parse($data)) {
            $min = $this->rangeAtomParser->getParsedValue();
            $max = $this->rangeAtomParser->getParsedValue();
            $this->parsedValue = $this->rangeElementFactory->createRangeElement($min, $max);
            return true;
        }

        if (preg_match('/^(.+)-(.+)$/', $data, $matches)) {
            if (false === $this->rangeAtomParser->parse($matches[1])) {
                return $this->parseFailed($data);
            }
            $min = $this->rangeAtomParser->getParsedValue();

            if (false === $this->rangeAtomParser->parse($matches[2])) {
                return $this->parseFailed($data);
            }
            $max = $this->rangeAtomParser->getParsedValue();

            $this->parsedValue = $this->rangeElementFactory->createRangeElement($min, $max);
            return true;
        }
        return $this->parseFailed($data);
    }

    protected function parseFailed($data) : bool
    {
        $this->errMsg = new InvalidRangeElementSpecificationMsg($this->patternDescription, $data);
        return false;
    }

    public function getParsedValue()
    {
        return $this->parsedValue;
    }

    public function getErrmsg(): ?MsgRetrievalInterface
    {
        return $this->errMsg ?? null;
    }
}
