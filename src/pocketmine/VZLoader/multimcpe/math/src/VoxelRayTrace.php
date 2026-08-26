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
namespace pocketmine\math;
use Generator;use InvalidArgumentException;use function floor;use const INF;
abstract class VoxelRayTrace{
	public static function inDirection(Vector3 $start, Vector3 $directionVector, float $maxDistance) : Generator{
		return self::betweenPoints($start, $start->add($directionVector->multiply($maxDistance)));
	}
	public static function betweenPoints(Vector3 $start, Vector3 $end) : Generator{
		$currentBlock = $start->floor();
		$directionVector = $end->subtract($start)->normalize();
		if($directionVector->lengthSquared() <= 0){
			throw new InvalidArgumentException("Start and end points are the same, giving a zero direction vector");
		}
		$radius = $start->distance($end);
		$stepX = $directionVector->x <=> 0;
		$stepY = $directionVector->y <=> 0;
		$stepZ = $directionVector->z <=> 0;
		$tMaxX = self::rayTraceDistanceToBoundary($start->x, $directionVector->x);
		$tMaxY = self::rayTraceDistanceToBoundary($start->y, $directionVector->y);
		$tMaxZ = self::rayTraceDistanceToBoundary($start->z, $directionVector->z);
		$tDeltaX = $directionVector->x == 0 ? 0 : $stepX / $directionVector->x;
		$tDeltaY = $directionVector->y == 0 ? 0 : $stepY / $directionVector->y;
		$tDeltaZ = $directionVector->z == 0 ? 0 : $stepZ / $directionVector->z;
		while(true){
			yield $currentBlock;
			if($tMaxX < $tMaxY and $tMaxX < $tMaxZ){
				if($tMaxX > $radius){
					break;
				}
				$currentBlock->x += $stepX;
				$tMaxX += $tDeltaX;
			}elseif($tMaxY < $tMaxZ){
				if($tMaxY > $radius){
					break;
				}
				$currentBlock->y += $stepY;
				$tMaxY += $tDeltaY;
			}else{
				if($tMaxZ > $radius){
					break;
				}
				$currentBlock->z += $stepZ;
				$tMaxZ += $tDeltaZ;
			}
		}
	}
	private static function rayTraceDistanceToBoundary(float $s, float $ds) : float{
		if($ds == 0){
			return INF;
		}
		if($ds < 0){
			$s = -$s;
			$ds = -$ds;
			if(floor($s) == $s){ 
				return 0;
			}
		}
		return (1 - ($s - floor($s))) / $ds;
	}}