<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\parser\range_collection;

use pvc\msg\Msg;
use pvc\msg\MsgRetrievalInterface;
use pvc\parser\ParserInterface;
use pvc\struct\range_collection\range_collection_factory\RangeCollectionFactoryInterface;
use pvc\struct\range_collection\RangeCollectionInterface;

/**
 * Class ParserRangeCollection
 * @package pvc\parser\range
 */
abstract class ParserRangeCollection implements ParserInterface
{
    protected RangeCollectionFactoryInterface $rangeCollectionFactory;
    protected RangeCollectionInterface $parsedValue;
    protected ParserRangeElement $parserRangeElement;
    protected Msg $errMsg;

    public function getParserRangeElement(): ParserRangeElement
    {
        return $this->parserRangeElement;
    }

    public function setParserRangeElement(ParserRangeElement $parserRangeElement): void
    {
        $this->parserRangeElement = $parserRangeElement;
    }

    public function parse(string $rangeSpec) : bool
    {
        $rangeCollection = $this->rangeCollectionFactory->createRangeCollection();
        $rangeShell = explode(',', $rangeSpec);
        foreach ($rangeShell as $rangeElement) {
            if (!$this->parserRangeElement->parse($rangeElement)) {
                $msg = $this->parserRangeElement->getErrmsg();
                $this->errMsg = new Msg($msg->getMsgVars(), $msg->getMsgText());
                return false;
            }
            $rangeCollection->addRangeElement($this->parserRangeElement->getParsedValue());
        }
        $this->parsedValue = $rangeCollection;
        return true;
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
