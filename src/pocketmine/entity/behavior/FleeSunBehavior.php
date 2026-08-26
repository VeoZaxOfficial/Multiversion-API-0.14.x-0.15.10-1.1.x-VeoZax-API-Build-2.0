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
namespace pocketmine\entity\behavior;
use pocketmine\block\Block;use pocketmine\block\Water;use pocketmine\entity\Mob;use pocketmine\math\Vector3;
class FleeSunBehavior extends Behavior{
	protected $speedMultiplier = 1.0;
	protected $shelter;
	public function __construct(Mob $mob, float $speedMultiplier = 1.0){
		parent::__construct($mob);
		$this->speedMultiplier = $speedMultiplier;
		$this->mutexBits = 1;
	}
	public function canStart() : bool{
		if($this->mob->isOnFire() and $this->mob->level->isDayTime() and $this->mob->level->canSeeSky($this->mob->floor())){
			$this->shelter = $this->findPossibleShelter();
			return $this->shelter !== null;
		}
		return false;
	}
	public function onStart() : void{
		$this->mob->getNavigator()->tryMoveTo($this->shelter, $this->speedMultiplier);
	}
	public function canContinue() : bool{
		return $this->mob->getNavigator()->isBusy();
	}
	public function findPossibleShelter() : ?Block{
		for($i = 0; $i < 10; $i++){
			$block = $this->mob->level->getBlock($this->mob->add($this->random->nextBoundedInt(20) - 10, $this->random->nextBoundedInt(6) - 3, $this->random->nextBoundedInt(20) - 10));
			$canSeeSky = $this->mob->level->canSeeSky($block);
			if(!$block->isSolid() and ($block instanceof Water or !$canSeeSky)){
				return $block;
			}
		}
		return null;
	}}