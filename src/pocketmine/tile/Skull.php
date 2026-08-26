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
namespace pocketmine\tile;
use pocketmine\item\Item;use pocketmine\math\Vector3;use pocketmine\nbt\tag\CompoundTag;use pocketmine\Player;use function floor;
class Skull extends Spawnable{
	public const TYPE_SKELETON = 0;
	public const TYPE_WITHER = 1;
	public const TYPE_ZOMBIE = 2;
	public const TYPE_HUMAN = 3;
	public const TYPE_CREEPER = 4;
	public const TYPE_DRAGON = 5;
	public const TAG_SKULL_TYPE = "SkullType"; 
	public const TAG_ROT = "Rot"; 
	public const TAG_MOUTH_MOVING = "MouthMoving"; 
	public const TAG_MOUTH_TICK_COUNT = "MouthTickCount"; 
	private $skullType;
	private $skullRotation;
	protected function readSaveData(CompoundTag $nbt) : void{
		$this->skullType = $nbt->getByte(self::TAG_SKULL_TYPE, self::TYPE_SKELETON, true);
		$this->skullRotation = $nbt->getByte(self::TAG_ROT, 0, true);
	}
	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setByte(self::TAG_SKULL_TYPE, $this->skullType);
		$nbt->setByte(self::TAG_ROT, $this->skullRotation);
	}
	public function setType(int $type){
		$this->skullType = $type;
		$this->onChanged();
	}
	public function getType() : int{
		return $this->skullType;
	}
	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		$nbt->setByte(self::TAG_SKULL_TYPE, $this->skullType);
		$nbt->setByte(self::TAG_ROT, $this->skullRotation);
	}
	protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, ?int $face = null, ?Item $item = null, ?Player $player = null) : void{
		$nbt->setByte(self::TAG_SKULL_TYPE, $item !== null ? $item->getDamage() : self::TYPE_SKELETON);
		$rot = 0;
		if($face === Vector3::SIDE_UP and $player !== null){
			$rot = floor(($player->yaw * 16 / 360) + 0.5) & 0x0F;
		}
		$nbt->setByte(self::TAG_ROT, $rot);
	}}