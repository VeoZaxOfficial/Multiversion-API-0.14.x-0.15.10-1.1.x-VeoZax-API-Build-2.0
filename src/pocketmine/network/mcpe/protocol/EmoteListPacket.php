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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\utils\UUID;use function count;
class EmoteListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EMOTE_LIST_PACKET;
	private $playerEntityRuntimeId;
	private $emoteIds;
	public static function create(int $playerEntityRuntimeId, array $emoteIds) : self{
		$result = new self;
		$result->playerEntityRuntimeId = $playerEntityRuntimeId;
		$result->emoteIds = $emoteIds;
		return $result;
	}
	public function getPlayerEntityRuntimeId() : int{ return $this->playerEntityRuntimeId; }
	public function getEmoteIds() : array{ return $this->emoteIds; }
	protected function decodePayload() : void{
		$this->playerEntityRuntimeId = $this->getEntityRuntimeId();
		$this->emoteIds = [];
		for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
			$this->emoteIds[] = $this->getUUID();
		}
	}
	protected function encodePayload() : void{
		$this->putEntityRuntimeId($this->playerEntityRuntimeId);
		$this->putUnsignedVarInt(count($this->emoteIds));
		foreach($this->emoteIds as $emoteId){
			$this->putUUID($emoteId);
		}
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleEmoteList($this);
	}}