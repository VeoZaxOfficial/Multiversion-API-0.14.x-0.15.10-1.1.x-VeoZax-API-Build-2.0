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

namespace pocketmine\network\mcpe\protocol\legacy\p84;
use pocketmine\utils\p70\Binary;
class EntityEventPacket extends DataPacket{
	const NETWORK_ID = Info::ENTITY_EVENT_PACKET;
	const HURT_ANIMATION = 2;
	const DEATH_ANIMATION = 3;
	const TAME_FAIL = 6;
	const TAME_SUCCESS = 7;
	const SHAKE_WET = 8;
	const USE_ITEM = 9;
	const EAT_GRASS_ANIMATION = 10;
	const FISH_HOOK_BUBBLE = 11;
	const FISH_HOOK_POSITION = 12;
	const FISH_HOOK_HOOK = 13;
	const FISH_HOOK_TEASE = 14;
	const SQUID_INK_CLOUD = 15;
	const AMBIENT_SOUND = 16;
	const RESPAWN = 17;
	public $eid;
	public $event;
	public function decode(){
		$this->eid = Binary::readLong($this->get(8));
		$this->event = ord($this->get(1));
	}
	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->eid);
		$this->buffer .= chr($this->event);
	}
}