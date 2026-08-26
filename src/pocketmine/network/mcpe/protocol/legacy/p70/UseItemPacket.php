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
class UseItemPacket extends DataPacket{
	const NETWORK_ID = Info::USE_ITEM_PACKET;
	public $x;
	public $y;
	public $z;
	public $face;
	public $item;
	public $fx;
	public $fy;
	public $fz;
	public $posX;
	public $posY;
	public $posZ;
	public function decode(){
		$this->x = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
		$this->y = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
		$this->z = (PHP_INT_SIZE === 8 ? unpack("N", $this->get(4))[1] << 32 >> 32 : unpack("N", $this->get(4))[1]);
		$this->face = ord($this->get(1));
		$this->fx = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->fy = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->fz = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->posX = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->posY = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->posZ = (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]);
		$this->item = $this->getSlot();
	}
	public function encode(){
	}
}