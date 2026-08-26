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

declare(strict_types=1);
namespace pocketmine\network\mcpe\encryption;
use Crypto\Cipher;use pocketmine\utils\Binary;use RuntimeException;use function openssl_digest;use function openssl_error_string;use function strlen;use function substr;
class EncryptionContext{
	private const CHECKSUM_ALGO = "sha256";
	public static $ENABLED = true;
	private $key;
	private $decryptCipher;
	private $decryptCounter = 0;
	private $encryptCipher;
	private $encryptCounter = 0;
	public function __construct(string $encryptionKey, string $algorithm, string $iv){
		$this->key = $encryptionKey;
		$this->decryptCipher = new Cipher($algorithm);
		$this->decryptCipher->decryptInit($this->key, $iv);
		$this->encryptCipher = new Cipher($algorithm);
		$this->encryptCipher->encryptInit($this->key, $iv);
	}
	public static function fakeGCM(string $encryptionKey) : self{
		return new EncryptionContext(
			$encryptionKey,
			"AES-256-CTR",
			substr($encryptionKey, 0, 12) . "\x00\x00\x00\x02"
		);
	}
	public static function cfb8(string $encryptionKey) : self{
		return new EncryptionContext(
			$encryptionKey,
			"AES-256-CFB8",
			substr($encryptionKey, 0, 16)
		);
	}
	public function decrypt(string $encrypted) : string{
		if(strlen($encrypted) < 9){
			throw new DecryptionException("Payload is too short");
		}
		$decrypted = $this->decryptCipher->decryptUpdate($encrypted);
		$payload = substr($decrypted, 0, -8);
		$packetCounter = $this->decryptCounter++;
		if(($expected = $this->calculateChecksum($packetCounter, $payload)) !== ($actual = substr($decrypted, -8))){
			throw new DecryptionException("Encrypted packet $packetCounter has invalid checksum (expected " . bin2hex($expected) . ", got " . bin2hex($actual) . ")");
		}
		return $payload;
	}
	public function encrypt(string $payload) : string{
		return $this->encryptCipher->encryptUpdate($payload . $this->calculateChecksum($this->encryptCounter++, $payload));
	}
	private function calculateChecksum(int $counter, string $payload) : string{
		$hash = openssl_digest(Binary::writeLLong($counter) . $payload . $this->key, self::CHECKSUM_ALGO, true);
		if($hash === false){
			throw new RuntimeException("openssl_digest() error: " . openssl_error_string());
		}
		return substr($hash, 0, 8);
	}}