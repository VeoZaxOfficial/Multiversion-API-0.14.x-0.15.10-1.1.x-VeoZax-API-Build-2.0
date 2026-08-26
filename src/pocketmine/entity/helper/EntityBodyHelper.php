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
class EntityBodyHelper{
	protected $entity;
	protected $rotationTickCounter = 0;
	protected $prevRenderYawHead = 0.0;
	public function __construct(Mob $mob){
		$this->entity = $mob;
	}
	public function onUpdate() : void{
		if($this->entity->getMotion()->lengthSquared() > 0.0025){
			$this->entity->yawOffset = $this->entity->yaw;
			$this->entity->headYaw = $this->computeAngleWithBound($this->entity->yawOffset, $this->entity->headYaw ?? 0.0, 75);
			$this->prevRenderYawHead = $this->entity->headYaw ?? 0.0;
			$this->rotationTickCounter = 0;
		}else{
			$f = 75;
			if(abs(($this->entity->headYaw ?? 0.0) - $this->prevRenderYawHead) > 15){
				$this->rotationTickCounter = 0;
				$this->prevRenderYawHead = $this->entity->headYaw ?? 0.0;
			}else{
				$this->rotationTickCounter++;
				if($this->rotationTickCounter > 10){
					$f = max(1 - ($this->rotationTickCounter - 10) / 10, 0) * 75;
				}
			}
			$this->entity->yawOffset = $this->computeAngleWithBound($this->entity->headYaw ?? 0.0, $this->entity->yawOffset, $f);
		}
	}
	private function computeAngleWithBound(float $angle1, float $angle2, float $angleMax) : float{
		$f = EntityLookHelper::wrapAngleTo180($angle1 - $angle2);
		if($f < -$angleMax){
			$f = -$angleMax;
		}
		if($f >= $angleMax){
			$f = $angleMax;
		}
		return $angle1 - $f;
	}}