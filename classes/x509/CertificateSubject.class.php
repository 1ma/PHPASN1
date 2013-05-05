<?php
/*
 * This file is part of PHPASN1 written by Friedrich Große.
 * 
 * Copyright © Friedrich Große, Berlin 2012
 * 
 * PHPASN1 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PHPASN1 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHPASN1.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace PHPASN1;

class CertificateSubject extends ASN_Sequence implements Parseable {
    
    private $commonName;
    private $email;
    private $organization;
    private $locality;
    private $state;
    private $country;
    private $organizationalUnit;
    
    public function __construct($commonName, $email, $orga, $locality, $state, $country, $ou) {
        parent::__construct(
            new RDNString(OID::COUNTRY_NAME, $country),
            new RDNString(OID::STATE_OR_PROVINCE_NAME, $state),
            new RDNString(OID::LOCALITY_NAME, $locality),
            new RDNString(OID::ORGANIZATION_NAME, $orga),
            new RDNString(OID::OU_NAME, $ou),
            new RDNString(OID::COMMON_NAME, $commonName),
            new RDNString(OID::PKCS9_EMAIL, $email)
        );
        
        $this->commonName = $commonName;
        $this->email = $email;       
        $this->organization = $orga;
        $this->locality = $locality;
        $this->state = $state;
        $this->country = $country;
        $this->organizationalUnit = $ou;
    }
    
    public function getCommonName() {
        return $this->commonName;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function getOrganization() {
        return $this->organization;
    }
    
    public function getLocality() {
        return $this->locality;
    }
    
    public function getState() {
        return $this->state;
    }
    
    public function getCountry() {
        return $this->country;
    }
    
    public function getOrganizationalUnit() {
        return $this->organizationalUnit;
    }
    
    public static function fromBinary(&$binaryData, &$offsetIndex=0) {
        self::parseIdentifier($binaryData[$offsetIndex], Identifier::SEQUENCE, $offsetIndex++);
        $contentLength = self::parseContentLength($binaryData, $offsetIndex);
        
        $rdns = array();
        $octetsToRead = $contentLength;
        while($octetsToRead > 0) {
            $relativeDistinguishedName = RelativeDistinguishedName::fromBinary($binaryData, $offsetIndex);
            $octetsToRead -= $relativeDistinguishedName->getObjectLength();
            $rdns[] = $relativeDistinguishedName;
        }
/*
        $parsedObject = new static();                
        $parsedObject->addChildren($children);
        $parsedObject->setContentLength($contentLength);
        return $parsedObject;*/
    }
}
?>
