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

namespace FG\X509;
use FG\ASN1\Exception\ParserException;use FG\ASN1\OID;use FG\ASN1\ASNObject;use FG\ASN1\Parsable;use FG\ASN1\Identifier;use FG\ASN1\Universal\OctetString;use FG\ASN1\Universal\Set;use FG\ASN1\Universal\Sequence;use FG\ASN1\Universal\ObjectIdentifier;use FG\X509\SAN\SubjectAlternativeNames;
class CertificateExtensions extends Set implements Parsable{
    private $innerSequence;
    private $extensions = [];
    public function __construct()
    {
        $this->innerSequence = new Sequence();
        parent::__construct($this->innerSequence);
    }
    public function addSubjectAlternativeNames(SubjectAlternativeNames $sans)
    {
        $this->addExtension(OID::CERT_EXT_SUBJECT_ALT_NAME, $sans);
    }
    private function addExtension($oidString, ASNObject $extension)
    {
        $sequence = new Sequence();
        $sequence->addChild(new ObjectIdentifier($oidString));
        $sequence->addChild($extension);
        $this->innerSequence->addChild($sequence);
        $this->extensions[] = $extension;
    }
    public function getContent()
    {
        return $this->extensions;
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SET, $offsetIndex++);
        self::parseContentLength($binaryData, $offsetIndex);
        $tmpOffset = $offsetIndex;
        $extensions = Sequence::fromBinary($binaryData, $offsetIndex);
        $tmpOffset += 1 + $extensions->getNumberOfLengthOctets();
        $parsedObject = new self();
        foreach ($extensions as $extension) {
            if ($extension->getType() != Identifier::SEQUENCE) {
                throw new ParserException('Could not parse Certificate Extensions: Expected ASN.1 Sequence but got '.$extension->getTypeName(), $offsetIndex);
            }
            $tmpOffset += 1 + $extension->getNumberOfLengthOctets();
            $children = $extension->getChildren();
            if (count($children) < 2) {
                throw new ParserException('Could not parse Certificate Extensions: Needs at least two child elements per extension sequence (object identifier and octet string)', $tmpOffset);
            }
            $objectIdentifier = $children[0];
            $octetString = $children[1];
            if ($objectIdentifier->getType() != Identifier::OBJECT_IDENTIFIER) {
                throw new ParserException('Could not parse Certificate Extensions: Expected ASN.1 Object Identifier but got '.$extension->getTypeName(), $tmpOffset);
            }
            $tmpOffset += $objectIdentifier->getObjectLength();
            if ($objectIdentifier->getContent() == OID::CERT_EXT_SUBJECT_ALT_NAME) {
                $sans = SubjectAlternativeNames::fromBinary($binaryData, $tmpOffset);
                $parsedObject->addSubjectAlternativeNames($sans);
            } else {
                $tmpOffset += $octetString->getObjectLength();
            }
        }
        $parsedObject->getBinary(); 
        return $parsedObject;
    }}