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
class BehaviorPool{
	protected $behaviorEntries = [];
	protected $workingBehaviors = [];
	protected $tickRate = 3;
	protected $tickCounter = 0;
	public function setBehavior(int $priority, Behavior $behavior) : void{
		$this->behaviorEntries[spl_object_id($behavior)] = new BehaviorEntry($priority, $behavior);
	}
	public function removeBehavior(Behavior $behavior) : void{
		unset($this->behaviorEntries[spl_object_id($behavior)]);
	}
	public function onUpdate() : bool{
		if($this->tickCounter++ % $this->tickRate === 0){
			foreach($this->behaviorEntries as $id => $entry){
				$behavior = $entry->getBehavior();
				if(isset($this->workingBehaviors[$id])){
					if(!$this->canUse($entry) or !$behavior->canContinue()){
						$behavior->onEnd();
						unset($this->workingBehaviors[$id]);
					}
				}
				if($this->canUse($entry) and $behavior->canStart()){
					$behavior->onStart();
					$this->workingBehaviors[$id] = $entry;
				}
			}
		}else{
			foreach($this->workingBehaviors as $id => $entry){
				if(!$entry->getBehavior()->canContinue()){
					$entry->getBehavior()->onEnd();
					unset($this->workingBehaviors[$id]);
				}
			}
		}
		foreach($this->workingBehaviors as $entry){
			$entry->getBehavior()->onTick();
		}
		return count($this->workingBehaviors) > 0;
	}
	public function canUse(BehaviorEntry $entry) : bool{
		foreach($this->behaviorEntries as $id => $behaviorEntry){
			if($behaviorEntry->getBehavior() !== $entry->getBehavior()){
				if($entry->getPriority() >= $behaviorEntry->getPriority()){
					if(!$this->theyCanWorkCompatible($entry->getBehavior(), $behaviorEntry->getBehavior()) and isset($this->workingBehaviors[$id])){
						return false;
					}
				}elseif(!$behaviorEntry->getBehavior()->isMutable() and isset($this->workingBehaviors[$id])){
					return false;
				}
			}
		}
		return true;
	}
	public function theyCanWorkCompatible(Behavior $b1, Behavior $b2) : bool{
		return ($b1->getMutexBits() & $b2->getMutexBits()) === 0;
	}
	public function getTickRate() : int{
		return $this->tickRate;
	}
	public function setTickRate(int $tickRate) : void{
		$this->tickRate = $tickRate;
	}
	public function getBehaviorEntries() : array{
		return $this->behaviorEntries;
	}
	public function clearBehaviors() : void{
		$this->behaviorEntries = [];
		$this->workingBehaviors = [];
	}}