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
use pocketmine\entity\Mob;use pocketmine\entity\utils\RandomPositionGenerator;use pocketmine\math\Vector3;
class RandomStrollBehavior extends Behavior{
	protected $speedMultiplier = 1.0;
	protected $chance = 120;
	protected $targetPos;
	public function __construct(Mob $mob, float $speedMultiplier = 1.0, int $chance = 120){
		parent::__construct($mob);
		$this->speedMultiplier = $speedMultiplier;
		$this->chance = $chance;
		$this->mutexBits = 1;
	}
	public function canStart() : bool{
		if($this->random->nextBoundedInt($this->chance) === 0){
			$pos = RandomPositionGenerator::findRandomTargetBlock($this->mob, 10, 7);
			if($pos === null) return false;
			$this->targetPos = $pos;
			return true;
		}
		return false;
	}
	public function canContinue() : bool{
		return $this->mob->getNavigator()->isBusy();
	}
	public function onStart() : void{
		$this->mob->getNavigator()->tryMoveTo($this->targetPos, $this->speedMultiplier);
	}
	public function onEnd() : void{
		$this->targetPos = null;
		$this->mob->getNavigator()->clearPath();
	}}