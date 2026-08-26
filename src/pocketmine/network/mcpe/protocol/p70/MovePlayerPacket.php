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

namespace pocketmine\network\mcpe\protocol\legacy\p70;
use pocketmine\utils\p70\Binary;
class MovePlayerPacket extends DataPacket{
	const NETWORK_ID = Info::MOVE_PLAYER_PACKET;
	const MODE_NORMAL = 0;
	const MODE_RESET = 1;
	const MODE_ROTATION = 2;
	public $eid;
	public $x;
	public $y;
	public $z;
	public $yaw;
	public $bodyYaw;
	public $pitch;
	public $mode = self::MODE_NORMAL;
	public $onGround;
	public function clean(){
		$this->teleport = \false;
		return parent::clean();
	}
	public function decode(){
		$this->eid = Binary::readLong($this->get(8));
		$this->x = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->y = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->z = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->yaw = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->bodyYaw = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->pitch = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->mode = ord($this->get(1));
		$this->onGround = ord($this->get(1)) > 0;
	}
	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->eid);
		$this->buffer .= Binary::writeFloat($this->x);
		$this->buffer .= Binary::writeFloat($this->y);
		$this->buffer .= Binary::writeFloat($this->z);
		$this->buffer .= Binary::writeFloat($this->yaw);
		$this->buffer .= Binary::writeFloat($this->bodyYaw); 
		$this->buffer .= Binary::writeFloat($this->pitch);
		$this->buffer .= chr($this->mode);
		$this->buffer .= chr($this->onGround > 0);
	}
}