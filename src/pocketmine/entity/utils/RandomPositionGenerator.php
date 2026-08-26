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
namespace pocketmine\entity\utils;
use pocketmine\entity\Mob;use pocketmine\math\Vector3;
class RandomPositionGenerator{
	public static function findRandomTargetBlockAwayFrom(Mob $entity, int $xz, int $y, Vector3 $targetPos) : ?Vector3{
		return self::findRandomTargetBlock($entity, $xz, $y, $entity->subtract($targetPos));
	}
	public static function findRandomTargetBlock(Mob $entity, int $dxz, int $dy, ?Vector3 $targetPos = null) : ?Vector3{
		$currentWeight = PHP_INT_MIN;
		$currentPos = null;
		for($i = 0; $i < 10; $i++){
			$x = $entity->random->nextBoundedInt(2 * $dxz + 1) - $dxz;
			$y = $entity->random->nextBoundedInt(2 * $dy + 1) - $dy;
			$z = $entity->random->nextBoundedInt(2 * $dxz + 1) - $dxz;
			if($targetPos === null or ($x * $targetPos->x + $z * $targetPos->z) > 0){
				$targetVector = $entity->asVector3()->add($x, $y, $z);
				if(($maxY = $entity->level->getHeightMap($targetVector->getFloorX(), $targetVector->getFloorZ()) + 1) < $targetVector->y){
					$targetVector->y = $maxY;
				}
				$weight = $entity->getBlockPathWeight($targetVector);
				if($weight > $currentWeight){
					$currentWeight = $weight;
					$currentPos = $targetVector;
				}
			}
		}
		return $currentPos;
	}}