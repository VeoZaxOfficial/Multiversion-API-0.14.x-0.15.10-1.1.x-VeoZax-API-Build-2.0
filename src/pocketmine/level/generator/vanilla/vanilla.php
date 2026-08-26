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

namespace pocketmine\level\generator\vanilla;
use pocketmine\level\generator\noise\glowstone\PerlinOctaveGenerator;use pocketmine\level\generator\noise\glowstone\SimplexOctaveGenerator;
use pocketmine\block\Block;use pocketmine\block\CoalOre;use pocketmine\block\DiamondOre;use pocketmine\block\Dirt;use pocketmine\block\GoldOre;use pocketmine\block\Gravel;use pocketmine\block\IronOre;use pocketmine\block\LapisOre;use pocketmine\block\RedstoneOre;use pocketmine\block\Stone;
use pocketmine\level\generator\object\OreType;use pocketmine\level\generator\populator\GroundCover;use pocketmine\level\generator\populator\Cave;use pocketmine\level\generator\populator\Ore;use pocketmine\level\biome\Biome;use pocketmine\level\generator\biome\BiomeSelector;use pocketmine\level\generator\Generator;use pocketmine\level\ChunkManager;use pocketmine\level\Level;use pocketmine\math\Vector3 as Vector3;use pocketmine\utils\Random;
class vanilla extends Generator{
	const NAME = "vanilla";
	private $populators = [];
	private $waterHeight = 62;
	private $bedrockDepth = 5;
	private $generationPopulators = [];
	private $noiseBase;
	private $selector;
	private static $ELEVATION_WEIGHT = null;
	private static $GAUSSIAN_KERNEL = null;
	private static $SMOOTH_SIZE = 2;
	protected const COORDINATE_SCALE = 684.412;
	protected const HEIGHT_SCALE = 684.412;
	protected const HEIGHT_NOISE_SCALE_X = 200.0;
	protected const HEIGHT_NOISE_SCALE_Z = 200.0;
	protected const DETAIL_NOISE_SCALE_X = 80.0;
	protected const DETAIL_NOISE_SCALE_Y = 160.0;
	protected const DETAIL_NOISE_SCALE_Z = 80.0;
	protected const SURFACE_SCALE = 0.0625;
	protected const BASE_SIZE = 8.5;
	protected const STRETCH_Y = 12.0;
	protected const BIOME_HEIGHT_OFFSET = 0.0;
	protected const BIOME_HEIGHT_WEIGHT = 1.0;
	protected const BIOME_SCALE_OFFSET = 0.0;
	protected const BIOME_SCALE_WEIGHT = 1.0;
	protected const DENSITY_FILL_MODE = 0;
	protected const DENSITY_FILL_SEA_MODE = 0;
	protected const DENSITY_FILL_OFFSET = 0.0;
	private static function elevationWeightHash(int $x, int $z) : int{
		return ($x << 3) | $z;
	}
	private static function densityHash(int $i, int $j, int $k) : int{
		return ($k << 6) | ($j << 3) | $i;
	}
	protected function getWorldOctaves() : WorldOctaves{
		return $this->octave_cache ??= $this->createWorldOctaves();
	}
	protected function createWorldOctaves() : WorldOctaves{
		$seed = new Random($this->random->getSeed());
		$height = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 1, 5);
		$height->setXScale(self::HEIGHT_NOISE_SCALE_X);
		$height->setZScale(self::HEIGHT_NOISE_SCALE_Z);
		$roughness = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 33, 5);
		$roughness->setXScale(self::COORDINATE_SCALE);
		$roughness->setYScale(self::HEIGHT_SCALE);
		$roughness->setZScale(self::COORDINATE_SCALE);
		$roughness2 = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 16, 5, 33, 5);
		$roughness2->setXScale(self::COORDINATE_SCALE);
		$roughness2->setYScale(self::HEIGHT_SCALE);
		$roughness2->setZScale(self::COORDINATE_SCALE);
		$detail = PerlinOctaveGenerator::fromRandomAndOctaves($seed, 8, 5, 33, 5);
		$detail->setXScale(self::COORDINATE_SCALE / self::DETAIL_NOISE_SCALE_X);
		$detail->setYScale(self::HEIGHT_SCALE / self::DETAIL_NOISE_SCALE_Y);
		$detail->setZScale(self::COORDINATE_SCALE / self::DETAIL_NOISE_SCALE_Z);
		$surface = SimplexOctaveGenerator::fromRandomAndOctaves($seed, 4, 16, 1, 16);
		$surface->setScale(self::SURFACE_SCALE);
		return new WorldOctaves($height, $roughness, $roughness2, $detail, $surface);
	}
	public function __construct(array $options = []){
		if(self::$ELEVATION_WEIGHT === null){
			self::generateKernel();
		}
	}
	private static function generateKernel(){
		for($x = 0; $x < 5; ++$x){
			for($z = 0; $z < 5; ++$z){
				$sq_x = $x - 2;
				$sq_x *= $sq_x;
				$sq_z = $z - 2;
				$sq_z *= $sq_z;
				self::$ELEVATION_WEIGHT[self::elevationWeightHash($x, $z)] = 10.0 / sqrt($sq_x + $sq_z + 0.2);
			}
		}
		self::$GAUSSIAN_KERNEL = [];
		$bellSize = 1 / self::$SMOOTH_SIZE;
		$bellHeight = 2 * self::$SMOOTH_SIZE;
		for($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx){
			self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE] = [];
			for($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz){
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
			}
		}
	}
	public function getName() : string{
		return self::NAME;
	}
	public function getWaterHeight() : int{
		return $this->waterHeight;
	}
	public function getSettings() : array{
		return [];
	}
	public function init(ChunkManager $level, Random $random) : void{
		$this->level = $level;
		$this->random = $random;
		$this->selector = new BiomeSelector($this->random, function($temperature, $rainfall, $River, $ocean, $hills){ 
			if ($ocean < -0.15) {
				if ($ocean < -0.91) {
					if ($ocean < -0.92) {
						return Biome::MUSHROOM_ISLAND;
					}else{
						return Biome::MUSHROOM_ISLAND_SHORE;
					}
				}else{
					if($temperature < -0.4){
						return Biome::FROZEN_OCEAN;
					}else{
						if ($rainfall < 0) {
							return Biome::OCEAN;
						}else{
							return Biome::DEEP_OCEAN;
						}
					}
				}
			}elseif(abs($River) <= 0.02){
				if($temperature < -0.36){
					return Biome::FROZEN_RIVER;
				}else{
					return Biome::RIVER;
				}
			} elseif($ocean < -0.12) {
				if($temperature < -0.379){
					return Biome::COLD_BEACH;
				}else{
					return Biome::BEACH;
				}
			}else{
				if($temperature < -0.379){ 
					if ($rainfall < 0) {
						if($hills < -0.1){
							return Biome::COLD_TAIGA;
						}else{
							if($hills < -0.3){
								return Biome::COLD_TAIGA_HILLS;
							}else{
								return Biome::ICE_MOUNTAINS;
							}
						}
					}else{
						if($hills < 0.7){
							return Biome::ICE_PLAINS;
						}else{
							return Biome::ICE_PLAINS;
						}
					}
				}elseif($temperature < 0){ 
					if($hills < 0){
						return Biome::MOUNTAINS;
					}elseif($hills < 0.2){
						return Biome::TAIGA_HILLS;
					}else{
						if ($rainfall < 0.6) {
							return Biome::TAIGA;
						}else{
							return Biome::TAIGA;
						}
					}
				}elseif($temperature < 0.5){ 
					if ($temperature < 0.25) {
						if ($rainfall < 0) {
							return Biome::PLAINS;
						}elseif($rainfall < 0.25){
							return Biome::FOREST;
						}else{
							return Biome::BIRCH_FOREST;
						}
					}else{
						if ($rainfall < -0.3) {
							return Biome::SWAMP;
						}elseif($rainfall > 0){
							if($hills < 0){
								return Biome::JUNGLE;
							}else{
								return Biome::JUNGLE;
							}
						}else{
							return Biome::ROOFED_FOREST;
						}
					}
				}else{ 
					if ($rainfall < 0) {
						return Biome::DESERT;
					}elseif($rainfall > 0.4){
						return Biome::SAVANNA;
					}else{
						return Biome::MESA;
					}
				}
			}
		}, Biome::getBiome(Biome::PLAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::OCEAN));
		$this->selector->addBiome(Biome::getBiome(Biome::FROZEN_OCEAN));
		$this->selector->addBiome(Biome::getBiome(Biome::DEEP_OCEAN));
		$this->selector->addBiome(Biome::getBiome(Biome::PLAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::DESERT));
		$this->selector->addBiome(Biome::getBiome(Biome::MOUNTAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::FOREST));
		$this->selector->addBiome(Biome::getBiome(Biome::TAIGA));
		$this->selector->addBiome(Biome::getBiome(Biome::TAIGA_HILLS));
		$this->selector->addBiome(Biome::getBiome(Biome::COLD_TAIGA));
		$this->selector->addBiome(Biome::getBiome(Biome::COLD_TAIGA_HILLS));
		$this->selector->addBiome(Biome::getBiome(Biome::SWAMP));
		$this->selector->addBiome(Biome::getBiome(Biome::RIVER));
		$this->selector->addBiome(Biome::getBiome(Biome::FROZEN_RIVER));
		$this->selector->addBiome(Biome::getBiome(Biome::ICE_PLAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::ICE_MOUNTAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::SMALL_MOUNTAINS));
		$this->selector->addBiome(Biome::getBiome(Biome::BIRCH_FOREST));
		$this->selector->addBiome(Biome::getBiome(Biome::BEACH));
		$this->selector->addBiome(Biome::getBiome(Biome::COLD_BEACH));
		$this->selector->addBiome(Biome::getBiome(Biome::SAVANNA));
		$this->selector->addBiome(Biome::getBiome(Biome::JUNGLE));
		$this->selector->addBiome(Biome::getBiome(Biome::MESA));
		$this->selector->addBiome(Biome::getBiome(Biome::MUSHROOM_ISLAND));
		$this->selector->addBiome(Biome::getBiome(Biome::MUSHROOM_ISLAND_SHORE));
		$this->selector->addBiome(Biome::getBiome(Biome::ROOFED_FOREST));
		$this->selector->recalculate();
		$cover = new GroundCover();
		$this->generationPopulators[] = $cover;
		$Cave = new Cave();
		$this->generationPopulators[] = $Cave;
		$ores = new Ore();
		$ores->setOreTypes([
			new OreType(new CoalOre(), 20, 16, 0, 128),
			new OreType(New IronOre(), 20, 8, 0, 64),
			new OreType(new RedstoneOre(), 8, 7, 0, 16),
			new OreType(new LapisOre(), 1, 6, 0, 32),
			new OreType(new GoldOre(), 2, 8, 0, 32),
			new OreType(new DiamondOre(), 1, 7, 0, 16),
			new OreType(new Dirt(), 20, 32, 0, 128),
			new OreType(new Stone(Stone::GRANITE), 20, 32, 0, 128),
			new OreType(new Stone(Stone::DIORITE), 20, 32, 0, 128),
			new OreType(new Stone(Stone::ANDESITE), 20, 32, 0, 128),
			new OreType(new Gravel(), 10, 16, 0, 128)
		]);
		$this->populators[] = $ores;
	}
	public function pickBiome($x, $z){
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->level->getSeed();
		$hash = (int) ($hash * ($hash + 223));
		$xNoise = $hash >> 20 & 3;
		$zNoise = $hash >> 22 & 3;
		if($xNoise == 3){
			$xNoise = 1;
		}
		if($zNoise == 3){
			$zNoise = 1;
		}
		return $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
	}
	public function generateChunk(int $chunkX, int $chunkZ) : void{
		$this->generateRawTerrain($this->level, $chunkX, $chunkZ);
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){
				$biome = $this->pickBiome($chunkX * 16 + $x, $chunkZ * 16 + $z);
				$chunk->setBiomeId($x, $z, $biome->getId());
				$chunk->setBlockId($x, 0, $z, Block::BEDROCK);
				$color = [0, 0, 0];
				$weightSum = 0;
				for($sx = -self::$SMOOTH_SIZE; $sx <= self::$SMOOTH_SIZE; ++$sx){
					for($sz = -self::$SMOOTH_SIZE; $sz <= self::$SMOOTH_SIZE; ++$sz){
						$weight = self::$GAUSSIAN_KERNEL[$sx + self::$SMOOTH_SIZE][$sz + self::$SMOOTH_SIZE];
						if($sx === 0 and $sz === 0){
							$adjacent = $biome;
						}else{
							$index = Level::chunkHash($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
							if(isset($biomeCache[$index])){
								$adjacent = $biomeCache[$index];
							}else{
								$biomeCache[$index] = $adjacent = $this->pickBiome($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
							}
						}
						$bColor = $adjacent->getColor();
						$color[0] += (($bColor >> 16) ** 2) * $weight;
						$color[1] += ((($bColor >> 8) & 0xff) ** 2) * $weight;
						$color[2] += (($bColor & 0xff) ** 2) * $weight;
						$weightSum += $weight;
					}
				}
			}
		}
		foreach($this->generationPopulators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}
	protected function generateRawTerrain(ChunkManager $world, int $chunk_x, int $chunk_z) : void{
		$density = $this->generateTerrainDensity($chunk_x, $chunk_z);
		$sea_level = 64;
		$fill = self::DENSITY_FILL_MODE;
		$afill = abs($fill);
		$sea_fill = self::DENSITY_FILL_SEA_MODE;
		$density_offset = self::DENSITY_FILL_OFFSET;
		$still_water = Block::STILL_WATER;
		$water = Block::WATER;
		$stone = Block::STONE;
		$chunk = $world->getChunk($chunk_x, $chunk_z);
		for($i = 0; $i < 5 - 1; ++$i){
			for($j = 0; $j < 5 - 1; ++$j){
				for($k = 0; $k < 16; ++$k){
					$d1 = $density[self::densityHash($i, $j, $k)];
					$d2 = $density[self::densityHash($i + 1, $j, $k)];
					$d3 = $density[self::densityHash($i, $j + 1, $k)];
					$d4 = $density[self::densityHash($i + 1, $j + 1, $k)];
					$d5 = ($density[self::densityHash($i, $j, $k + 1)] - $d1) / 8;
					$d6 = ($density[self::densityHash($i + 1, $j, $k + 1)] - $d2) / 8;
					$d7 = ($density[self::densityHash($i, $j + 1, $k + 1)] - $d3) / 8;
					$d8 = ($density[self::densityHash($i + 1, $j + 1, $k + 1)] - $d4) / 8;
					for($l = 0; $l < 8; ++$l){
						$d9 = $d1;
						$d10 = $d3;
						$y_pos = $l + ($k << 3);
						$y_block_pos = $y_pos;
						$sub_chunk = $chunk;
						for($m = 0; $m < 4; ++$m){
							$dens = $d9;
							for($n = 0; $n < 4; ++$n){
								if($afill === 1 || $afill === 10 || $afill === 13 || $afill === 16){
									$sub_chunk->setBlockId($m + ($i << 2), $y_block_pos, $n + ($j << 2), $water);
								}elseif($afill === 2 || $afill === 9 || $afill === 12 || $afill === 15){
									$sub_chunk->setBlockId($m + ($i << 2), $y_block_pos, $n + ($j << 2), $stone);
								}
								if(($dens > $density_offset && $fill > -1) || ($dens <= $density_offset && $fill < 0)){
									if($afill === 0 || $afill === 3 || $afill === 6 || $afill === 9 || $afill === 12){
										$sub_chunk->setBlockId($m + ($i << 2), $y_block_pos, $n + ($j << 2), $stone);
									}elseif($afill === 2 || $afill === 7 || $afill === 10 || $afill === 16){
										$sub_chunk->setBlockId($m + ($i << 2), $y_block_pos, $n + ($j << 2), $still_water);
									}
								}elseif(($y_pos < $sea_level - 1 && $sea_fill === 0) || ($y_pos >= $sea_level - 1 && $sea_fill === 1)){
									if($afill === 0 || $afill === 3 || $afill === 7 || $afill === 10 || $afill === 13){
										$sub_chunk->setBlockId($m + ($i << 2), $y_block_pos, $n + ($j << 2), $still_water);
									}elseif($afill === 1 || $afill === 6 || $afill === 9 || $afill === 15){
										$sub_chunk->setBlockId($m + ($i << 2), $y_block_pos, $n + ($j << 2), $stone);
									}
								}
								$dens += ($d10 - $d9) / 4;
							}
							$d9 += ($d2 - $d1) / 4;
							$d10 += ($d4 - $d3) / 4;
						}
						$d1 += $d5;
						$d3 += $d7;
						$d2 += $d6;
						$d4 += $d8;
					}
				}
			}
		}
	}
	protected function getBiomeHeight(int $id){
		switch($id){
			case Biome::OCEAN:
			case Biome::FROZEN_OCEAN:
				return new BiomeHeight(-1.0, 0.1);
			case Biome::DEEP_OCEAN:
				return new BiomeHeight(-1.8, 0.1);
			case Biome::RIVER:
			case Biome::FROZEN_RIVER:
				return new BiomeHeight(-0.5, 0.0);
			case Biome::BEACH:
			case Biome::COLD_BEACH:
			case Biome::MUSHROOM_ISLAND_SHORE:
				return new BiomeHeight(0.0, 0.025);
			case Biome::DESERT:
			case Biome::ICE_PLAINS:
			case Biome::SAVANNA:
				return new BiomeHeight(0.125, 0.05);
			case Biome::MOUNTAINS: 
				return new BiomeHeight(1.0, 0.5);
			case Biome::TAIGA:
			case Biome::COLD_TAIGA:
				return new BiomeHeight(0.2, 0.2);
			case Biome::SWAMP:
				return new BiomeHeight(-0.2, 0.1);
			case Biome::MUSHROOM_ISLAND:
				return new BiomeHeight(0.2, 0.3);
			case Biome::ICE_MOUNTAINS:
			case Biome::TAIGA_HILLS:
			case Biome::SMALL_MOUNTAINS:
			case Biome::JUNGLE_HILLS:
			case Biome::MESA:
			case Biome::COLD_TAIGA_HILLS:
				return new BiomeHeight(0.45, 0.3);
			default:
				return new BiomeHeight(0.1, 0.2);
		}
	}
	protected function generateTerrainDensity(int $x, int $z) : array{
		$density = [];
		$chunkX = $x;
		$chunkZ = $z;
		$x <<= 2;
		$z <<= 2;
		$x -= 2;
		$z -= 2;
		$octaves = $this->getWorldOctaves();
		$height_noise = $octaves->height->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$roughness_noise = $octaves->roughness->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$roughness_noise_2 = $octaves->roughness_2->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$detail_noise = $octaves->detail->getFractalBrownianMotion($x, 0, $z, 0.5, 2.0);
		$index = 0;
		$index_height = 0;
		for($i = 0; $i < 5; ++$i){
			for($j = 0; $j < 5; ++$j){
				$avg_height_scale = 0.0;
				$avg_height_base = 0.0;
				$total_weight = 0.0;
				$cx = $x + $i + 2;
				$cz = $z + $j + 2;
				$cx *= 4;
				$cz *= 4;
				$biome = $this->pickBiome($cx, $cz);
				$biome_height = $this->getBiomeHeight($biome->getId());
				for($m = 0; $m < 5; ++$m){
					for($n = 0; $n < 5; ++$n){
						$cx = $x + $i + $m;
						$cz = $z + $j + $n;
						$cx *= 4;
						$cz *= 4;
						$near_biome = $this->pickBiome($cx, $cz);
						$near_biome_height = $this->getBiomeHeight($near_biome->getId());
						$height_base = self::BIOME_HEIGHT_OFFSET + $near_biome_height->getHeight() * self::BIOME_HEIGHT_WEIGHT;
						$height_scale = self::BIOME_SCALE_OFFSET + $near_biome_height->getScale() * self::BIOME_SCALE_WEIGHT;
						$weight = self::$ELEVATION_WEIGHT[self::elevationWeightHash($m, $n)] / ($height_base + 2.0);
						if($near_biome_height->getHeight() > $biome_height->getHeight()){
							$weight *= 0.5;
						}
						$avg_height_scale += $height_scale * $weight;
						$avg_height_base += $height_base * $weight;
						$total_weight += $weight;
					}
				}
				$avg_height_scale /= $total_weight;
				$avg_height_base /= $total_weight;
				$avg_height_scale = $avg_height_scale * 0.9 + 0.1;
				$avg_height_base = ($avg_height_base * 4.0 - 1.0) / 8.0;
				$noise_h = $height_noise[$index_height++] / 8000.0;
				if($noise_h < 0){
					$noise_h = -$noise_h * 0.3;
				}
				$noise_h = $noise_h * 3.0 - 2.0;
				if($noise_h < 0){
					$noise_h = max($noise_h * 0.5, -1) / 1.4 * 0.5;
				}else{
					$noise_h = min($noise_h, 1) / 8.0;
				}
				$noise_h = ($noise_h * 0.2 + $avg_height_base) * self::BASE_SIZE / 8.0 * 4.0 + self::BASE_SIZE;
				for($k = 0; $k < 33; ++$k){
					$nh = ($k - $noise_h) * self::STRETCH_Y * 128.0 / 256.0 / $avg_height_scale;
					if($nh < 0.0){
						$nh *= 4.0;
					}
					$noise_r = $roughness_noise[$index] / 512.0;
					$noise_r_2 = $roughness_noise_2[$index] / 512.0;
					$noise_d = ($detail_noise[$index] / 10.0 + 1.0) / 2.0;
					$dens = $noise_d < 0 ? $noise_r : ($noise_d > 1 ? $noise_r_2 : $noise_r + ($noise_r_2 - $noise_r) * $noise_d);
					$dens -= $nh;
					++$index;
					if($k > 29){
						$lowering = ($k - 29) / 3.0;
						$dens = $dens * (1.0 - $lowering) + -10.0 * $lowering;
					}
					$density[self::densityHash($i, $j, $k)] = $dens;
				}
			}
		}
		return $density;
	}
	public function populateChunk(int $chunkX, int $chunkZ) : void{
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
		foreach($this->populators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = Biome::getBiome($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}
	public function getSpawn() : Vector3{
		return new Vector3(127.5, 128, 127.5);
	}
}