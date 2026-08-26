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
use Exception;use FG\ASN1\ASNObject;use FG\ASN1\Parsable;use FG\ASN1\Identifier;
class OctetString extends ASNObject implements Parsable{
    protected $value;
    public function __construct($value)
    {
        if (is_string($value)) {
            $value = preg_replace('/\s|0x/', '', $value);
        } elseif (is_numeric($value)) {
            $value = dechex($value);
        } elseif ($value === null) {
            return;
        } else {
            throw new Exception('OctetString: unrecognized input type!');
        }
        if (strlen($value) % 2 != 0) {
            $value = '0'.$value;
        }
        $this->value = $value;
    }
    public function getType()
    {
        return Identifier::OCTETSTRING;
    }
    protected function calculateContentLength()
    {
        return strlen($this->value) / 2;
    }
    protected function getEncodedValue()
    {
        $value = $this->value;
        $result = '';
        while (strlen($value) >= 2) {
            $result .= chr(hexdec(substr($value, 0, 2)));
            $value = substr($value, 2);
        }
        return $result;
    }
    public function getContent()
    {
        return strtoupper($this->value);
    }
    public function getBinaryContent()
    {
        return $this->getEncodedValue();
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OCTETSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $value = substr($binaryData, $offsetIndex, $contentLength);
        $offsetIndex += $contentLength;
        $parsedObject = new self(bin2hex($value));
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}