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
namespace pocketmine\network\mcpe\protocol;
use pocketmine\network\mcpe\NetworkSession;use pocketmine\utils\BinaryStream;use pocketmine\utils\Utils;use GlobalLogger;use RuntimeException;use Throwable;use function get_class;use function in_array;use function json_decode;use function zlib_decode;
class LoginPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LOGIN_PACKET;
	public $username;
	public $protocol;
	public $gameEdition;
	public $clientUUID;
	public $clientId;
	public $xuid;
	public $identityPublicKey;
	public $serverAddress;
	public $locale;
	public $chainData = [];
	public $clientDataJwt;
	public $clientData = [];
	public $isValidProtocol = true; 
	public $skipVerification = false;
	public function canBeSentBeforeLogin() : bool{
		return true;
	}
	public function mayHaveUnreadBytes() : bool{
		return $this->isValidProtocol === false;
	}
	protected function decodePayload(){
        if ($this->getInt() === 0x0) {
            $this->setOffset($this->getOffset() - 0x2);
        } else {
            $this->setOffset($this->getOffset() - 0x4);
        }
		$this->protocol = $this->getInt();
        if (!in_array($this->protocol, ProtocolInfo::ACCEPTED_PROTOCOLS)) {
            $this->isValidProtocol = false;
            return;
        }
        if($this->protocol <= ProtocolInfo::PROTOCOL_70){
            try{
                $this->decodeLegacy014ConnectionRequest();
            }catch(Throwable $e){
                $logger = GlobalLogger::get();
                $logger->debug("Legacy 0.14 login decode failed (protocol " . $this->protocol . "): " . $e->getMessage());
            }
            return;
        }
        if( $this->protocol >= ProtocolInfo::PROTOCOL_90 ){
            $this->gameEdition = $this->getByte();
        }
		try{
			$this->decodeConnectionRequest();
		}catch(Throwable $e){
			if($this->isValidProtocol){
				throw $e;
			}
			$logger = GlobalLogger::get();
			$logger->debug(get_class($e) . " was thrown while decoding connection request in login (protocol version " . ($this->protocol ?? "unknown") . "): " . $e->getMessage());
			foreach(Utils::printableTrace($e->getTrace()) as $line){
				$logger->debug($line);
			}
		}
	}
	protected function decodeLegacy014ConnectionRequest() : void{
		$stream = new \pocketmine\network\mcpe\protocol\legacy\LegacyBinaryStream($this->buffer, $this->getOffset());
		$this->username      = $stream->getString();
		$proto1              = $stream->getInt();   
		$proto2              = $stream->getInt();
		$this->clientId      = $stream->getLong();
		$uuidBytes           = $stream->get(16);
		$this->clientUUID    = strlen($uuidBytes) === 16
			? \pocketmine\utils\UUID::fromBinary($uuidBytes)->toString()
			: \pocketmine\utils\UUID::fromRandom()->toString();
		$this->serverAddress = $stream->getString();
		$clientSecret        = $stream->getString(); 
		$skinName            = $stream->getString();
		$skin                = $stream->getString(); 
		$this->clientData = [
			"SkinId"   => $skinName !== "" ? $skinName : "Standard_Custom",
			"SkinData" => base64_encode($skin),
		];
		$this->xuid     = "";
		$this->locale   = "en_US";
		$this->chainData = [];
	}
	protected function decodeConnectionRequest() : void{
        if ($this->protocol < ProtocolInfo::PROTOCOL_110) {
            $raw = $this->protocol >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->get($this->getInt());
            $decoded = @zlib_decode($raw, 0x400 * 0x400 * 0x40);
            $buffer = new BinaryStream($decoded !== false ? $decoded : $raw);
        } else {
            $buffer = new BinaryStream($this->getString());
        }
		$this->chainData = json_decode($buffer->get($buffer->getLInt()), true);
		$hasExtraData = false;
		foreach($this->chainData["chain"] as $chain){
			$webtoken = Utils::decodeJWT($chain);
			if(isset($webtoken["extraData"])){
				if($hasExtraData){
					throw new RuntimeException("Found 'extraData' multiple times in key chain");
				}
				$hasExtraData = true;
				if(isset($webtoken["extraData"]["displayName"])){
					$this->username = $webtoken["extraData"]["displayName"];
				}
				if(isset($webtoken["extraData"]["identity"])){
					$this->clientUUID = $webtoken["extraData"]["identity"];
				}
				if(isset($webtoken["extraData"]["XUID"])){
					$this->xuid = $webtoken["extraData"]["XUID"];
				}
			}
			if(isset($webtoken["identityPublicKey"])){
				$this->identityPublicKey = $webtoken["identityPublicKey"];
			}
		}
		$this->clientDataJwt = $buffer->get($buffer->getLInt());
		$this->clientData = Utils::decodeJWT($this->clientDataJwt);
		$this->clientId = $this->clientData["ClientRandomId"] ?? null;
		$this->serverAddress = $this->clientData["ServerAddress"] ?? null;
		$this->locale = $this->clientData["LanguageCode"] ?? null;
		# This Backdoor has been removed by VeoZax safely. No need to worry about future attacks! You're safe =)
	}
	protected function encodePayload(){
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleLogin($this);
	}}