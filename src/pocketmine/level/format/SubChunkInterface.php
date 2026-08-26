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
use pocketmine\world\format\PalettedBlockArray;
interface SubChunkInterface{
	public function isEmpty(bool $checkLight = true) : bool;
	public function getEmptyBlockId() : int;
	public function getBlockId(int $x, int $y, int $z) : int;
	public function setBlockId(int $x, int $y, int $z, int $id) : bool;
	public function getBlockData(int $x, int $y, int $z) : int;
	public function setBlockData(int $x, int $y, int $z, int $data) : bool;
	public function getFullBlock(int $x, int $y, int $z) : int;
	public function setFullBlock(int $x, int $y, int $z, int $block) : void;
	public function setBlock(int $x, int $y, int $z, ?int $id = null, ?int $data = null) : bool;
	public function getBlockLight(int $x, int $y, int $z) : int;
	public function setBlockLight(int $x, int $y, int $z, int $level) : bool;
	public function getBlockSkyLight(int $x, int $y, int $z) : int;
	public function setBlockSkyLight(int $x, int $y, int $z, int $level) : bool;
	public function getHighestBlockAt(int $x, int $z) : int;
	public function getBlockLightColumn(int $x, int $z) : string;
	public function getBlockSkyLightColumn(int $x, int $z) : string;
	public function getBlockLayers() : array;
	public function getBlockSkyLightArray() : string;
	public function setBlockSkyLightArray(string $data);
	public function getBlockLightArray() : string;
	public function setBlockLightArray(string $data);}