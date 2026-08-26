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
use DateTime;use DateTimeZone;use FG\ASN1\AbstractTime;use FG\ASN1\Parsable;use FG\ASN1\Identifier;use FG\ASN1\Exception\ParserException;
class UTCTime extends AbstractTime implements Parsable{
	public function getType()
    {
        return Identifier::UTC_TIME;
    }
    protected function calculateContentLength()
    {
        return 13; 
    }
    protected function getEncodedValue()
    {
        return $this->value->format('ymdHis').'Z';
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::UTC_TIME, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, 11);
        $format = 'ymdGi';
        $dateTimeString = substr($binaryData, $offsetIndex, 10);
        $offsetIndex += 10;
        if ($binaryData[$offsetIndex] != 'Z'
        && $binaryData[$offsetIndex] != '+'
        && $binaryData[$offsetIndex] != '-') {
            $dateTimeString .= substr($binaryData, $offsetIndex, 2);
            $offsetIndex += 2;
            $format .= 's';
        }
        $dateTime = DateTime::createFromFormat($format, $dateTimeString, new DateTimeZone('UTC'));
        if ($binaryData[$offsetIndex] == '+'
        || $binaryData[$offsetIndex] == '-') {
            $dateTime = static::extractTimeZoneData($binaryData, $offsetIndex, $dateTime);
        } elseif ($binaryData[$offsetIndex++] != 'Z') {
            throw new ParserException('Invalid UTC String', $offsetIndex);
        }
        $parsedObject = new self($dateTime);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}