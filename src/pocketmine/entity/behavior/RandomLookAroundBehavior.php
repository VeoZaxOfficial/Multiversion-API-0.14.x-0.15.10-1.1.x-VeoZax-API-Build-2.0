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
use pocketmine\entity\Mob;use function cos;use function pi;use function sin;
class RandomLookAroundBehavior extends Behavior{
	protected $lookX = 0;
	protected $lookZ = 0;
	protected $idleTime = 0;
	public function __construct(Mob $mob){
		parent::__construct($mob);
		$this->mutexBits = 3;
	}
	public function canStart() : bool{
		return $this->random->nextFloat() < 0.02;
	}
	public function onStart() : void{
		$d0 = (pi() * 2) * $this->random->nextFloat();
		$this->lookX = cos($d0);
		$this->lookZ = sin($d0);
		$this->idleTime = 20 + $this->random->nextBoundedInt(20);
	}
	public function canContinue() : bool{
		return $this->idleTime > 0;
	}
	public function onTick() : void{
		$this->idleTime--;
		$this->mob->getLookHelper()->setLookPosition($this->mob->x + $this->lookX, $this->mob->y + $this->mob->getEyeHeight(), $this->mob->z + $this->lookZ, 10, $this->mob->getVerticalFaceSpeed());
	}}