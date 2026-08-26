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
class AddPlayerPacket extends DataPacket{
	const NETWORK_ID = Info::ADD_PLAYER_PACKET;
	public $uuid;
	public $username = "";
	public $eid;
	public $x;
	public $y;
	public $z;
	public $speedX;
	public $speedY;
	public $speedZ;
	public $pitch;
	public $yaw;
	public $item;
	public $metadata;
	public function decode(){
	}
	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		$this->buffer .= Binary::writeLong($this->eid);
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->x) : strrev(pack("f", $this->x)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->y) : strrev(pack("f", $this->y)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->z) : strrev(pack("f", $this->z)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->speedX) : strrev(pack("f", $this->speedX)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->speedY) : strrev(pack("f", $this->speedY)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->speedZ) : strrev(pack("f", $this->speedZ)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->yaw) : strrev(pack("f", $this->yaw)));
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->yaw) : strrev(pack("f", $this->yaw))); 
		$this->buffer .= (ENDIANNESS === 0 ? pack("f", $this->pitch) : strrev(pack("f", $this->pitch)));
		$this->putSlot($this->item);
		$meta = Binary::writeMetadata($this->metadata);
		$this->buffer .= $meta;
	}
}