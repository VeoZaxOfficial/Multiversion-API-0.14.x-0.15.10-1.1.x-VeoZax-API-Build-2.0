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
use Exception;
abstract class AbstractString extends ASNObject implements Parsable{
    protected $value;
    private $checkStringForIllegalChars = true;
    private $allowedCharacters = [];
    public function __construct($string)
    {
        $this->value = $string;
    }
    public function getContent()
    {
        return $this->value;
    }
    protected function allowCharacter($character)
    {
        $this->allowedCharacters[] = $character;
    }
    protected function allowCharacters(...$characters)
    {
        foreach ($characters as $character) {
            $this->allowedCharacters[] = $character;
        }
    }
    protected function allowNumbers()
    {
        foreach (range('0', '9') as $char) {
            $this->allowedCharacters[] = (string) $char;
        }
    }
    protected function allowAllLetters()
    {
        $this->allowSmallLetters();
        $this->allowCapitalLetters();
    }
    protected function allowSmallLetters()
    {
        foreach (range('a', 'z') as $char) {
            $this->allowedCharacters[] = $char;
        }
    }
    protected function allowCapitalLetters()
    {
        foreach (range('A', 'Z') as $char) {
            $this->allowedCharacters[] = $char;
        }
    }
    protected function allowSpaces()
    {
        $this->allowedCharacters[] = ' ';
    }
    protected function allowAll()
    {
        $this->checkStringForIllegalChars = false;
    }
    protected function calculateContentLength()
    {
        return strlen($this->value);
    }
    protected function getEncodedValue()
    {
        if ($this->checkStringForIllegalChars) {
            $this->checkString();
        }
        return $this->value;
    }
    protected function checkString()
    {
        $stringLength = $this->getContentLength();
        for ($i = 0; $i < $stringLength; $i++) {
            if (in_array($this->value[$i], $this->allowedCharacters) == false) {
                $typeName = Identifier::getName($this->getType());
                throw new Exception("Could not create a {$typeName} from the character sequence '{$this->value}'.");
            }
        }
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        $parsedObject = new static('');
        self::parseIdentifier($binaryData[$offsetIndex], $parsedObject->getType(), $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $string = substr($binaryData, $offsetIndex, $contentLength);
        $offsetIndex += $contentLength;
        $parsedObject->value = $string;
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }
    public static function isValid($string)
    {
        $testObject = new static($string);
        try {
            $testObject->checkString();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }}