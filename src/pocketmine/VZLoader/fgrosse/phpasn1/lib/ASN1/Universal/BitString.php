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

namespace FG\ASN1\Universal;
use Exception;use FG\ASN1\Exception\ParserException;use FG\ASN1\Parsable;use FG\ASN1\Identifier;
class BitString extends OctetString implements Parsable{
    private $nrOfUnusedBits;
    public function __construct($value, $nrOfUnusedBits = 0)
    {
        parent::__construct($value);
        if (!is_numeric($nrOfUnusedBits) || $nrOfUnusedBits < 0) {
            throw new Exception('BitString: second parameter needs to be a positive number (or zero)!');
        }
        $this->nrOfUnusedBits = $nrOfUnusedBits;
    }
    public function getType()
    {
        return Identifier::BITSTRING;
    }
    protected function calculateContentLength()
    {
        return parent::calculateContentLength() + 1;
    }
    protected function getEncodedValue()
    {
        $nrOfUnusedBitsOctet = chr($this->nrOfUnusedBits);
        $actualContent = parent::getEncodedValue();
        return $nrOfUnusedBitsOctet.$actualContent;
    }
    public function getNumberOfUnusedBits()
    {
        return $this->nrOfUnusedBits;
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::BITSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 2);
        $nrOfUnusedBits = ord($binaryData[$offsetIndex]);
        $value = substr($binaryData, $offsetIndex + 1, $contentLength - 1);
        if ($nrOfUnusedBits > 7 || 
            ($contentLength - 1) == 1 && $nrOfUnusedBits > 0 || 
            (ord($value[strlen($value)-1])&((1<<$nrOfUnusedBits)-1)) != 0 
        ) {
            throw new ParserException("Can not parse bit string with invalid padding", $offsetIndex);
        }
        $offsetIndex += $contentLength;
        $parsedObject = new self(bin2hex($value), $nrOfUnusedBits);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}