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
use pocketmine\network\mcpe\NetworkSession;
class EmotePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::EMOTE_PACKET;
	public const FLAG_SERVER = 1 << 0;
	public const FLAG_MUTE_ANNOUNCEMENT = 1 << 1;
	public $entityRuntimeId;
	public $emoteLengthTicks = 0;
	public $emoteId;
	public $xboxUserId;
	public $platformChatId;
	public $flags;
	public static function create(int $entityRuntimeId, string $emoteId, int $emoteLengthTicks, string $xboxUserId, string $platformChatId, int $flags) : self{
		$result = new self;
		$result->entityRuntimeId = $entityRuntimeId;
		$result->emoteId = $emoteId;
		$result->emoteLengthTicks = $emoteLengthTicks;
		$result->xboxUserId = $xboxUserId;
		$result->platformChatId = $platformChatId;
		$result->flags = $flags;
		return $result;
	}
	public function getEntityRuntimeIdField() : int{
		return $this->entityRuntimeId;
	}
	public function getEmoteId() : string{
		return $this->emoteId;
	}
	public function getEmoteLengthTicks() : int{ return $this->emoteLengthTicks; }
	public function getXboxUserId() : string{ return $this->xboxUserId; }
	public function getPlatformChatId() : string{ return $this->platformChatId; }
	public function getFlags() : int{
		return $this->flags;
	}
	protected function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->emoteId = $this->getString();
		
		$this->flags = $this->getByte();
	}
	protected function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putString($this->emoteId);
		
        $this->putByte($this->flags);
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleEmote($this);
	}}