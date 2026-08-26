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

namespace pocketmine\level\biome;
use pocketmine\block\Block;use pocketmine\entity\Animal;use pocketmine\entity\Creature;use pocketmine\entity\CreatureType;use pocketmine\entity\hostile\Creeper;use pocketmine\entity\hostile\Skeleton;use pocketmine\entity\hostile\Slime;use pocketmine\entity\hostile\Spider;use pocketmine\entity\hostile\Zombie;use pocketmine\entity\Monster;use pocketmine\entity\passive\Chicken;use pocketmine\entity\passive\Cow;use pocketmine\entity\passive\Pig;use pocketmine\entity\passive\Sheep;use pocketmine\entity\passive\Squid;use pocketmine\entity\WaterAnimal;use pocketmine\level\ChunkManager;use pocketmine\level\biome\SwampBiome;use pocketmine\level\biome\SavannaBiome;use pocketmine\level\biome\BeachBiome;use pocketmine\level\biome\ColdBeachBiome;use pocketmine\level\biome\DesertBiome;use pocketmine\level\biome\ForestBiome;use pocketmine\level\biome\IcePlainsBiome;use pocketmine\level\biome\IceMountainsBiome;use pocketmine\level\biome\MountainsBiome;use pocketmine\level\biome\MushroomIsland;use pocketmine\level\biome\OceanBiome;use pocketmine\level\biome\FrozenOceanBiome;use pocketmine\level\biome\DeepOceanBiome;use pocketmine\level\biome\PlainBiome;use pocketmine\level\biome\RiverBiome;use pocketmine\level\biome\FrozenRiverBiome;use pocketmine\level\biome\SmallMountainsBiome;use pocketmine\level\biome\TaigaBiome;use pocketmine\level\biome\TaigaHellBiome;use pocketmine\level\biome\ColdTaigaBiome;use pocketmine\level\biome\ColdTaigaHellBiome;use pocketmine\level\biome\JungleBiome;use pocketmine\level\biome\JungleHillsBiome;use pocketmine\level\biome\MesaBiome;use pocketmine\level\biome\DrakOakBiome;use pocketmine\level\biome\HellBiome;use pocketmine\level\generator\populator\Populator;use pocketmine\utils\Random;
use pocketmine\level\generator\populator\Flower;
abstract class Biome{
	const OCEAN = 0; 
	const PLAINS = 1;  
	const DESERT = 2; 
	const MOUNTAINS = 3; 
	const FOREST = 4; 
	const TAIGA = 5; 
	const SWAMP = 6; 
	const RIVER = 7; 
	const HELL = 8; 
	const END = 9; 
	const FROZEN_OCEAN = 10; 
	const FROZEN_RIVER = 11; 
	const ICE_PLAINS = 12; 
	const ICE_MOUNTAINS = 13;  
	const MUSHROOM_ISLAND = 14; 
	const MUSHROOM_ISLAND_SHORE = 15;  
	const BEACH = 16; 
	const TAIGA_HILLS = 19; 
	const SMALL_MOUNTAINS = 20; 
	const JUNGLE = 21; 
	const JUNGLE_HILLS = 22; 
	const DEEP_OCEAN = 24; 
	const COLD_BEACH = 26; 
	const BIRCH_FOREST = 27; 
	const ROOFED_FOREST = 29; 
	const COLD_TAIGA = 30; 
	const COLD_TAIGA_HILLS = 31; 
	const SAVANNA = 35; 
	const MESA = 37; 
	const MAX_BIOMES = 256;
	private static $biomes = [];
	private $id;
	private $registered = false;
	private $populators = [];
	private $minElevation;
	private $maxElevation;
	private $groundCover = [];
	protected $rainfall = 0.5;
	protected $temperature = 0.5;
	protected $grassColor = 0;
	protected $spawnableMonsterList = [];
	protected $spawnableCreatureList = [];
	protected $spawnableWaterCreatureList = [];
	protected $spawnableCaveCreatureList = [];
	public function __construct(){
		$this->spawnableCreatureList[] = new SpawnListEntry(Sheep::class, 12, 4, 4);
		$this->spawnableCreatureList[] = new SpawnListEntry(Pig::class, 10, 4, 4);
		$this->spawnableCreatureList[] = new SpawnListEntry(Chicken::class, 10, 4, 4);
		$this->spawnableCreatureList[] = new SpawnListEntry(Cow::class, 8, 4, 4);
		$this->spawnableMonsterList[] = new SpawnListEntry(Spider::class, 100, 4, 4);
		$this->spawnableMonsterList[] = new SpawnListEntry(Zombie::class, 100, 4, 4);
		$this->spawnableMonsterList[] = new SpawnListEntry(Skeleton::class, 100, 4, 4);
		$this->spawnableMonsterList[] = new SpawnListEntry(Creeper::class, 100, 4, 4);
		$this->spawnableMonsterList[] = new SpawnListEntry(Slime::class, 100, 4, 4);
		$this->spawnableWaterCreatureList[] = new SpawnListEntry(Squid::class, 10, 4, 4);
	}
	public function getSpawnableList(CreatureType $creatureType) : array{
		$entityClass = $creatureType->getCreatureClass();
		switch($entityClass){
			case WaterAnimal::class:
				return $this->spawnableWaterCreatureList;
			case Creature::class:
				return $this->spawnableCaveCreatureList;
			case Animal::class:
				return $this->spawnableCreatureList;
			case Monster::class:
				return $this->spawnableMonsterList;
		}
		return [];
	}
	public function getSpawningChance() : float{
		return 0.1;
	}
	protected static function register($id, Biome $biome){
		self::$biomes[(int) $id] = $biome;
		$biome->setId((int) $id);
		$biome->grassColor = self::generateBiomeColor($biome->getTemperature(), $biome->getRainfall());
		$flowerPopFound = false;
		foreach($biome->getPopulators() as $populator){
			if($populator instanceof Flower){
				$flowerPopFound = true;
				break;
			}
		}
		if($flowerPopFound === false){
			$flower = new Flower();
			$biome->addPopulator($flower);
		}
	}
	public static function init(){
		self::register(self::BEACH, new BeachBiome());
		self::register(self::COLD_BEACH, new ColdBeachBiome());
		self::register(self::OCEAN, new OceanBiome());
		self::register(self::FROZEN_OCEAN, new FrozenOceanBiome());
		self::register(self::DEEP_OCEAN, new DeepOceanBiome());
		self::register(self::PLAINS, new PlainBiome());
		self::register(self::DESERT, new DesertBiome());
		self::register(self::MOUNTAINS, new MountainsBiome());
		self::register(self::FOREST, new ForestBiome());
		self::register(self::TAIGA, new TaigaBiome());
		self::register(self::TAIGA_HILLS, new TaigaHellBiome());
		self::register(self::COLD_TAIGA, new ColdTaigaBiome());
		self::register(self::COLD_TAIGA_HILLS, new ColdTaigaHellBiome());
		self::register(self::SWAMP, new SwampBiome());
		self::register(self::RIVER, new RiverBiome());
		self::register(self::FROZEN_RIVER, new FrozenRiverBiome());
		self::register(self::ICE_PLAINS, new IcePlainsBiome());
		self::register(self::ICE_MOUNTAINS, new IceMountainsBiome());
		self::register(self::SMALL_MOUNTAINS, new SmallMountainsBiome());
		self::register(self::JUNGLE, new JungleBiome());
		self::register(self::JUNGLE_HILLS, new JungleHillsBiome());
		self::register(self::SAVANNA, new SavannaBiome());
		self::register(self::MESA, new MesaBiome());
		self::register(self::MUSHROOM_ISLAND, new MushroomIsland());
		self::register(self::MUSHROOM_ISLAND_SHORE, new MushroomIsland());
		self::register(self::ROOFED_FOREST, new DrakOakBiome());
		self::register(self::HELL, new HellBiome());
		self::register(self::BIRCH_FOREST, new ForestBiome(ForestBiome::TYPE_BIRCH));
	}
	public static function getBiome($id){
		return isset(self::$biomes[$id]) ? self::$biomes[$id] : self::$biomes[self::OCEAN];
	}
	public function clearPopulators(){
		$this->populators = [];
	}
	public function addPopulator(Populator $populator){
		$this->populators[get_class($populator)] = $populator;
	}
	public function removePopulator($class){
		if(isset($this->populators[$class])){
			unset($this->populators[$class]);
		}
	}
	public function populateChunk(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		foreach($this->populators as $populator){
			$populator->populate($level, $chunkX, $chunkZ, $random);
		}
	}
	public function getPopulators(){
		return $this->populators;
	}
	public function setId($id){
		if(!$this->registered){
			$this->registered = true;
			$this->id = $id;
		}
	}
	public function getId(){
		return $this->id;
	}
	public abstract function getName();
	public function getMinElevation(){
		return $this->minElevation;
	}
	public function getMaxElevation(){
		return $this->maxElevation;
	}
	public function setElevation($min, $max){
		$this->minElevation = $min;
		$this->maxElevation = $max;
	}
	public function getGroundCover(){
		return $this->groundCover;
	}
	public function setGroundCover(array $covers){
		$this->groundCover = $covers;
	}
	public function getTemperature(){
		return $this->temperature;
	}
	public function getRainfall(){
		return $this->rainfall;
	}
	private static function generateBiomeColor($temperature, $rainfall){
		$x = (1 - $temperature) * 255;
		$z = (1 - $rainfall * $temperature) * 255;
		$c = self::interpolateColor(256, $x, $z, [0x47, 0xd0, 0x33], [0x6c, 0xb4, 0x93], [0xbf, 0xb6, 0x55], [0x80, 0xb4, 0x97]);
		$r = (int) round($c[0]);
		$g = (int) round($c[1]);
		$b = (int) round($c[2]);
		return ($r << 16) | ($g << 8) | $b;
	}
	private static function interpolateColor($size, $x, $z, $c1, $c2, $c3, $c4){
		$l1 = self::lerpColor($c1, $c2, $x / $size);
		$l2 = self::lerpColor($c3, $c4, $x / $size);
		return self::lerpColor($l1, $l2, $z / $size);
	}
	private static function lerpColor($a, $b, $s){
		$invs = 1 - $s;
		return [$a[0] * $invs + $b[0] * $s, $a[1] * $invs + $b[1] * $s, $a[2] * $invs + $b[2] * $s];
	}
	abstract public function getColor();}