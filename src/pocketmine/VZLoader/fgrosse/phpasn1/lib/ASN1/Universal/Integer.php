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
use Exception;use FG\Utility\BigInteger;use FG\ASN1\Exception\ParserException;use FG\ASN1\ASNObject;use FG\ASN1\Parsable;use FG\ASN1\Identifier;
class Integer extends ASNObject implements Parsable{
    private $value;
    public function __construct($value)
    {
        if (is_numeric($value) == false) {
            throw new Exception("Invalid VALUE [{$value}] for ASN1_INTEGER");
        }
        $this->value = $value;
    }
    public function getType()
    {
        return Identifier::INTEGER;
    }
    public function getContent()
    {
        return $this->value;
    }
    protected function calculateContentLength()
    {
        return strlen($this->getEncodedValue());
    }
    protected function getEncodedValue()
    {
        $value = BigInteger::create($this->value, 10);
        $negative = $value->compare(0) < 0;
        if ($negative) {
             $value = $value->absoluteValue();
             $limit = 0x80;
        } else {
             $limit = 0x7f;
        }
        $mod = 0xff+1;
        $values = [];
        while($value->compare($limit) > 0) {
            $values[] = $value->modulus($mod)->toInteger();
            $value = $value->shiftRight(8);
        }
        $values[] = $value->modulus($mod)->toInteger();
        $numValues = count($values);
        if ($negative) {
            for ($i = 0; $i < $numValues; $i++) {
                $values[$i] = 0xff - $values[$i];
            }
            for ($i = 0; $i < $numValues; $i++) {
                $values[$i] += 1;
                if ($values[$i] <= 0xff) {
                    break;
                }
                assert($i != $numValues - 1);
                $values[$i] = 0;
            }
            if ($values[$numValues - 1] == 0x7f) {
                $values[] = 0xff;
            }
        }
        $values = array_reverse($values);
        $r = pack("C*", ...$values);
        return $r;
    }
    private static function ensureMinimalEncoding($binaryData, $offsetIndex)
    {
        if ((ord($binaryData[$offsetIndex]) == 0x00 && (ord($binaryData[$offsetIndex+1]) & 0x80) == 0) ||
            (ord($binaryData[$offsetIndex]) == 0xff && (ord($binaryData[$offsetIndex+1]) & 0x80) == 0x80)) {
            throw new ParserException("Integer not minimally encoded", $offsetIndex);
        }
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static(0);
        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 1);
        if ($contentLength > 1) {
            self::ensureMinimalEncoding($binaryData, $offsetIndex);
        }
        $isNegative = (ord($binaryData[$offsetIndex]) & 0x80) != 0x00;
        $number = BigInteger::create(ord($binaryData[$offsetIndex++]) & 0x7F);
        for ($i = 0; $i < $contentLength - 1; $i++) {
            $number = $number->multiply(0x100)->add(ord($binaryData[$offsetIndex++]));
        }
        if ($isNegative) {
            $number = $number->subtract(BigInteger::create(2)->toPower(8 * $contentLength - 1));
        }
        $parsedObject = new static((string)$number);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}