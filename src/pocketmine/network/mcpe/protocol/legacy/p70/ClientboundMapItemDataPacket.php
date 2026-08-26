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
use pocketmine\utils\Color;
class ClientboundMapItemDataPacket extends DataPacket {
    const NETWORK_ID = Info::CLIENTBOUND_MAP_ITEM_DATA_PACKET;
    const BITFLAG_TEXTURE_UPDATE = 0x02;
	const BITFLAG_DECORATION_UPDATE = 0x04;
	public $mapId;
	public $type;
	public $scale = 0;
	public $decorations = []; 
	public $width = 128;
	public $height = 128;
	public $xOffset = 0;
	public $yOffset = 0;
	public $colors;
	public $isColorArray = true;
    public function decode() {
    }
    public function encode() {
        $this->reset();
        $this->putLong($this->mapId);
		$type = 0x00;
		if(count($this->colors) > 0){
			$type |= self::BITFLAG_TEXTURE_UPDATE;
		}
		$this->putInt($type);
		if(($type & self::BITFLAG_TEXTURE_UPDATE) !== 0) {
			$this->putByte($this->scale);
			$this->putInt($this->width);
			$this->putInt($this->height);
			$this->putInt($this->xOffset);
			$this->putInt($this->yOffset);
			if($this->isColorArray) {
				for($y = 0; $y < $this->height; ++$y){
					for($x = 0; $x < $this->width; ++$x) {
						$color = $this->colors[$y][$x];
						$this->putByte($color->getR());
						$this->putByte($color->getG());
						$this->putByte($color->getB());
						$this->putByte($color->getA());
					}
				}
			} else {
				$this->put($this->colors);
			}
		}
    }
}