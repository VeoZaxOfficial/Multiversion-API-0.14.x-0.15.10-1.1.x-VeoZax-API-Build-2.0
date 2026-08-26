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
use pocketmine\entity\Mob;
class LeapAtTargetBehavior extends Behavior{
	protected $leapHeight;
	protected $mustBeOnGround;
	protected $leapTarget;
	public function __construct(Mob $mob, float $leapHeight, bool $mustBeOnGround = true){
		parent::__construct($mob);
		$this->leapHeight = $leapHeight;
		$this->mustBeOnGround = $mustBeOnGround;
		$this->mutexBits = 5;
	}
	public function canStart() : bool{
		$this->leapTarget = $this->mob->getTargetEntity();
		if($this->leapTarget == null) return false;
		$distance = $this->mob->distance($this->leapTarget);
		return $distance >= 4 and $distance <= 16 and ($this->mustBeOnGround ? $this->mob->isOnGround() : true) and $this->random->nextBoundedInt(5) == 0;
	}
	public function canContinue() : bool{
		return !$this->mob->onGround;
	}
	public function onStart() : void{
		$d1 = $this->leapTarget->x - $this->mob->x;
		$d2 = $this->leapTarget->z - $this->mob->z;
		$f = sqrt($d1 ** 2 + $d2 ** 2);
		$motion = $this->mob->getMotion();
		$motion->x += $d1 / $f * 0.5 * 0.8 + $motion->x * 0.2;
		$motion->y = $this->leapHeight;
		$motion->z += $d2 / $f * 0.5 * 0.8 + $motion->z * 0.2;
		$this->mob->setMotion($motion);
	}}