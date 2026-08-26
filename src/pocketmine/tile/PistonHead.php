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
use pocketmine\nbt\tag\CompoundTag;
class PistonHead extends Spawnable{
	public const TAG_PROGRESS = "Progress";
	public const TAG_STATE = "State";
	public const TAG_STICKY = "Sticky";
	protected $progress;
	protected $state;
	protected $sticky;
	public function getProgress() : float{
		return $this->progress;
	}
	public function setProgress(float $progress) : void{
		$this->progress = $progress;
	}
	public function getState() : int{
		return $this->state;
	}
	public function setState(int $state) : void{
		$this->state = $state;
	}
	public function getSticky() : int{
		return $this->sticky;
	}
	public function setSticky(int $sticky) : void{
		$this->sticky = $sticky;
	}
	protected function readSaveData(CompoundTag $nbt) : void{
		$this->progress = $nbt->getFloat(self::TAG_PROGRESS, 0.0, true);
		$this->state = $nbt->getByte(self::TAG_STATE, 0, true);
		$this->sticky = $nbt->getByte(self::TAG_STICKY, 0, true);
	}
	public function getDefaultName() : string{
		return "PistonHead";
	}
	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setFloat(self::TAG_PROGRESS, $this->progress, true);
		$nbt->setByte(self::TAG_STATE, $this->state, true);
		$nbt->setByte(self::TAG_STICKY, $this->sticky, true);
	}
	public function addAdditionalSpawnData(CompoundTag $nbt) : void{
		$nbt->setFloat(self::TAG_PROGRESS, $this->progress);
		$nbt->setByte(self::TAG_STATE, $this->state);
		$nbt->setByte(self::TAG_STICKY, $this->sticky);
	}}