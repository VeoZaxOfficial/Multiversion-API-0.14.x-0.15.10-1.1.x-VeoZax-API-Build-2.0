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
use Exception;use FG\ASN1\Base128;use FG\ASN1\OID;use FG\ASN1\ASNObject;use FG\ASN1\Parsable;use FG\ASN1\Identifier;use FG\ASN1\Exception\ParserException;
class ObjectIdentifier extends ASNObject implements Parsable{
    protected $subIdentifiers;
    protected $value;
    public function __construct($value)
    {
        $this->subIdentifiers = explode('.', $value);
        $nrOfSubIdentifiers = count($this->subIdentifiers);
        for ($i = 0; $i < $nrOfSubIdentifiers; $i++) {
            if (is_numeric($this->subIdentifiers[$i])) {
                $this->subIdentifiers[$i] = intval($this->subIdentifiers[$i]);
            } else {
                throw new Exception("[{$value}] is no valid object identifier (sub identifier ".($i + 1).' is not numeric)!');
            }
        }
        if ($nrOfSubIdentifiers >= 2) {
            $this->subIdentifiers[1] = ($this->subIdentifiers[0] * 40) + $this->subIdentifiers[1];
            unset($this->subIdentifiers[0]);
        }
        $this->value = $value;
    }
    public function getContent()
    {
        return $this->value;
    }
    public function getType()
    {
        return Identifier::OBJECT_IDENTIFIER;
    }
    protected function calculateContentLength()
    {
        $length = 0;
        foreach ($this->subIdentifiers as $subIdentifier) {
            do {
                $subIdentifier = $subIdentifier >> 7;
                $length++;
            } while ($subIdentifier > 0);
        }
        return $length;
    }
    protected function getEncodedValue()
    {
        $encodedValue = '';
        foreach ($this->subIdentifiers as $subIdentifier) {
            $encodedValue .= Base128::encode($subIdentifier);
        }
        return $encodedValue;
    }
    public function __toString()
    {
        return OID::getName($this->value);
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OBJECT_IDENTIFIER, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);
        $firstOctet = ord($binaryData[$offsetIndex++]);
        $oidString = floor($firstOctet / 40).'.'.($firstOctet % 40);
        $oidString .= '.'.self::parseOid($binaryData, $offsetIndex, $contentLength - 1);
        $parsedObject = new self($oidString);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
    protected static function parseOid(&$binaryData, &$offsetIndex, $octetsToRead)
    {
        $oid = '';
        while ($octetsToRead > 0) {
            $octets = '';
            do {
                if (0 === $octetsToRead) {
                    throw new ParserException('Malformed ASN.1 Object Identifier', $offsetIndex - 1);
                }
                $octetsToRead--;
                $octet = $binaryData[$offsetIndex++];
                $octets .= $octet;
            } while (ord($octet) & 0x80);
            $oid .= sprintf('%d.', Base128::decode($octets));
        }
        return substr($oid, 0, -1) ?: '';
    }}