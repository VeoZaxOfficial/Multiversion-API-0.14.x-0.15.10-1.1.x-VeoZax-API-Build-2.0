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
use pocketmine\entity\Entity;use pocketmine\entity\Tamable;use pocketmine\Player;
class FollowOwnerBehavior extends Behavior{
	protected $speedMultiplier;
	protected $followDelay = 0;
	protected $mob;
	protected $minDistance;
	protected $maxDistance;
	protected $owner;
	public function __construct(Tamable $mob, float $speedMultiplier, float $minDistance, float $maxDistance){
		parent::__construct($mob);
		$this->minDistance = $minDistance;
		$this->maxDistance = $maxDistance;
		$this->speedMultiplier = $speedMultiplier;
		$this->mutexBits = 3;
	}
	public function canStart() : bool{
		$owner = $this->mob->getOwningEntity();
		if($owner !== null and !($owner instanceof Player and $owner->isSpectator()) and !$this->mob->isSitting() and $this->mob->distanceSquared($owner) > ($this->minDistance ** 2)){
			$this->owner = $owner;
			return true;
		}
		return false;
	}
	public function onStart() : void{
		$this->followDelay = 0;
		$this->mob->getNavigator()->setAvoidsWater(false);
	}
	public function canContinue() : bool{
		return $this->mob->getNavigator()->isBusy() and $this->mob->distanceSquared($this->owner) > ($this->maxDistance ** 2) and !$this->mob->isSitting();
	}
	public function onTick() : void{
		$this->mob->getLookHelper()->setLookPositionWithEntity($this->owner, 10, $this->mob->getVerticalFaceSpeed());
		if(!$this->mob->isSitting()){
			if(--$this->followDelay <= 0){
				$this->followDelay = 10;
				$this->mob->getNavigator()->tryMoveTo($this->owner, $this->speedMultiplier);
				if(!$this->mob->isLeashed()){
					if($this->mob->distanceSquared($this->owner) > 144){
						$this->mob->teleport($this->owner);
						$this->mob->getNavigator()->clearPath(true);
					}
				}
			}
		}
	}
	public function onEnd() : void{
		$this->mob->getNavigator()->clearPath(true);
		$this->owner = null;
		$this->mob->getNavigator()->setAvoidsWater(true);
	}}