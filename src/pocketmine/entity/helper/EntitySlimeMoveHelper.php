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
namespace pocketmine\entity\helper;
use pocketmine\entity\hostile\Slime;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
class EntitySlimeMoveHelper extends EntityMoveHelper{
	protected $entity;
	protected $targetYaw = 0;
	protected $jumpTimer = 0;
	protected $speedJump = false;
	public function __construct(Slime $slime){
		parent::__construct($slime);
	}
	public function jumpWithYaw(float $yaw, bool $speedJump){
		$this->targetYaw = $yaw;
		$this->speedJump = $speedJump;
	}
	public function setSpeed(float $speedIn){
		$this->speedMultiplier = $speedIn;
		$this->needsUpdate = true;
	}
	public function onUpdate() : void{
		$this->entity->yaw = EntityLookHelper::limitAngle($this->entity->yaw, $this->targetYaw, 30.0);
		$this->entity->headYaw = $this->entity->yaw;
		$this->entity->yawOffset = $this->entity->yaw;
		if(!$this->needsUpdate){
			$this->entity->setMoveForward(0);
		}else{
			$this->needsUpdate = false;
			if($this->entity->onGround){
				$this->entity->setAIMoveSpeed($s = $this->speedMultiplier * $this->entity->getMovementSpeed());
				$this->entity->setMoveForward($s);
				if($this->jumpTimer-- <= 0){
					$this->jumpTimer = $this->entity->getJumpDelay();
					if($this->speedJump){
						$this->jumpTimer /= 3;
					}
					$this->entity->getJumpHelper()->setJumping(true);
					if($this->entity->makesSoundOnJump()){
						$this->entity->level->broadcastLevelSoundEvent($this->entity, $this->entity->getSlimeSize() > 1 ? LevelSoundEventPacket::SOUND_SQUISH_BIG : LevelSoundEventPacket::SOUND_SQUISH_SMALL, -1, $this->entity::NETWORK_ID);
					}
				}else{
					$this->entity->setMoveStrafing(0);
					$this->entity->setMoveForward(0);
					$this->entity->setAIMoveSpeed(0);
				}
			}else{
				$this->entity->setAIMoveSpeed($s = $this->speedMultiplier * $this->entity->getMovementSpeed());
				$this->entity->setMoveForward($s);
			}
		}
	}}