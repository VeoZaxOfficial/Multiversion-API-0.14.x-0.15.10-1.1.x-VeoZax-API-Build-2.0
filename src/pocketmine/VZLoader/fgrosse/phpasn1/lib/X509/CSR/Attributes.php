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

namespace FG\X509\CSR;
use FG\ASN1\ASNObject;use FG\X509\CertificateExtensions;use FG\ASN1\OID;use FG\ASN1\Parsable;use FG\ASN1\Construct;use FG\ASN1\Identifier;use FG\ASN1\Universal\Set;use FG\ASN1\Universal\Sequence;use FG\ASN1\Universal\ObjectIdentifier;
class Attributes extends Construct implements Parsable{
    public function getType()
    {
        return 0xA0;
    }
    public function addAttribute($objectIdentifier, Set $attribute)
    {
        if (is_string($objectIdentifier)) {
            $objectIdentifier = new ObjectIdentifier($objectIdentifier);
        }
        $attributeSequence = new Sequence($objectIdentifier, $attribute);
        $attributeSequence->getNumberOfLengthOctets();  
        $this->addChild($attributeSequence);
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], 0xA0, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $octetsToRead = $contentLength;
        $parsedObject = new self();
        while ($octetsToRead > 0) {
            $initialOffset = $offsetIndex; 
            self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
            self::parseContentLength($binaryData, $offsetIndex);
            $objectIdentifier = ObjectIdentifier::fromBinary($binaryData, $offsetIndex);
            $oidString = $objectIdentifier->getContent();
            if ($oidString == OID::PKCS9_EXTENSION_REQUEST) {
                $attribute = CertificateExtensions::fromBinary($binaryData, $offsetIndex);
            } else {
                $attribute = ASNObject::fromBinary($binaryData, $offsetIndex);
            }
            $parsedObject->addAttribute($objectIdentifier, $attribute);
            $octetsToRead -= ($offsetIndex - $initialOffset);
        }
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;
    }}