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
use pocketmine\entity\Mob;
class EntityMoveHelper{
	protected $targetX = 0, $targetY = 0, $targetZ = 0;
	protected $needsUpdate = false;
	protected $speedMultiplier = 1.0;
	protected $entity;
	public function __construct(Mob $mob){
		$this->entity = $mob;
		$this->targetX = $mob->getFloorX();
		$this->targetY = $mob->getFloorY();
		$this->targetZ = $mob->getFloorZ();
	}
	public function moveTo(float $x, float $y, float $z, float $speedMultiplier) : void{
		$this->targetX = $x;
		$this->targetY = $y;
		$this->targetZ = $z;
		$this->speedMultiplier = $speedMultiplier;
		$this->needsUpdate = true;
	}
	public function onUpdate() : void{
		$this->entity->setMoveForward(0.0);
		if($this->needsUpdate){
			$this->needsUpdate = false;
			$i = floor($this->entity->y + 0.5);
			$d0 = $this->targetX - $this->entity->x;
			$d1 = $this->targetZ - $this->entity->z;
			$d2 = $this->targetY - $i;
			$d3 = $d0 * $d0 + $d2 * $d2 + $d1 * $d1;
			if($d3 >= 0){
				$f = atan2($d1, $d0) * 180 / pi() - 90;
				$this->entity->yaw = EntityLookHelper::limitAngle($this->entity->yaw, $f, 30);
				$this->entity->setAIMoveSpeed($sf = $this->speedMultiplier * $this->entity->getMovementSpeed());
				$this->entity->setMoveForward($sf);
				if($d2 > 0 and ($d0 * $d0 + $d1 * $d1) < 1.0){
					$this->entity->getJumpHelper()->setJumping(true);
				}
			}
		}
	}
	public function getSpeedMultiplier() : float{
		return $this->speedMultiplier;
	}
	public function getTargetX() : int{
		return $this->targetX;
	}
	public function getTargetY() : int{
		return $this->targetY;
	}
	public function getTargetZ() : int{
		return $this->targetZ;
	}}