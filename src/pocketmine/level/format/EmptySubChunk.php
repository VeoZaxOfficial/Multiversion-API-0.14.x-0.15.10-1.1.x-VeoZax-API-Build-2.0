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
namespace pocketmine\level\format;
use pocketmine\block\Block;use function str_repeat;
class EmptySubChunk implements SubChunkInterface{
	private static $instance;
	public static function getInstance() : self{
		if(self::$instance === null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function isEmpty(bool $checkLight = true) : bool{
		return true;
	}
	public function getEmptyBlockId() : int{
		return Block::AIR;
	}
	public function getBlockId(int $x, int $y, int $z) : int{
		return 0;
	}
	public function setBlockId(int $x, int $y, int $z, int $id) : bool{
		return false;
	}
	public function getBlockData(int $x, int $y, int $z) : int{
		return 0;
	}
	public function setBlockData(int $x, int $y, int $z, int $data) : bool{
		return false;
	}
	public function getFullBlock(int $x, int $y, int $z) : int{
		return 0;
	}
	public function setFullBlock(int $x, int $y, int $z, int $block) : void{
	}
	public function setBlock(int $x, int $y, int $z, ?int $id = null, ?int $data = null) : bool{
		return false;
	}
	public function getBlockLight(int $x, int $y, int $z) : int{
		return 0;
	}
	public function setBlockLight(int $x, int $y, int $z, int $level) : bool{
		return false;
	}
	public function getBlockSkyLight(int $x, int $y, int $z) : int{
		return 15;
	}
	public function setBlockSkyLight(int $x, int $y, int $z, int $level) : bool{
		return false;
	}
	public function getHighestBlockAt(int $x, int $z) : int{
		return -1;
	}
	public function getBlockLightColumn(int $x, int $z) : string{
		return "\x00\x00\x00\x00\x00\x00\x00\x00";
	}
	public function getBlockSkyLightColumn(int $x, int $z) : string{
		return "\xff\xff\xff\xff\xff\xff\xff\xff";
	}
	public function getBlockLayers() : array{
		return [];
	}
	public function getBlockLightArray() : string{
		return str_repeat("\x00", 2048);
	}
	public function setBlockLightArray(string $data){
	}
	public function getBlockSkyLightArray() : string{
		return str_repeat("\xff", 2048);
	}
	public function setBlockSkyLightArray(string $data){
	}}