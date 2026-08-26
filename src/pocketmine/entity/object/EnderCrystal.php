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
namespace pocketmine\entity\object;
use pocketmine\entity\Entity;use pocketmine\entity\Explosive;use pocketmine\event\entity\EntityDamageEvent;use pocketmine\event\entity\ExplosionPrimeEvent;use pocketmine\level\Explosion;use pocketmine\level\Position;
class EnderCrystal extends Entity implements Explosive{
    public const NETWORK_ID = self::ENDER_CRYSTAL;
	public const TAG_SHOWBASE = "ShowBottom"; 
	public $gravity = 0.0;
	public $drag = 1.0;
	public $height = 2.0;
	public $width = 2.0;
	public function showBase() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_SHOWBASE);
	}
	public function setShowBase(bool $showBase) : void{
		$this->setGenericFlag(self::DATA_FLAG_SHOWBASE, $showBase);
	}
	public function attack(EntityDamageEvent $source) : void{
		if(
			$source->getCause() !== EntityDamageEvent::CAUSE_FIRE &&
			$source->getCause() !== EntityDamageEvent::CAUSE_FIRE_TICK &&
		    $source->getCause() !== EntityDamageEvent::CAUSE_LAVA
		){
			parent::attack($source);
			if(!$this->isFlaggedForDespawn() && !$source->isCancelled()){
				$this->flagForDespawn();
				$this->explode();
			}
		}
	}
	public function isFireProof() : bool{
		return true;
	}
	public function hasMovementUpdate() : bool{
		return false;
	}
	protected function updateMovement(bool $teleport = false) : void{
	}
	public function canBeMovedByCurrents() : bool{
		return false;
	}
	public function canBeCollidedWith() : bool{
		return true;
	}
	public function setDataFlag(int $propertyId, int $flagId, bool $value = true, int $propertyType = self::DATA_TYPE_LONG) : void{
	    if($flagId === self::DATA_FLAG_ONFIRE){
	        return; 
	    }
	    parent::setDataFlag($propertyId, $flagId, $value, $propertyType);
	}
	public function setGenericFlag(int $flagId, bool $value = true) : void{
	    if($flagId === self::DATA_FLAG_ONFIRE){
	        return; 
	    }
	    parent::setGenericFlag($flagId, $value);
	}
	protected function initEntity() : void{
		parent::initEntity();
		$this->setMaxHealth(1);
		$this->setHealth(1);
		$this->setShowBase($this->namedtag->getByte(self::TAG_SHOWBASE, 0) === 1);
	}
	public function saveNBT() : void{
		parent::saveNBT();
		$this->namedtag->setByte(self::TAG_SHOWBASE, $this->showBase() ? 1 : 0);
	}
	public function explode() : void{
		$ev = new ExplosionPrimeEvent($this, 6);
		$ev->call();
		if(!$ev->isCancelled()){
			$explosion = new Explosion(Position::fromObject($this->add(0, 0.5, 0), $this->getLevelNonNull()), $ev->getForce(), $this);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}}