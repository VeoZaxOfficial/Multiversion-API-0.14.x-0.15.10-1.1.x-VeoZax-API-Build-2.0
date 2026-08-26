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
namespace pocketmine\level\generator\noise\glowstone;
use pocketmine\level\generator\noise\bukkit\NoiseGenerator;use pocketmine\level\generator\noise\bukkit\OctaveGenerator;use pocketmine\utils\Random;
class PerlinOctaveGenerator extends OctaveGenerator{
	protected static function createOctaves(Random $rand, int $octaves) : array{
		$result = [];
		for($i = 0; $i < $octaves; ++$i){
			$result[$i] = new PerlinNoise($rand);
		}
		return $result;
	}
	protected static function floor(float $x) : int{
		return $x >= 0 ? (int) $x : (int) $x - 1;
	}
	public static function fromRandomAndOctaves(Random $random, int $octaves, int $size_x, int $size_y, int $size_z) : self{
		return new PerlinOctaveGenerator(self::createOctaves($random, $octaves), $size_x, $size_y, $size_z);
	}
	protected int $size_x;
	protected int $size_y;
	protected int $size_z;
	protected array $noise;
	public function __construct(array $octaves, int $size_x, int $size_y, int $size_z){
		parent::__construct($octaves);
		$this->size_x = $size_x;
		$this->size_y = $size_y;
		$this->size_z = $size_z;
		$this->noise = array_fill(0, $size_x * $size_y * $size_z, 0.0);
	}
	public function getSizeX() : int{
		return $this->size_x;
	}
	public function getSizeY() : int{
		return $this->size_y;
	}
	public function getSizeZ() : int{
		return $this->size_z;
	}
	public function setSizeX(int $size_x) : void{
		$this->size_x = $size_x;
	}
	public function setSizeY(int $size_y) : void{
		$this->size_y = $size_y;
	}
	public function setSizeZ(int $size_z) : void{
		$this->size_z = $size_z;
	}
	public function getFractalBrownianMotion(float $x, float $y, float $z, float $lacunarity, float $persistence) : array{
		$this->noise = array_fill(0, $this->size_x * $this->size_y * $this->size_z, 0.0);
		$freq = 1;
		$amp = 1;
		$x *= $this->x_scale;
		$y *= $this->y_scale;
		$z *= $this->z_scale;
		foreach($this->octaves as $octave){
			$dx = $x * $freq;
			$dz = $z * $freq;
			$lx = self::floor($dx);
			$lz = self::floor($dz);
			$dx -= $lx;
			$dz -= $lz;
			$lx %= 16777216;
			$lz %= 16777216;
			$dx += $lx;
			$dz += $lz;
			$dy = $y * $freq;
			$this->noise = $octave->getNoise($this->noise, $dx, $dy, $dz, $this->size_x, $this->size_y, $this->size_z, $this->x_scale * $freq, $this->y_scale * $freq, $this->z_scale * $freq, $amp);
			$freq *= $lacunarity;
			$amp *= $persistence;
		}
		return $this->noise;
	}}