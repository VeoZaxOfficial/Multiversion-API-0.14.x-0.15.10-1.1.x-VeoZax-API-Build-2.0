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
namespace pocketmine\level\generator;
use pocketmine\level\ChunkManager;use pocketmine\level\generator\noise\Noise;use pocketmine\math\Vector3;use pocketmine\utils\Random;use pocketmine\utils\Utils;use function preg_match;
abstract class Generator{
	public static function convertSeed(string $seed) : ?int{
		if($seed === ""){ 
			$convertedSeed = null;
		}elseif(preg_match('/^-?\d+$/', $seed) === 1){ 
			$convertedSeed = (int) $seed;
		}else{
			$convertedSeed = Utils::javaStringHash($seed);
		}
		return $convertedSeed;
	}
	protected $level;
	protected $random;
	abstract public function __construct(array $settings = []);
	public function init(ChunkManager $level, Random $random) : void{
		$this->level = $level;
		$this->random = $random;
	}
	abstract public function generateChunk(int $chunkX, int $chunkZ) : void;
	abstract public function populateChunk(int $chunkX, int $chunkZ) : void;
	abstract public function getSettings() : array;
	abstract public function getName() : string;
	abstract public function getSpawn() : Vector3;
	public function getWaterHeight() : int{
		return 0;
	}
	public static function getFastNoise1D(Noise $noise, $xSize, $samplingRate, $x, $y, $z){
		if($samplingRate === 0){
			throw new \InvalidArgumentException("samplingRate cannot be 0");
		}
		if($xSize % $samplingRate !== 0){
			throw new \InvalidArgumentException("xSize % samplingRate must return 0");
		}
		$noiseArray = new \SplFixedArray($xSize + 1);
		for($xx = 0; $xx <= $xSize; $xx += $samplingRate){
			$noiseArray[$xx] = $noise->noise3D($xx + $x, $y, $z);
		}
		for($xx = 0; $xx < $xSize; ++$xx){
			if($xx % $samplingRate !== 0){
				$nx = (int) ($xx / $samplingRate) * $samplingRate;
				$noiseArray[$xx] = Noise::linearLerp($xx, $nx, $nx + $samplingRate, $noiseArray[$nx], $noiseArray[$nx + $samplingRate]);
			}
		}
		return $noiseArray;
	}
	public static function getFastNoise2D(Noise $noise, $xSize, $zSize, $samplingRate, $x, $y, $z){
		if($samplingRate === 0){
			throw new \InvalidArgumentException("samplingRate cannot be 0");
		}
		if($xSize % $samplingRate !== 0){
			throw new \InvalidArgumentException("xSize % samplingRate must return 0");
		}
		if($zSize % $samplingRate !== 0){
			throw new \InvalidArgumentException("zSize % samplingRate must return 0");
		}
		$noiseArray = new \SplFixedArray($xSize + 1);
		for($xx = 0; $xx <= $xSize; $xx += $samplingRate){
			$noiseArray[$xx] = new \SplFixedArray($zSize + 1);
			for($zz = 0; $zz <= $zSize; $zz += $samplingRate){
				$noiseArray[$xx][$zz] = $noise->noise3D($x + $xx, $y, $z + $zz);
			}
		}
		for($xx = 0; $xx < $xSize; ++$xx){
			if($xx % $samplingRate !== 0){
				$noiseArray[$xx] = new \SplFixedArray($zSize + 1);
			}
			for($zz = 0; $zz < $zSize; ++$zz){
				if($xx % $samplingRate !== 0 or $zz % $samplingRate !== 0){
					$nx = (int) ($xx / $samplingRate) * $samplingRate;
					$nz = (int) ($zz / $samplingRate) * $samplingRate;
					$noiseArray[$xx][$zz] = Noise::bilinearLerp(
						$xx, $zz, $noiseArray[$nx][$nz], $noiseArray[$nx][$nz + $samplingRate],
						$noiseArray[$nx + $samplingRate][$nz], $noiseArray[$nx + $samplingRate][$nz + $samplingRate],
						$nx, $nx + $samplingRate, $nz, $nz + $samplingRate
					);
				}
			}
		}
		return $noiseArray;
	}
	public static function getFastNoise3D(Noise $noise, $xSize, $ySize, $zSize, $xSamplingRate, $ySamplingRate, $zSamplingRate, $x, $y, $z){
		if($xSamplingRate === 0){
			throw new \InvalidArgumentException("xSamplingRate cannot be 0");
		}
		if($zSamplingRate === 0){
			throw new \InvalidArgumentException("zSamplingRate cannot be 0");
		}
		if($ySamplingRate === 0){
			throw new \InvalidArgumentException("ySamplingRate cannot be 0");
		}
		if($xSize % $xSamplingRate !== 0){
			throw new \InvalidArgumentException("xSize % xSamplingRate must return 0");
		}
		if($zSize % $zSamplingRate !== 0){
			throw new \InvalidArgumentException("zSize % zSamplingRate must return 0");
		}
		if($ySize % $ySamplingRate !== 0){
			throw new \InvalidArgumentException("ySize % ySamplingRate must return 0");
		}
		$noiseArray = array_fill(0, $xSize + 1, array_fill(0, $zSize + 1, []));
		for($xx = 0; $xx <= $xSize; $xx += $xSamplingRate){
			for($zz = 0; $zz <= $zSize; $zz += $zSamplingRate){
				for($yy = 0; $yy <= $ySize; $yy += $ySamplingRate){
					$noiseArray[$xx][$zz][$yy] = $noise->noise3D($x + $xx, $y + $yy, $z + $zz, true);
				}
			}
		}
		for($xx = 0; $xx < $xSize; ++$xx){
			for($zz = 0; $zz < $zSize; ++$zz){
				for($yy = 0; $yy < $ySize; ++$yy){
					if($xx % $xSamplingRate !== 0 or $zz % $zSamplingRate !== 0 or $yy % $ySamplingRate !== 0){
						$nx = (int) ($xx / $xSamplingRate) * $xSamplingRate;
						$ny = (int) ($yy / $ySamplingRate) * $ySamplingRate;
						$nz = (int) ($zz / $zSamplingRate) * $zSamplingRate;
						$nnx = $nx + $xSamplingRate;
						$nny = $ny + $ySamplingRate;
						$nnz = $nz + $zSamplingRate;
						$dx1 = (($nnx - $xx) / ($nnx - $nx));
						$dx2 = (($xx - $nx) / ($nnx - $nx));
						$dy1 = (($nny - $yy) / ($nny - $ny));
						$dy2 = (($yy - $ny) / ($nny - $ny));
						$noiseArray[$xx][$zz][$yy] = (($nnz - $zz) / ($nnz - $nz)) * (
							$dy1 * (
								$dx1 * $noiseArray[$nx][$nz][$ny] + $dx2 * $noiseArray[$nnx][$nz][$ny]
							) + $dy2 * (
								$dx1 * $noiseArray[$nx][$nz][$nny] + $dx2 * $noiseArray[$nnx][$nz][$nny]
							)
						) + (($zz - $nz) / ($nnz - $nz)) * (
							$dy1 * (
								$dx1 * $noiseArray[$nx][$nnz][$ny] + $dx2 * $noiseArray[$nnx][$nnz][$ny]
							) + $dy2 * (
								$dx1 * $noiseArray[$nx][$nnz][$nny] + $dx2 * $noiseArray[$nnx][$nnz][$nny]
							)
						);
					}
				}
			}
		}
		return $noiseArray;
	}}