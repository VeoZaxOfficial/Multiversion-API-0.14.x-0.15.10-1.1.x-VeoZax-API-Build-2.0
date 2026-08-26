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

namespace FG\X509\SAN;
use FG\ASN1\ASNObject;use FG\ASN1\Parsable;use FG\ASN1\Exception\ParserException;
class IPAddress extends ASNObject implements Parsable{
    const IDENTIFIER = 0x87; 
    private $value;
    public function __construct($ipAddressString)
    {
        $this->value = $ipAddressString;
    }
    public function getType()
    {
        return self::IDENTIFIER;
    }
    public function getContent()
    {
        return $this->value;
    }
    protected function calculateContentLength()
    {
        return 4;
    }
    protected function getEncodedValue()
    {
        $ipParts = explode('.', $this->value);
        $binary  = chr($ipParts[0]);
        $binary .= chr($ipParts[1]);
        $binary .= chr($ipParts[2]);
        $binary .= chr($ipParts[3]);
        return $binary;
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], self::IDENTIFIER, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        if ($contentLength != 4) {
            throw new ParserException("A FG\\X509\SAN\IPAddress should have a content length of 4. Extracted length was {$contentLength}", $offsetIndex);
        }
        $ipAddressString = ord($binaryData[$offsetIndex++]).'.';
        $ipAddressString .= ord($binaryData[$offsetIndex++]).'.';
        $ipAddressString .= ord($binaryData[$offsetIndex++]).'.';
        $ipAddressString .= ord($binaryData[$offsetIndex++]);
        $parsedObject = new self($ipAddressString);
        $parsedObject->getObjectLength();
        return $parsedObject;
    }}