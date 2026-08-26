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
class Identifier{
    const CLASS_UNIVERSAL        = 0x00;
    const CLASS_APPLICATION      = 0x01;
    const CLASS_CONTEXT_SPECIFIC = 0x02;
    const CLASS_PRIVATE          = 0x03;
    const EOC               = 0x00; 
    const BOOLEAN           = 0x01;
    const INTEGER           = 0x02;
    const BITSTRING         = 0x03;
    const OCTETSTRING       = 0x04;
    const NULL              = 0x05;
    const OBJECT_IDENTIFIER = 0x06;
    const OBJECT_DESCRIPTOR = 0x07;
    const EXTERNAL          = 0x08; 
    const REAL              = 0x09; 
    const ENUMERATED        = 0x0A;
    const EMBEDDED_PDV      = 0x0B; 
    const UTF8_STRING       = 0x0C;
    const RELATIVE_OID      = 0x0D;
    const SEQUENCE          = 0x30;
    const SET               = 0x31;
    const NUMERIC_STRING    = 0x12;
    const PRINTABLE_STRING  = 0x13;
    const T61_STRING        = 0x14; 
    const VIDEOTEXT_STRING  = 0x15;
    const IA5_STRING        = 0x16;
    const UTC_TIME          = 0x17;
    const GENERALIZED_TIME  = 0x18;
    const GRAPHIC_STRING    = 0x19;
    const VISIBLE_STRING    = 0x1A;
    const GENERAL_STRING    = 0x1B;
    const UNIVERSAL_STRING  = 0x1C;
    const CHARACTER_STRING  = 0x1D; 
    const BMP_STRING        = 0x1E;
    const LONG_FORM         = 0x1F;
    const IS_CONSTRUCTED    = 0x20;
    public static function create($class, $isConstructed, $tagNumber)
    {
        if (!is_numeric($class) || $class < self::CLASS_UNIVERSAL || $class > self::CLASS_PRIVATE) {
            throw new Exception(sprintf('Invalid class %d given', $class));
        }
        if (!is_bool($isConstructed)) {
            throw new Exception("\$isConstructed must be a boolean value ($isConstructed given)");
        }
        $tagNumber = self::makeNumeric($tagNumber);
        if ($tagNumber < 0) {
            throw new Exception(sprintf('Invalid $tagNumber %d given. You can only use positive integers.', $tagNumber));
        }
        if ($tagNumber < self::LONG_FORM) {
            return ($class << 6) | ($isConstructed << 5) | $tagNumber;
        }
        $firstOctet = ($class << 6) | ($isConstructed << 5) | self::LONG_FORM;
        return chr($firstOctet).Base128::encode($tagNumber);
    }
    public static function isConstructed($identifierOctet)
    {
        return ($identifierOctet & self::IS_CONSTRUCTED) === self::IS_CONSTRUCTED;
    }
    public static function isLongForm($identifierOctet)
    {
        return ($identifierOctet & self::LONG_FORM) === self::LONG_FORM;
    }
    public static function getName($identifier)
    {
        $identifierOctet = self::makeNumeric($identifier);
        $typeName = static::getShortName($identifier);
        if (($identifierOctet & self::LONG_FORM) < self::LONG_FORM) {
            $typeName = "ASN.1 {$typeName}";
        }
        return $typeName;
    }
    public static function getShortName($identifier)
    {
        $identifierOctet = self::makeNumeric($identifier);
        switch ($identifierOctet) {
            case self::EOC:
                return 'End-of-contents octet';
            case self::BOOLEAN:
                return 'Boolean';
            case self::INTEGER:
                return 'Integer';
            case self::BITSTRING:
                return 'Bit String';
            case self::OCTETSTRING:
                return 'Octet String';
            case self::NULL:
                return 'NULL';
            case self::OBJECT_IDENTIFIER:
                return 'Object Identifier';
            case self::OBJECT_DESCRIPTOR:
                return 'Object Descriptor';
            case self::EXTERNAL:
                return 'External Type';
            case self::REAL:
                return 'Real';
            case self::ENUMERATED:
                return 'Enumerated';
            case self::EMBEDDED_PDV:
                return 'Embedded PDV';
            case self::UTF8_STRING:
                return 'UTF8 String';
            case self::RELATIVE_OID:
                return 'Relative OID';
            case self::SEQUENCE:
                return 'Sequence';
            case self::SET:
                return 'Set';
            case self::NUMERIC_STRING:
                return 'Numeric String';
            case self::PRINTABLE_STRING:
                return 'Printable String';
            case self::T61_STRING:
                return 'T61 String';
            case self::VIDEOTEXT_STRING:
                return 'Videotext String';
            case self::IA5_STRING:
                return 'IA5 String';
            case self::UTC_TIME:
                return 'UTC Time';
            case self::GENERALIZED_TIME:
                return 'Generalized Time';
            case self::GRAPHIC_STRING:
                return 'Graphic String';
            case self::VISIBLE_STRING:
                return 'Visible String';
            case self::GENERAL_STRING:
                return 'General String';
            case self::UNIVERSAL_STRING:
                return 'Universal String';
            case self::CHARACTER_STRING:
                return 'Character String';
            case self::BMP_STRING:
                return 'BMP String';
            case 0x0E:
                return 'RESERVED (0x0E)';
            case 0x0F:
                return 'RESERVED (0x0F)';
            case self::LONG_FORM:
            default:
                $classDescription = self::getClassDescription($identifier);
                if (is_int($identifier)) {
                    $identifier = chr($identifier);
                }
                return "$classDescription (0x".strtoupper(bin2hex($identifier)).')';
        }
    }
    public static function getClassDescription($identifier)
    {
        $identifierOctet = self::makeNumeric($identifier);
        if (self::isConstructed($identifierOctet)) {
            $classDescription = 'Constructed ';
        } else {
            $classDescription = 'Primitive ';
        }
        $classBits = $identifierOctet >> 6;
        switch ($classBits) {
            case self::CLASS_UNIVERSAL:
                $classDescription .= 'universal';
                break;
            case self::CLASS_APPLICATION:
                $classDescription .= 'application';
                break;
            case self::CLASS_CONTEXT_SPECIFIC:
                $tagNumber = self::getTagNumber($identifier);
                $classDescription = "[$tagNumber] Context-specific";
                break;
            case self::CLASS_PRIVATE:
                $classDescription .= 'private';
                break;
            default:
                return "INVALID IDENTIFIER OCTET: {$identifierOctet}";
        }
        return $classDescription;
    }
    public static function getTagNumber($identifier)
    {
        $firstOctet = self::makeNumeric($identifier);
        $tagNumber = $firstOctet & self::LONG_FORM;
        if ($tagNumber < self::LONG_FORM) {
            return $tagNumber;
        }
        if (is_numeric($identifier)) {
            $identifier = chr($identifier);
        }
        return Base128::decode(substr($identifier, 1));
    }
    public static function isUniversalClass($identifier)
    {
        $identifier = self::makeNumeric($identifier);
        return $identifier >> 6 == self::CLASS_UNIVERSAL;
    }
    public static function isApplicationClass($identifier)
    {
        $identifier = self::makeNumeric($identifier);
        return $identifier >> 6 == self::CLASS_APPLICATION;
    }
    public static function isContextSpecificClass($identifier)
    {
        $identifier = self::makeNumeric($identifier);
        return $identifier >> 6 == self::CLASS_CONTEXT_SPECIFIC;
    }
    public static function isPrivateClass($identifier)
    {
        $identifier = self::makeNumeric($identifier);
        return $identifier >> 6 == self::CLASS_PRIVATE;
    }
    private static function makeNumeric($identifierOctet)
    {
        if (!is_numeric($identifierOctet)) {
            return ord($identifierOctet);
        } else {
            return $identifierOctet;
        }
    }}