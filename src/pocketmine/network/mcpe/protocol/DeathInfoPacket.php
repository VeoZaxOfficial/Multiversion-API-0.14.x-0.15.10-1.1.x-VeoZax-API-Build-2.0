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
use pocketmine\network\mcpe\NetworkSession;use function count;
class DeathInfoPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::DEATH_INFO_PACKET;
	private string $messageTranslationKey;
	private array $messageParameters;
	public static function create(string $messageTranslationKey, array $messageParameters) : self{
		$result = new self;
		$result->messageTranslationKey = $messageTranslationKey;
		$result->messageParameters = $messageParameters;
		return $result;
	}
	public function getMessageTranslationKey() : string{ return $this->messageTranslationKey; }
	public function getMessageParameters() : array{ return $this->messageParameters; }
	protected function decodePayload(){
		$this->messageTranslationKey = $this->getString();
		$this->messageParameters = [];
		for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; $i++){
			$this->messageParameters[] = $this->getString();
		}
	}
	protected function encodePayload(){
		$this->putString($this->messageTranslationKey);
		$this->putUnsignedVarInt(count($this->messageParameters));
		foreach($this->messageParameters as $parameter){
			$this->putString($parameter);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleDeathInfo($this);
	}}