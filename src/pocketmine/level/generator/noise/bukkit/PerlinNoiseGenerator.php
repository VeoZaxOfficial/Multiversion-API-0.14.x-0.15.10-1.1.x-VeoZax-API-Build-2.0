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
namespace pocketmine\level\generator\noise\bukkit;
class PerlinNoiseGenerator extends BasePerlinNoiseGenerator{
	private static ?PerlinNoiseGenerator $instance;
	public static function getInstance() : PerlinNoiseGenerator{
		return self::$instance ??= new PerlinNoiseGenerator();
	}
	public static function getNoise3d(float $x, float $y = 0.0, float $z = 0.0) : float{
		return self::getInstance()->noise3d($x, $y, $z);
	}
	public static function getNoise(float $x, float $y, float $z, int $octaves, float $frequency, float $amplitude) : float{
		return self::getInstance()->noise($x, $y, $z, $octaves, $frequency, $amplitude);
	}
	public function noise3d(float $x, float $y = 0.0, float $z = 0.0) : float{
		$x += $this->offset_x;
		$y += $this->offset_y;
		$z += $this->offset_z;
		$floor_x = self::floor($x);
		$floor_y = self::floor($y);
		$floor_z = self::floor($z);
		$X = $floor_x & 255;
		$Y = $floor_y & 255;
		$Z = $floor_z & 255;
		$x -= $floor_x;
		$y -= $floor_y;
		$z -= $floor_z;
		$fX = self::fade($x);
		$fY = self::fade($y);
		$fZ = self::fade($z);
		$A = $this->perm[$X] + $Y;
		$AA = $this->perm[$A] + $Z;
		$AB = $this->perm[$A + 1] + $Z;
		$B = $this->perm[$X + 1] + $Y;
		$BA = $this->perm[$B] + $Z;
		$BB = $this->perm[$B + 1] + $Z;
		return self::lerp($fZ, self::lerp($fY, self::lerp($fX, self::grad($this->perm[$AA], $x, $y, $z),
			self::grad($this->perm[$BA], $x - 1, $y, $z)),
			self::lerp($fX, self::grad($this->perm[$AB], $x, $y - 1, $z),
				self::grad($this->perm[$BB], $x - 1, $y - 1, $z))),
			self::lerp($fY, self::lerp($fX, self::grad($this->perm[$AA + 1], $x, $y, $z - 1),
				self::grad($this->perm[$BA + 1], $x - 1, $y, $z - 1)),
				self::lerp($fX, self::grad($this->perm[$AB + 1], $x, $y - 1, $z - 1),
					self::grad($this->perm[$BB + 1], $x - 1, $y - 1, $z - 1))));
	}
	public function noise(float $x, float $y, float $z, int $octaves, float $frequency, float $amplitude, bool $normalized = false) : float{
		$result = 0.0;
		$amp = 1.0;
		$freq = 1.0;
		$max = 0.0;
		for($i = 0; $i < $octaves; ++$i){
			$result += $this->noise3d($x * $freq, $y * $freq, $z * $freq) * $amp;
			$max += $amp;
			$freq *= $frequency;
			$amp *= $amplitude;
		}
		if($normalized){
			$result /= $max;
		}
		return $result;
	}}