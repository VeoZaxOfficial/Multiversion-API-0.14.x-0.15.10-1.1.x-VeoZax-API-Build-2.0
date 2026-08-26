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
use pocketmine\entity\Entity;use pocketmine\entity\Mob;use pocketmine\entity\RangedAttackerMob;use pocketmine\event\entity\EntityDamageByEntityEvent;use pocketmine\event\entity\EntityDamageEvent;use function floor;use function sqrt;
class RangedAttackBehavior extends Behavior{
	protected $speedMultiplier = 1.0;
	protected $minAttackTime, $maxAttackTime;
	protected $maxAttackDistanceIn, $maxAttackDistance;
	protected $rangedAttackTime = 0;
	protected $targetSeenTicks = 0;
	protected $attackTarget;
	public function __construct(Mob $mob, float $speedMultiplier, int $minAttackTime, int $maxAttackTime, float $maxAttackDistanceIn){
		parent::__construct($mob);
		$this->speedMultiplier = $speedMultiplier;
		$this->minAttackTime = $minAttackTime;
		$this->maxAttackTime = $maxAttackTime;
		$this->maxAttackDistanceIn = $maxAttackDistanceIn;
		$this->maxAttackDistance = $maxAttackDistanceIn ** 2;
		$this->rangedAttackTime = -1;
		$this->mutexBits = 3;
	}
	public function canStart() : bool{
		if(($target = $this->mob->getTargetEntity()) !== null){
			$this->attackTarget = $target;
			return true;
		}
		return false;
	}
	public function canContinue() : bool{
		return $this->canStart() or $this->mob->getNavigator()->isBusy();
	}
	public function onEnd() : void{
		$this->attackTarget = null;
		$this->targetSeenTicks = 0;
		$this->rangedAttackTime = -1;
	}
	public function onTick() : void{
		if(!$this->mob->isAlive()){
			return;
		}
		$dist = $this->mob->distanceSquared($this->attackTarget);
		if($flag = $this->mob->canSeeEntity($this->attackTarget)){
			++$this->targetSeenTicks;
		}else{
			$this->targetSeenTicks = 0;
		}
		if($dist <= $this->maxAttackDistance and $this->targetSeenTicks >= 20){
			$this->mob->getNavigator()->clearPath();
		}else{
			$this->mob->getNavigator()->tryMoveTo($this->attackTarget, $this->speedMultiplier);
		}
		$this->mob->getLookHelper()->setLookPositionWithEntity($this->attackTarget, 30, 30);
		if(--$this->rangedAttackTime <= 0){
			if($dist > $this->maxAttackDistance or !$flag){
				return;
			}
			$f = sqrt($dist) / $this->maxAttackDistanceIn;
			if($f > 1) $f = 1;
			if($f < 0.1) $f = 0.1;
			if($this->mob instanceof RangedAttackerMob){
				$this->mob->onRangedAttackToTarget($this->attackTarget, $f);
			}
			if($dist < 1){
				$this->attackTarget->attack(new EntityDamageByEntityEvent($this->mob, $this->attackTarget, EntityDamageEvent::CAUSE_CUSTOM, $this->mob->getAttackDamage()));
			}
			$this->rangedAttackTime = floor($f * ($this->maxAttackTime - $this->minAttackTime) + $this->minAttackTime);
		}
	}}