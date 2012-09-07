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
	
class CSR {
	
	protected $version;
	protected $commonName;
	protected $email;
	protected $orgName;
	protected $localName;
	protected $state;
	protected $country;
	protected $ou;
	protected $publicKey;
	protected $signature;
	protected $signatureAlgorithm;
	
	protected $startSequence;
	
	public function __construct($version, $commonName, $email, $orgName, $localName, $state, $country, $ou, $publicKey, $signature, $signatureAlgorithm = OID_SHA1_WITH_RSA_ENCRYPTION) {						
		if( !is_numeric($version)) throw new Exception("Version must be an int value (Given:[$version])!");
	
		$this->version				= $version;
		$this->commonName			= $commonName;
		$this->email				= $email;
		$this->orgName				= $orgName;
		$this->localName			= $localName;
		$this->state				= $state;
		$this->country				= $country;
		$this->ou					= $ou;
		$this->publicKey			= $publicKey;
		$this->signature			= $signature;
		$this->signatureAlgorithm	= $signatureAlgorithm;
		
		$this->startSequence = $this->createStartSequence();
	}
	
	protected function createStartSequence() {
		$versionNr			= new ASN_Integer($this->version);
		$set_commonName		= new CSR_StringObject(OID_COMMON_NAME, $this->commonName);
		$set_email			= new CSR_SimpleObject(OID_EMAIL, new ASN_IA5String($this->email));
		$set_orgName		= new CSR_StringObject(OID_ORGANIZATION_NAME, $this->orgName);
		$set_localName		= new CSR_StringObject(OID_LOCALITY_NAME, $this->localName);
		$set_state			= new CSR_StringObject(OID_STATE_OR_PROVINCE_NAME, $this->state);
		$set_country		= new CSR_StringObject(OID_COUNTRY_NAME, $this->country);
		$set_ou				= new CSR_StringObject(OID_OU_NAME, $this->ou);
		$publicKey 			= new CSR_PublicKey($this->publicKey);
		$signature			= new ASN_BitString($this->signature);
		$signatureAlgorithm	= new CSR_SignatureKeyAlgorithm($this->signatureAlgorithm);		
		
		$subjectSequence = new ASN_Sequence(array(
			$set_commonName,
			$set_email,
			$set_orgName,
			$set_localName,
			$set_state,
			$set_country,
			$set_ou
		));

		$mainSequence  = new ASN_Sequence(array($versionNr, $subjectSequence, $publicKey));
		$startSequence = new ASN_Sequence(array($mainSequence, $signatureAlgorithm, $signature));
		
		return $startSequence;
	}
	
	public function getBase64DER() {
		$tmp = base64_encode($this->startSequence->getBinary());
		
		for( $i = 0 ; $i < strlen($tmp) ; $i++ ) {
			if(($i+2) % 65 == 0) {
				$tmp = substr($tmp, 0, $i+1) . "\n" . substr($tmp, $i+1);
			}
		}
	
		$result = "-----BEGIN CERTIFICATE REQUEST-----\n";
		$result .= $tmp;
		$result .= "\n-----END CERTIFICATE REQUEST-----";
		
		return $result;
	}

	public function getDERByteSize() {
		return $this->startSequence->getObjectLength();
	}

	public function getVersion() 			{ return $this->version; }
	public function getCommonName()			{ return $this->commonName; }
	public function getEmail()				{ return $this->email; }
	public function getOrgaName()			{ return $this->orgName; }
	public function getLocalName()			{ return $this->localName; }
	public function getState()				{ return $this->state; }
	public function getCountry()			{ return $this->country; }
	public function getOrgaUnits()			{ return $this->ou; }
	public function getPublicKey()			{ return $this->publicKey; }
	public function getSignature()			{ return $this->signature; }
	public function getSignatureAlgorithm()	{ return $this->signatureAlgorithm; }
}
?>