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
namespace pocketmine\network\mcpe;
use pocketmine\network\mcpe\protocol\LoginPacket;use pocketmine\Player;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;use pocketmine\thread\NonThreadSafeValue;use function assert;use function base64_decode;use function chr;use function explode;use function json_decode;use function ltrim;use function openssl_verify;use function ord;use function str_split;use function strlen;use function time;use function wordwrap;use const OPENSSL_ALGO_SHA384;
class VerifyLoginTask extends AsyncTask{
	public const MOJANG_OLD_ROOT_PUBLIC_KEY = "MHYwEAYHKoZIzj0CAQYFK4EEACIDYgAE8ELkixyLcwlZryUQcu1TvPOmI2B7vX83ndnWRUaXm74wFfa5f/lwQNTfrLVHa2PmenpGI6JhIMUJaWZrjmMj90NoKNFSNBuKdm8rYiXsfaz3K36x/1U26HpG0ZxK/V1V";
	public const MOJANG_ROOT_PUBLIC_KEY = "MHYwEAYHKoZIzj0CAQYFK4EEACIDYgAECRXueJeTDqNRRgJi/vlRufByu/2G0i2Ebt6YMar5QX/R0DIIyrJMcUpruK4QveTfJSTp3Shlq4Gk34cD/4GUWwkv0DVuzeuB+tXija7HBxii03NHDbPAD0AKnLr2wdAp";
	private const CLOCK_DRIFT_MAX = 60 * 60 * 24 * 7;
	private $packet;
	private $error = "Unknown";
	private $authenticated = false;
	public function __construct(Player $player, LoginPacket $packet){
		$this->storeLocal($player);
		$this->packet = new NonThreadSafeValue($packet);
	}
	public function onRun() : void{
		$packet = $this->packet->deserialize(); 
		try{
			$currentKey = null;
			$first = true;
			foreach($packet->chainData["chain"] as $jwt){
				$this->validateToken($jwt, $currentKey, $first);
				$first = false;
			}
			$this->validateToken($packet->clientDataJwt, $currentKey);
			$this->error = null;
		}catch(VerifyLoginException $e){
			$this->error = $e->getMessage();
		}
	}
	private function validateToken(string $jwt, ?string &$currentPublicKey, bool $first = false) : void{
		[$headB64, $payloadB64, $sigB64] = explode('.', $jwt);
		$headers = json_decode(base64_decode(strtr($headB64, '-_', '+/'), true), true);
		if($currentPublicKey === null){
			if(!$first){
				throw new VerifyLoginException("%pocketmine.disconnect.invalidSession.missingKey");
			}
			$currentPublicKey = $headers["x5u"];
		}
		$plainSignature = base64_decode(strtr($sigB64, '-_', '+/'), true);
		assert(strlen($plainSignature) === 96);
		[$rString, $sString] = str_split($plainSignature, 48);
		$rString = ltrim($rString, "\x00");
		if(ord($rString[0]) >= 128){ 
			$rString = "\x00" . $rString;
		}
		$sString = ltrim($sString, "\x00");
		if(ord($sString[0]) >= 128){ 
			$sString = "\x00" . $sString;
		}
		$sequence = "\x02" . chr(strlen($rString)) . $rString . "\x02" . chr(strlen($sString)) . $sString;
		$derSignature = "\x30" . chr(strlen($sequence)) . $sequence;
		$v = openssl_verify("$headB64.$payloadB64", $derSignature, "-----BEGIN PUBLIC KEY-----\n" . wordwrap($currentPublicKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----\n", OPENSSL_ALGO_SHA384);
		if($v !== 1){
			throw new VerifyLoginException("%pocketmine.disconnect.invalidSession.badSignature");
		}
		if($currentPublicKey === self::MOJANG_ROOT_PUBLIC_KEY || $currentPublicKey === self::MOJANG_OLD_ROOT_PUBLIC_KEY){
			$this->authenticated = true; 
		}
		$claims = json_decode(base64_decode(strtr($payloadB64, '-_', '+/'), true), true);
		$time = time();
		if(isset($claims["nbf"]) and $claims["nbf"] > $time + self::CLOCK_DRIFT_MAX){
			throw new VerifyLoginException("%pocketmine.disconnect.invalidSession.tooEarly");
		}
		if(isset($claims["exp"]) and $claims["exp"] < $time - self::CLOCK_DRIFT_MAX){
			throw new VerifyLoginException("%pocketmine.disconnect.invalidSession.tooLate");
		}
		$currentPublicKey = $claims["identityPublicKey"] ?? null; 
	}
	public function onCompletion(Server $server){
		$player = $this->fetchLocal();
		if(!$player->isConnected()){
			$server->getLogger()->error("Player " . $player->getName() . " was disconnected before their login could be verified");
		}else{
			$player->onVerifyCompleted($this->packet->deserialize(), $this->error, $this->authenticated);
		}
	}}