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
use pocketmine\entity\Entity;use pocketmine\entity\Mob;use pocketmine\Player;
class LookAtEntityBehavior extends Behavior{
	protected $lookDistance = 6.0;
	protected $nearestEntity;
	protected $lookTime = 0;
	protected $targetClass;
	public function __construct(Mob $mob, string $targetClass, float $lookDistance = 6.0){
		parent::__construct($mob);
		$this->lookDistance = $lookDistance;
		$this->targetClass = $targetClass;
		$this->mutexBits = 2;
	}
	public function canStart() : bool{
		if($this->random->nextFloat() < 0.02){
			$target = $this->mob->level->getNearestEntity($this->mob->asVector3(), $this->lookDistance, $this->targetClass);
			if($target !== null){
				if($target instanceof Player){
					if($target->isSpectator()){
						return false;
					}
				}
				$this->nearestEntity = $target;
				return true;
			}
		}
		return false;
	}
	public function onStart() : void{
		$this->lookTime = 40 + $this->random->nextBoundedInt(40);
	}
	public function canContinue() : bool{
		return (!$this->nearestEntity->isAlive() ? false : (($this->nearestEntity instanceof Player && $this->nearestEntity->isSpectator()) ? false : (($this->mob->distanceSquared($this->nearestEntity) > $this->lookDistance ** 2) ? false : ($this->lookTime > 0))));
	}
	public function onTick() : void{
		$this->mob->getLookHelper()->setLookPositionWithEntity($this->nearestEntity, 10, $this->mob->getVerticalFaceSpeed());
		$this->lookTime--;
	}
	public function onEnd() : void{
		$this->nearestEntity = null;
	}}