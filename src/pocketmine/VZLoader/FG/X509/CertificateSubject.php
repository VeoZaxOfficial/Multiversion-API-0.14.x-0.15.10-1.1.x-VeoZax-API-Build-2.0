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
use FG\ASN1\Composite\RelativeDistinguishedName;use FG\ASN1\Identifier;use FG\ASN1\OID;use FG\ASN1\Parsable;use FG\ASN1\Composite\RDNString;use FG\ASN1\Universal\Sequence;
class CertificateSubject extends Sequence implements Parsable{
    private $commonName;
    private $email;
    private $organization;
    private $locality;
    private $state;
    private $country;
    private $organizationalUnit;
    public function __construct($commonName, $email, $organization, $locality, $state, $country, $organizationalUnit)
    {
        parent::__construct(
            new RDNString(OID::COUNTRY_NAME, $country),
            new RDNString(OID::STATE_OR_PROVINCE_NAME, $state),
            new RDNString(OID::LOCALITY_NAME, $locality),
            new RDNString(OID::ORGANIZATION_NAME, $organization),
            new RDNString(OID::OU_NAME, $organizationalUnit),
            new RDNString(OID::COMMON_NAME, $commonName),
            new RDNString(OID::PKCS9_EMAIL, $email)
        );
        $this->commonName = $commonName;
        $this->email = $email;
        $this->organization = $organization;
        $this->locality = $locality;
        $this->state = $state;
        $this->country = $country;
        $this->organizationalUnit = $organizationalUnit;
    }
    public function getCommonName()
    {
        return $this->commonName;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getOrganization()
    {
        return $this->organization;
    }
    public function getLocality()
    {
        return $this->locality;
    }
    public function getState()
    {
        return $this->state;
    }
    public function getCountry()
    {
        return $this->country;
    }
    public function getOrganizationalUnit()
    {
        return $this->organizationalUnit;
    }
    public static function fromBinary(&$binaryData, &$offsetIndex = 0)
    {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        $names = [];
        $octetsToRead = $contentLength;
        while ($octetsToRead > 0) {
            $relativeDistinguishedName = RelativeDistinguishedName::fromBinary($binaryData, $offsetIndex);
            $octetsToRead -= $relativeDistinguishedName->getObjectLength();
            $names[] = $relativeDistinguishedName;
        }
    }}