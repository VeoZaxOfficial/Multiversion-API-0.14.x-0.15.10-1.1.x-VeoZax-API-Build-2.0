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
namespace pocketmine\nbt;
use pocketmine\utils\Binary;use function count;use function strlen;
class NetworkLittleEndianNBTStream extends LittleEndianNBTStream{
	public function getInt() : int{
		return Binary::readVarInt($this->buffer, $this->offset);
	}
	public function putInt(int $v) : void{
		($this->buffer .= Binary::writeVarInt($v));
	}
	public function getLong() : int{
		return Binary::readVarLong($this->buffer, $this->offset);
	}
	public function putLong(int $v) : void{
		($this->buffer .= Binary::writeVarLong($v));
	}
	public function getString() : string{
		return $this->get(self::checkReadStringLength(Binary::readUnsignedVarInt($this->buffer, $this->offset)));
	}
	public function putString(string $v) : void{
		($this->buffer .= Binary::writeUnsignedVarInt(self::checkWriteStringLength(strlen($v))) . $v);
	}
	public function getIntArray() : array{
		$len = $this->getInt(); 
		$ret = [];
		for($i = 0; $i < $len; ++$i){
			$ret[] = $this->getInt(); 
		}
		return $ret;
	}
	public function putIntArray(array $array) : void{
		$this->putInt(count($array)); 
		foreach($array as $v){
			$this->putInt($v); 
		}
	}}