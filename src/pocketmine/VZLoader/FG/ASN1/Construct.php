<?php

/* 
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *  This API has now modified by VeoZax under GNU Lesser General Public License.
 *  Feel free to use it + if you are willing to modify or Enhance this API,
 *  Make sure to publish your changes to the GitHub open sourced.
 *  Do Not Own This API Privately Since this API is made to use Freely for Every
 *  Legacy users from 0.14.x - 0.15.10 - 1.1.x
 *   
 *               ╦  ╦┌─┐┌─┐╔═╗┌─┐─┐ ┬  ╔═╗┌─┐┬
 *               ╚╗╔╝├┤ │ │╔═╝├─┤┌┴┬┘  ╠═╣├─┘│
 *                ╚╝ └─┘└─┘╚═╝┴ ┴┴ └─  ╩ ╩┴  ┴
 *  
 *  	         » Multi-Version API by VeoZax 
 *             » Accepted MCPE Versions: 0.14x - 0.15.10 - 1.1.x
 *  			     » YouTube: @VeoZax
 *            » Discord: https://discord.gg/dCzgPYam2J
 *               » Website: https://info.veozax.xyz
 */

namespace FG\ASN1;
use ArrayAccess;use ArrayIterator;use Countable;use FG\ASN1\Exception\ParserException;use Iterator;use ReturnTypeWillChange;
abstract class Construct extends ASNObject implements Countable, ArrayAccess, Iterator, Parsable{
    protected $children;
    private $iteratorPosition;
    public function __construct( ...$children)
    {
        $this->children = $children;
        $this->iteratorPosition = 0;
    }
    public function getContent()
    {
        return $this->children;
    }
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->iteratorPosition = 0;
    }
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->children[$this->iteratorPosition];
    }
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->iteratorPosition;
    }
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->iteratorPosition++;
    }
    #[ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->children[$this->iteratorPosition]);
    }
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->children);
    }
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->children[$offset];
    }
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $offset = count($this->children);
        }
        $this->children[$offset] = $value;
    }
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->children[$offset]);
    }
    protected function calculateContentLength()
    {
        $length = 0;
        foreach ($this->children as $component) {
            $length += $component->getObjectLength();
        }
        return $length;
    }
    protected function getEncodedValue()
    {
        $result = '';
        foreach ($this->children as $component) {
            $result .= $component->getBinary();
        }
        return $result;
    }
    public function addChild(ASNObject $child)
    {
        $this->children[] = $child;
    }
    public function addChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
    }
    public function __toString()
    {
        $nrOfChildren = $this->getNumberOfChildren();
        $childString = $nrOfChildren == 1 ? 'child' : 'children';
        return "[{$nrOfChildren} {$childString}]";
    }
    public function getNumberOfChildren()
    {
        return count($this->children);
    }
    public function getChildren()
    {
        return $this->children;
    }
    public function getFirstChild()
    {
        return $this->children[0];
    }
    #[ReturnTypeWillChange]
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static();
        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $startIndex = $offsetIndex;
        $children = [];
        $octetsToRead = $contentLength;
        while ($octetsToRead > 0) {
            $newChild = ASNObject::fromBinary($binaryData, $offsetIndex);
            $octetsToRead -= $newChild->getObjectLength();
            $children[] = $newChild;
        }
        if ($octetsToRead !== 0) {
            throw new ParserException("Sequence length incorrect", $startIndex);
        }
        $parsedObject->addChildren($children);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
    #[ReturnTypeWillChange]
    public function count($mode = COUNT_NORMAL)
    {
        return count($this->children, $mode);
    }
    public function getIterator()
    {
        return new ArrayIterator($this->children);
    }}