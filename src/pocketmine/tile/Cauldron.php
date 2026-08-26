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
use pocketmine\nbt\tag\ByteTag;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\ShortTag;use pocketmine\utils\Binary;use pocketmine\utils\Color;
class Cauldron extends Spawnable{
	protected $potionId = -1;
	protected $splashPotion = false;
	protected $customColor;
	public function getName() : string{
		return "Cauldron";
	}
	protected function readSaveData(CompoundTag $nbt) : void{
		if($nbt->hasTag("PotionId", ShortTag::class)){
	    	$this->potionId = $nbt->getShort("PotionId", -1);
		}
		if($nbt->hasTag("SplashPotion", ByteTag::class)){
	    	$this->splashPotion = boolval($nbt->getByte("SplashPotion", 0));
		}
		if($nbt->hasTag("CustomColor", IntTag::class)){
			$this->customColor = Color::fromARGB(Binary::unsignInt($nbt->getInt("CustomColor")));
		}
	}
	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setShort("PotionId", $this->potionId);
		$nbt->setByte("SplashPotion", intval($this->splashPotion));
		if($this->customColor !== null){
			$nbt->setInt("CustomColor", Binary::signInt($this->customColor->toARGB()));
		}
	}
	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		$this->writeSaveData($nbt);
	}
	public function getCustomColor() : ?Color{
		return $this->customColor;
	}
	public function setCustomColor(?Color $customColor) : void{
		$this->customColor = $customColor;
		$this->onChanged();
	}
	public function getPotionId() : int{
		return $this->potionId;
	}
	public function setPotionId(int $potionId) : void{
		$this->potionId = $potionId;
		$this->onChanged();
	}
	public function hasPotion() : bool{
		return $this->potionId !== -1;
	}
	public function isSplashPotion() : bool{
		return $this->splashPotion;
	}
	public function setSplashPotion(bool $splashPotion) : void{
		$this->splashPotion = $splashPotion;
		$this->onChanged();
	}}