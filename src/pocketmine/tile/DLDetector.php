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
use pocketmine\block\BlockIds;use pocketmine\block\DaylightSensor;use pocketmine\level\Level;use pocketmine\nbt\tag\CompoundTag;
class DLDetector extends Spawnable{
	private $nbt;
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	}
	public function getLightByTime() : int{
		$time = $this->getLevelNonNull()->getTime();
		if(($time >= Level::TIME_DAY and $time <= Level::TIME_SUNSET) or
			($time >= Level::TIME_SUNRISE and $time <= Level::TIME_FULL)
		){
			return 15;
		}
		return 0;
	}
	public function isActivated() : bool{
		if($this->getType() === BlockIds::DAYLIGHT_SENSOR){
			if($this->getLightByTime() === 15){
				return true;
			}
			return false;
		}else{
			if($this->getLightByTime() === 0){
				return true;
			}
			return false;
		}
	}
	private function getType() : int{
		return $this->getBlock()->getId();
	}
	public function onUpdate() : bool{
		if($this->closed){
			return false;
		}
		$this->timings->startTiming();
		if(($this->getLevelNonNull()->getServer()->getTick() % 3) === 0){ 
			$block = $this->getBlock();
			if(!$this->isActivated()){
				$block->deactivate();
			}else{
				$block->activate();
			}
		}
		$this->timings->stopTiming();
		return true;
	}
	protected function readSaveData(CompoundTag $nbt) : void{
		$this->nbt = $nbt;
	}
	protected function writeSaveData(CompoundTag $nbt) : void{
	}
	public function addAdditionalSpawnData(CompoundTag $nbt) : void{
	}}