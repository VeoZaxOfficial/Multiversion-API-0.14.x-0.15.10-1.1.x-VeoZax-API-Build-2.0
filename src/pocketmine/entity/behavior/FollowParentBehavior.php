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
use pocketmine\entity\Animal;
class FollowParentBehavior extends Behavior{
	protected $speedMultiplier;
	protected $delay = 0;
	protected $parentAnimal;
	public function __construct(Animal $mob, float $speedMultiplier){
		parent::__construct($mob);
		$this->speedMultiplier = $speedMultiplier;
	}
	public function canStart() : bool{
		if($this->mob->isBaby()){
			$dist = 9;
			$animal = null;
			foreach($this->mob->level->getEntities() as $entity){
				if($entity !== $this->mob){
					if(!$entity->isBaby()){
						if(($d2 = $entity->distanceSquared($this->mob)) < $dist){
							$dist = $d2;
							$animal = $entity;
						}
					}
				}
			}
			if($animal instanceof Animal){
				if($dist >= 9){
					$this->parentAnimal = $animal;
					return true;
				}
			}
		}
		return false;
	}
	public function canContinue() : bool{
		$d = $this->mob->distanceSquared($this->parentAnimal);
		return $this->mob->isBaby() and $this->parentAnimal->isAlive() and $d >= 9 and $d <= 256;
	}
	public function onStart() : void{
		$this->delay = 0;
	}
	public function onTick() : void{
		if($this->delay-- <= 0){
			$this->delay = 10;
			$this->mob->getNavigator()->tryMoveTo($this->parentAnimal, $this->speedMultiplier);
		}
	}
	public function onEnd() : void{
		$this->parentAnimal = null;
	}}