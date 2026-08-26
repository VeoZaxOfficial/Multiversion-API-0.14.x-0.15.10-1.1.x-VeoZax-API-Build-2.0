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
use FG\ASN1\Exception\ParserException;use FG\ASN1\Universal\BitString;use FG\ASN1\Universal\Boolean;use FG\ASN1\Universal\Enumerated;use FG\ASN1\Universal\GeneralizedTime;use FG\ASN1\Universal\Integer;use FG\ASN1\Universal\NullObject;use FG\ASN1\Universal\ObjectIdentifier;use FG\ASN1\Universal\RelativeObjectIdentifier;use FG\ASN1\Universal\OctetString;use FG\ASN1\Universal\Sequence;use FG\ASN1\Universal\Set;use FG\ASN1\Universal\UTCTime;use FG\ASN1\Universal\IA5String;use FG\ASN1\Universal\PrintableString;use FG\ASN1\Universal\NumericString;use FG\ASN1\Universal\UTF8String;use FG\ASN1\Universal\UniversalString;use FG\ASN1\Universal\CharacterString;use FG\ASN1\Universal\GeneralString;use FG\ASN1\Universal\VisibleString;use FG\ASN1\Universal\GraphicString;use FG\ASN1\Universal\BMPString;use FG\ASN1\Universal\T61String;use FG\ASN1\Universal\ObjectDescriptor;use FG\Utility\BigInteger;use LogicException;
abstract class ASNObject implements Parsable{
    private $contentLength;
    private $nrOfLengthOctets;
    abstract protected function calculateContentLength();
    abstract protected function getEncodedValue();
    abstract public function getContent();
    abstract public function getType();
    public function getIdentifier()
    {
        $firstOctet = $this->getType();
        if (Identifier::isLongForm($firstOctet)) {
            throw new LogicException(sprintf('Identifier of %s uses the long form and must therefor override "ASNObject::getIdentifier()".', get_class($this)));
        }
        return chr($firstOctet);
    }
    public function getBinary()
    {
        $result  = $this->getIdentifier();
        $result .= $this->createLengthPart();
        $result .= $this->getEncodedValue();
        return $result;
    }
    private function createLengthPart()
    {
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);
        if ($nrOfLengthOctets == 1) {
            return chr($contentLength);
        } else {
            $lengthOctets = chr(0x80 | ($nrOfLengthOctets - 1));
            for ($shiftLength = 8 * ($nrOfLengthOctets - 2); $shiftLength >= 0; $shiftLength -= 8) {
                $lengthOctets .= chr($contentLength >> $shiftLength);
            }
            return $lengthOctets;
        }
    }
    protected function getNumberOfLengthOctets($contentLength = null)
    {
        if (!isset($this->nrOfLengthOctets)) {
            if ($contentLength == null) {
                $contentLength = $this->getContentLength();
            }
            $this->nrOfLengthOctets = 1;
            if ($contentLength > 127) {
                do { 
                    $this->nrOfLengthOctets++;
                    $contentLength = $contentLength >> 8;
                } while ($contentLength > 0);
            }
        }
        return $this->nrOfLengthOctets;
    }
    protected function getContentLength()
    {
        if (!isset($this->contentLength)) {
            $this->contentLength = $this->calculateContentLength();
        }
        return $this->contentLength;
    }
    protected function setContentLength($newContentLength)
    {
        $this->contentLength = $newContentLength;
        $this->getNumberOfLengthOctets($newContentLength);
    }
    public function getObjectLength()
    {
        $nrOfIdentifierOctets = strlen($this->getIdentifier());
        $contentLength = $this->getContentLength();
        $nrOfLengthOctets = $this->getNumberOfLengthOctets($contentLength);
        return $nrOfIdentifierOctets + $nrOfLengthOctets + $contentLength;
    }
    public function __toString()
    {
        return $this->getContent();
    }
    public function getTypeName()
    {
        return Identifier::getName($this->getType());
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        if (strlen($binaryData) <= $offsetIndex) {
            throw new ParserException('Can not parse binary from data: Offset index larger than input size', $offsetIndex);
        }
        $identifierOctet = ord($binaryData[$offsetIndex]);
        if (Identifier::isContextSpecificClass($identifierOctet) && Identifier::isConstructed($identifierOctet)) {
            return ExplicitlyTaggedObject::fromBinary($binaryData, $offsetIndex);
        }
        switch ($identifierOctet) {
            case Identifier::BITSTRING:
                return BitString::fromBinary($binaryData, $offsetIndex);
            case Identifier::BOOLEAN:
                return Boolean::fromBinary($binaryData, $offsetIndex);
            case Identifier::ENUMERATED:
                return Enumerated::fromBinary($binaryData, $offsetIndex);
            case Identifier::INTEGER:
                return Integer::fromBinary($binaryData, $offsetIndex);
            case Identifier::NULL:
                return NullObject::fromBinary($binaryData, $offsetIndex);
            case Identifier::OBJECT_IDENTIFIER:
                return ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            case Identifier::RELATIVE_OID:
                return RelativeObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            case Identifier::OCTETSTRING:
                return OctetString::fromBinary($binaryData, $offsetIndex);
            case Identifier::SEQUENCE:
                return Sequence::fromBinary($binaryData, $offsetIndex);
            case Identifier::SET:
                return Set::fromBinary($binaryData, $offsetIndex);
            case Identifier::UTC_TIME:
                return UTCTime::fromBinary($binaryData, $offsetIndex);
            case Identifier::GENERALIZED_TIME:
                return GeneralizedTime::fromBinary($binaryData, $offsetIndex);
            case Identifier::IA5_STRING:
                return IA5String::fromBinary($binaryData, $offsetIndex);
            case Identifier::PRINTABLE_STRING:
                return PrintableString::fromBinary($binaryData, $offsetIndex);
            case Identifier::NUMERIC_STRING:
                return NumericString::fromBinary($binaryData, $offsetIndex);
            case Identifier::UTF8_STRING:
                return UTF8String::fromBinary($binaryData, $offsetIndex);
            case Identifier::UNIVERSAL_STRING:
                return UniversalString::fromBinary($binaryData, $offsetIndex);
            case Identifier::CHARACTER_STRING:
                return CharacterString::fromBinary($binaryData, $offsetIndex);
            case Identifier::GENERAL_STRING:
                return GeneralString::fromBinary($binaryData, $offsetIndex);
            case Identifier::VISIBLE_STRING:
                return VisibleString::fromBinary($binaryData, $offsetIndex);
            case Identifier::GRAPHIC_STRING:
                return GraphicString::fromBinary($binaryData, $offsetIndex);
            case Identifier::BMP_STRING:
                return BMPString::fromBinary($binaryData, $offsetIndex);
            case Identifier::T61_STRING:
                return T61String::fromBinary($binaryData, $offsetIndex);
            case Identifier::OBJECT_DESCRIPTOR:
                return ObjectDescriptor::fromBinary($binaryData, $offsetIndex);
            default:
                if (Identifier::isConstructed($identifierOctet)) {
                    return new UnknownConstructedObject($binaryData, $offsetIndex);
                } else {
                    $identifier = self::parseBinaryIdentifier($binaryData, $offsetIndex);
                    $lengthOfUnknownObject = self::parseContentLength($binaryData, $offsetIndex);
                    $offsetIndex += $lengthOfUnknownObject;
                    return new UnknownObject($identifier, $lengthOfUnknownObject);
                }
        }
    }
    protected static function parseIdentifier($identifierOctet, $expectedIdentifier, $offsetForExceptionHandling)
    {
        if (is_string($identifierOctet) || is_numeric($identifierOctet) == false) {
            $identifierOctet = ord($identifierOctet);
        }
        if ($identifierOctet != $expectedIdentifier) {
            $message = 'Can not create an '.Identifier::getName($expectedIdentifier).' from an '.Identifier::getName($identifierOctet);
            throw new ParserException($message, $offsetForExceptionHandling);
        }
    }
    protected static function parseBinaryIdentifier($binaryData, &$offsetIndex)
    {
        if (strlen($binaryData) <= $offsetIndex) {
            throw new ParserException('Can not parse identifier from data: Offset index larger than input size', $offsetIndex);
        }
        $identifier = $binaryData[$offsetIndex++];
        if (Identifier::isLongForm(ord($identifier)) == false) {
            return $identifier;
        }
        while (true) {
            if (strlen($binaryData) <= $offsetIndex) {
                throw new ParserException('Can not parse identifier (long form) from data: Offset index larger than input size', $offsetIndex);
            }
            $nextOctet = $binaryData[$offsetIndex++];
            $identifier .= $nextOctet;
            if ((ord($nextOctet) & 0x80) === 0) {
                break;
            }
        }
        return $identifier;
    }
    protected static function parseContentLength(&$binaryData, &$offsetIndex, $minimumLength = 0)
    {
        if (strlen($binaryData) <= $offsetIndex) {
            throw new ParserException('Can not parse content length from data: Offset index larger than input size', $offsetIndex);
        }
        $contentLength = ord($binaryData[$offsetIndex++]);
        if (($contentLength & 0x80) != 0) {
            $nrOfLengthOctets = $contentLength & 0x7F;
            $contentLength = BigInteger::create(0x00);
            for ($i = 0; $i < $nrOfLengthOctets; $i++) {
                if (strlen($binaryData) <= $offsetIndex) {
                    throw new ParserException('Can not parse content length (long form) from data: Offset index larger than input size', $offsetIndex);
                }
                $contentLength = $contentLength->shiftLeft(8)->add(ord($binaryData[$offsetIndex++]));
            }
            if ($contentLength->compare(PHP_INT_MAX) > 0) {
                throw new ParserException("Can not parse content length from data: length > maximum integer", $offsetIndex);
            }
            $contentLength = $contentLength->toInteger();
        }
        if ($contentLength < $minimumLength) {
            throw new ParserException('A '.get_called_class()." should have a content length of at least {$minimumLength}. Extracted length was {$contentLength}", $offsetIndex);
        }
        $lenDataRemaining = strlen($binaryData) - $offsetIndex;
        if ($lenDataRemaining < $contentLength) {
            throw new ParserException("Content length {$contentLength} exceeds remaining data length {$lenDataRemaining}", $offsetIndex);
        }
        return $contentLength;
    }}