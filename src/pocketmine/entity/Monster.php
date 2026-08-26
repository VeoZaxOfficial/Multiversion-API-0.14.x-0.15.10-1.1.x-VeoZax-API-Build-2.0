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
namespace pocketmine\entity;
use pocketmine\level\Level;use pocketmine\math\Vector3;use function max;
abstract class Monster extends Mob{
	protected function isValidLightLevel() : bool{
		$x = $this->getFloorX();
		$y = $this->getFloorY();
		$z = $this->getFloorZ();
		if($this->level->getBlockLightAt($x, $y, $z) < $this->random->nextBoundedInt(32)){
			$i = max(
				$this->level->getRealBlockSkyLightAt($x, $y + 1, $z),
				$this->level->getRealBlockSkyLightAt($x, $y - 1, $z),
				$this->level->getRealBlockSkyLightAt($x, $y, $z + 1),
				$this->level->getRealBlockSkyLightAt($x, $y, $z - 1),
				$this->level->getRealBlockSkyLightAt($x + 1, $y, $z),
				$this->level->getRealBlockSkyLightAt($x - 1, $y, $z)
			);
			return $i <= $this->random->nextBoundedInt(8);
		}
		return false;
	}
	public function entityBaseTick(int $diff = 1) : bool{
		$hasUpdate = parent::entityBaseTick($diff);
		if($this->isAlive()){
			if($this->level->getDifficulty() === Level::DIFFICULTY_PEACEFUL){
				$this->flagForDespawn();
			}
		}
		return $hasUpdate;
	}
	public function canSpawnHere() : bool{
		return $this->level->getDifficulty() !== Level::DIFFICULTY_PEACEFUL and $this->isValidLightLevel();
	}
        public function canDespawn() : bool{
		return false;
        }
	public function getBlockPathWeight(Vector3 $pos) : float{
		return 0.5 - max(
				$this->level->getRealBlockSkyLightAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ()),
				$this->level->getBlockLightAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ())
			);
	}
}