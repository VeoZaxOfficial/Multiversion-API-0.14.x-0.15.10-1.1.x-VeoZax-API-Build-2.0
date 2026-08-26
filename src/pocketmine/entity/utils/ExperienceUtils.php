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
use InvalidArgumentException;use pocketmine\math\Math;use pocketmine\utils\AssumptionFailedError;use function count;use function max;
abstract class ExperienceUtils{
	public static function getXpToReachLevel(int $level) : int{
		if($level <= 16){
			return $level ** 2 + $level * 6;
		}elseif($level <= 31){
			return (int) ($level ** 2 * 2.5 - 40.5 * $level + 360);
		}
		return (int) ($level ** 2 * 4.5 - 162.5 * $level + 2220);
	}
	public static function getXpToCompleteLevel(int $level) : int{
		if($level <= 15){
			return 2 * $level + 7;
		}elseif($level <= 30){
			return 5 * $level - 38;
		}else{
			return 9 * $level - 158;
		}
	}
	public static function getLevelFromXp(int $xp) : float{
		if($xp < 0){
			throw new InvalidArgumentException("XP must be at least 0");
		}
		if($xp <= self::getXpToReachLevel(16)){
			$a = 1;
			$b = 6;
			$c = 0;
		}elseif($xp <= self::getXpToReachLevel(31)){
			$a = 2.5;
			$b = -40.5;
			$c = 360;
		}else{
			$a = 4.5;
			$b = -162.5;
			$c = 2220;
		}
		$x = Math::solveQuadratic($a, $b, $c - $xp);
		if(count($x) === 0){
			throw new AssumptionFailedError("Expected at least 1 solution");
		}
		return max($x); 
	}}