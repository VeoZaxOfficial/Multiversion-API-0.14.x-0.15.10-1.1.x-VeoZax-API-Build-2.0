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
use FG\ASN1\AbstractTime;use FG\ASN1\Parsable;use FG\ASN1\Identifier;use FG\ASN1\Exception\ParserException;
class GeneralizedTime extends AbstractTime implements Parsable{
    private $microseconds;
    public function __construct($dateTime = null, $dateTimeZone = 'UTC')
    {
        parent::__construct($dateTime, $dateTimeZone);
        $this->microseconds = $this->value->format('u');
        if ($this->containsFractionalSecondsElement()) {
            $this->microseconds = preg_replace('/([1-9]+)0+$/', '$1', $this->microseconds);
        }
    }
    public function getType()
    {
        return Identifier::GENERALIZED_TIME;
    }
    protected function calculateContentLength()
    {
        $contentSize = 15; 
        if ($this->containsFractionalSecondsElement()) {
            $contentSize += 1 + strlen($this->microseconds);
        }
        return $contentSize;
    }
    public function containsFractionalSecondsElement()
    {
        return intval($this->microseconds) > 0;
    }
    protected function getEncodedValue()
    {
        $encodedContent = $this->value->format('YmdHis');
        if ($this->containsFractionalSecondsElement()) {
            $encodedContent .= ".{$this->microseconds}";
        }
        return $encodedContent.'Z';
    }
    public function __toString()
    {
        if ($this->containsFractionalSecondsElement()) {
            return $this->value->format("Y-m-d\tH:i:s.uP");
        } else {
            return $this->value->format("Y-m-d\tH:i:sP");
        }
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::GENERALIZED_TIME, $offsetIndex++);
        $lengthOfMinimumTimeString = 14; 
        $contentLength = self::parseContentLength($binaryData, $offsetIndex, $lengthOfMinimumTimeString);
        $maximumBytesToRead = $contentLength;
        $format = 'YmdGis';
        $content = substr($binaryData, $offsetIndex, $contentLength);
        $dateTimeString = substr($content, 0, $lengthOfMinimumTimeString);
        $offsetIndex += $lengthOfMinimumTimeString;
        $maximumBytesToRead -= $lengthOfMinimumTimeString;
        if ($contentLength == $lengthOfMinimumTimeString) {
            $localTimeZone = new \DateTimeZone(date_default_timezone_get());
            $dateTime = \DateTime::createFromFormat($format, $dateTimeString, $localTimeZone);
        } else {
            if ($binaryData[$offsetIndex] == '.') {
                $maximumBytesToRead--; 
                $nrOfFractionalSecondElements = 1; 
                while ($maximumBytesToRead > 0
                      && $binaryData[$offsetIndex + $nrOfFractionalSecondElements] != '+'
                      && $binaryData[$offsetIndex + $nrOfFractionalSecondElements] != '-'
                      && $binaryData[$offsetIndex + $nrOfFractionalSecondElements] != 'Z') {
                    $nrOfFractionalSecondElements++;
                    $maximumBytesToRead--;
                }
                $dateTimeString .= substr($binaryData, $offsetIndex, $nrOfFractionalSecondElements);
                $offsetIndex += $nrOfFractionalSecondElements;
                $format .= '.u';
            }
            $dateTime = \DateTime::createFromFormat($format, $dateTimeString, new \DateTimeZone('UTC'));
            if ($maximumBytesToRead > 0) {
                if ($binaryData[$offsetIndex] == '+'
                || $binaryData[$offsetIndex] == '-') {
                    $dateTime = static::extractTimeZoneData($binaryData, $offsetIndex, $dateTime);
                } elseif ($binaryData[$offsetIndex++] != 'Z') {
                    throw new ParserException('Invalid ISO 8601 Time String', $offsetIndex);
                }
            }
        }
        $parsedObject = new self($dateTime);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}