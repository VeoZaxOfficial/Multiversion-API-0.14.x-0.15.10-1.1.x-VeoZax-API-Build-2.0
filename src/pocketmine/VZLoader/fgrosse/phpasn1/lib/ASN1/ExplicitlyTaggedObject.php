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
use FG\ASN1\Exception\ParserException;
class ExplicitlyTaggedObject extends ASNObject{
    private $decoratedObjects;
    private $tag;
    public function __construct($tag,  ...$objects)
    {
        $this->tag = $tag;
        $this->decoratedObjects = $objects;
    }
    protected function calculateContentLength()
    {
        $length = 0;
        foreach ($this->decoratedObjects as $object) {
            $length += $object->getObjectLength();
        }
        return $length;
    }
    protected function getEncodedValue()
    {
        $encoded = '';
        foreach ($this->decoratedObjects as $object) {
            $encoded .= $object->getBinary();
        }
        return $encoded;
    }
    public function getContent()
    {
        return $this->decoratedObjects;
    }
    public function __toString()
    {
        switch ($length = count($this->decoratedObjects)) {
        case 0:
            return "Context specific empty object with tag [{$this->tag}]";
        case 1:
            $decoratedType = Identifier::getShortName($this->decoratedObjects[0]->getType());
            return "Context specific $decoratedType with tag [{$this->tag}]";
        default:
            return "$length context specific objects with tag [{$this->tag}]";
        }
    }
    public function getType()
    {
        return ord($this->getIdentifier());
    }
    public function getIdentifier()
    {
        $identifier = Identifier::create(Identifier::CLASS_CONTEXT_SPECIFIC, true, $this->tag);
        return is_int($identifier) ? chr($identifier) : $identifier;
    }
    public function getTag()
    {
        return $this->tag;
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $identifier = self::parseBinaryIdentifier($binaryData, $offsetIndex);
        $firstIdentifierOctet = ord($identifier);
        assert(Identifier::isContextSpecificClass($firstIdentifierOctet), 'identifier octet should indicate context specific class');
        assert(Identifier::isConstructed($firstIdentifierOctet), 'identifier octet should indicate constructed object');
        $tag = Identifier::getTagNumber($identifier);
        $totalContentLength = self::parseContentLength($binaryData, $offsetIndex);
        $remainingContentLength = $totalContentLength;
        $offsetIndexOfDecoratedObject = $offsetIndex;
        $decoratedObjects = [];
        while ($remainingContentLength > 0) {
            $nextObject = ASNObject::fromBinary($binaryData, $offsetIndex);
            $remainingContentLength -= $nextObject->getObjectLength();
            $decoratedObjects[] = $nextObject;
        }
        if ($remainingContentLength != 0) {
            throw new ParserException("Context-Specific explicitly tagged object [$tag] starting at offset $offsetIndexOfDecoratedObject specifies a length of $totalContentLength octets but $remainingContentLength remain after parsing the content", $offsetIndexOfDecoratedObject);
        }
        $parsedObject = new self($tag, ...$decoratedObjects);
        $parsedObject->setContentLength($totalContentLength);
        return $parsedObject;
    }}