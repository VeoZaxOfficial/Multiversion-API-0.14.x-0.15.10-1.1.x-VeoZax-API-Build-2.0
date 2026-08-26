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
use Closure;use InvalidArgumentException;use JsonException;use pocketmine\network\mcpe\JwtUtils;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;use RuntimeException;use function igbinary_serialize;use function igbinary_unserialize;use function openssl_error_string;use function openssl_free_key;use function openssl_pkey_get_details;use function openssl_pkey_new;use function random_bytes;
class PrepareEncryptionTask extends AsyncTask{
	private static $SERVER_PRIVATE_KEY = null;
	private $serverPrivateKey;
	private $serverPublickey;
	private $aesKey = null;
	private $handshakeJwt = null;
	private $clientPub;
	private $serverTokenRandom;
	public function __construct(string $clientPub, Closure $onCompletion){
		if(self::$SERVER_PRIVATE_KEY === null){
			$serverPrivateKey = openssl_pkey_new(["ec" => ["curve_name" => "secp384r1"]]);
			if($serverPrivateKey === false){
				throw new RuntimeException("openssl_pkey_new() failed: " . openssl_error_string());
			}
			self::$SERVER_PRIVATE_KEY = $serverPrivateKey;
		}
		$this->serverPrivateKey = igbinary_serialize(openssl_pkey_get_details(self::$SERVER_PRIVATE_KEY));
		$this->clientPub = $clientPub;
		$this->storeLocal($onCompletion);
	}
	public function onRun() : void{
		$serverPrivDetails = igbinary_unserialize($this->serverPrivateKey);
		$serverPriv = openssl_pkey_new($serverPrivDetails);
		if($serverPriv === false) throw new  InvalidArgumentException("Failed to restore server signing key from details");
		$clientPub = JwtUtils::parseDerPublicKey($this->clientPub);
		$sharedSecret = EncryptionUtils::generateSharedSecret($serverPriv, $clientPub);
		$salt = random_bytes(16);
		$this->aesKey = EncryptionUtils::generateKey($sharedSecret, $salt);
		$derServPublicKey = JwtUtils::emitDerPublicKey($serverPriv);
		$this->serverPublickey = base64_encode($derServPublicKey);
		$this->handshakeJwt = EncryptionUtils::generateServerHandshakeJwt($derServPublicKey, $serverPriv, $salt);
		$this->serverTokenRandom = $salt;
		@openssl_free_key($serverPriv);
		@openssl_free_key($clientPub);
	}
	public function onCompletion(Server $server) : void{
		$callback = $this->fetchLocal();
		if($this->aesKey === null || $this->handshakeJwt === null){
			throw new  InvalidArgumentException("Something strange happened here ...");
		}
		$callback($this->aesKey, $this->handshakeJwt, $this->serverPublickey, $this->serverTokenRandom);
	}}