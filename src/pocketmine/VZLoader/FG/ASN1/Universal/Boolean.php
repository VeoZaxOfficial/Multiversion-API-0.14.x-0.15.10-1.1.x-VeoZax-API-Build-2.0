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
use FG\ASN1\ASNObject;use FG\ASN1\Parsable;use FG\ASN1\Identifier;use FG\ASN1\Exception\ParserException;
class Boolean extends ASNObject implements Parsable{
    private $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
    public function getType()
    {
        return Identifier::BOOLEAN;
    }
    protected function calculateContentLength()
    {
        return 1;
    }
    protected function getEncodedValue()
    {
        if ($this->value == false) {
            return chr(0x00);
        } else {
            return chr(0xFF);
        }
    }
    public function getContent()
    {
        if ($this->value == true) {
            return 'TRUE';
        } else {
            return 'FALSE';
        }
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::BOOLEAN, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        if ($contentLength != 1) {
            throw new ParserException("An ASN.1 Boolean should not have a length other than one. Extracted length was {$contentLength}", $offsetIndex);
        }
        $value = ord($binaryData[$offsetIndex++]);
        $booleanValue = $value == 0xFF ? true : false;
        $parsedObject = new self($booleanValue);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}