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
use FG\ASN1\Exception\ParserException;use FG\ASN1\ASNObject;use FG\ASN1\OID;use FG\ASN1\Parsable;use FG\ASN1\Identifier;use FG\ASN1\Universal\Sequence;
class SubjectAlternativeNames extends ASNObject implements Parsable{
    private $alternativeNamesSequence;
    public function __construct()
    {
        $this->alternativeNamesSequence = new Sequence();
    }
    protected function calculateContentLength()
    {
        return $this->alternativeNamesSequence->getObjectLength();
    }
    public function getType()
    {
        return Identifier::OCTETSTRING;
    }
    public function addDomainName(DNSName $domainName)
    {
        $this->alternativeNamesSequence->addChild($domainName);
    }
    public function addIP(IPAddress $ip)
    {
        $this->alternativeNamesSequence->addChild($ip);
    }
    public function getContent()
    {
        return $this->alternativeNamesSequence->getContent();
    }
    protected function getEncodedValue()
    {
        return $this->alternativeNamesSequence->getBinary();
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::OCTETSTRING, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        if ($contentLength < 2) {
            throw new ParserException('Can not parse Subject Alternative Names: The Sequence within the octet string after the Object identifier '.OID::CERT_EXT_SUBJECT_ALT_NAME." is too short ({$contentLength} octets)", $offsetIndex);
        }
        $offsetOfSequence = $offsetIndex;
        $sequence = Sequence::fromBinary($binaryData, $offsetIndex);
        $offsetOfSequence += $sequence->getNumberOfLengthOctets() + 1;
        if ($sequence->getObjectLength() != $contentLength) {
            throw new ParserException('Can not parse Subject Alternative Names: The Sequence length does not match the length of the surrounding octet string', $offsetIndex);
        }
        $parsedObject = new self();
        foreach ($sequence as $object) {
            if ($object->getType() == DNSName::IDENTIFIER) {
                $domainName = DNSName::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addDomainName($domainName);
            } elseif ($object->getType() == IPAddress::IDENTIFIER) {
                $ip = IPAddress::fromBinary($binaryData, $offsetOfSequence);
                $parsedObject->addIP($ip);
            } else {
                throw new ParserException('Could not parse Subject Alternative Name: Only DNSName and IP SANs are currently supported', $offsetIndex);
            }
        }
        $parsedObject->getBinary(); 
        return $parsedObject;
    }}