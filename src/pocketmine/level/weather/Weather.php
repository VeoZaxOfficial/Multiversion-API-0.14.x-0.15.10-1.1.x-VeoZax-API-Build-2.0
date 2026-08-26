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
namespace pocketmine\level\weather;
use pocketmine\entity\Entity;use pocketmine\event\level\WeatherChangeEvent;use pocketmine\level\Level;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\LevelEventPacket;use pocketmine\Player;use function array_rand;use function count;use function is_int;use function max;use function min;use function mt_rand;
class Weather{
	public const CLEAR = 0;
	public const SUNNY = 0;
	public const RAIN = 1;
	public const RAINY = 1;
	public const RAINY_THUNDER = 2;
	public const THUNDER = 3;
	private $level;
	private $weatherNow = 0;
	private $strength1 = 100000;
	private $strength2 = 35000;
	private $duration;
	private $canCalculate = true;
	private $temporalVector = null;
	private $lastUpdate = 0;
	private $randomWeatherData = [0, 1, 0, 1, 0, 1, 0, 2, 0, 3];
	public function __construct(Level $level, int $duration = 1200){
		$this->level = $level;
		$this->weatherNow = self::SUNNY;
		$this->duration = $duration;
		$this->lastUpdate = $level->getServer()->getTick();
		$this->temporalVector = new Vector3(0, 0, 0);
	}
	public function canCalculate() : bool{
		return $this->canCalculate;
	}
	public function setCanCalculate(bool $canCalc) : void{
		$this->canCalculate = $canCalc;
	}
	public function calcWeather(int $currentTick) : void{
		if($this->canCalculate()){
			$tickDiff = $currentTick - $this->lastUpdate;
			$this->duration -= $tickDiff;
			if($this->duration <= 0){
				$duration = mt_rand(
					min($this->level->getServer()->weatherRandomDurationMin, $this->level->getServer()->weatherRandomDurationMax),
					max($this->level->getServer()->weatherRandomDurationMin, $this->level->getServer()->weatherRandomDurationMax));
				if($this->weatherNow === self::SUNNY){
					$weather = $this->randomWeatherData[array_rand($this->randomWeatherData)];
					$this->setWeather($weather, $duration);
				}else{
					$weather = self::SUNNY;
					$this->setWeather($weather, $duration);
				}
			}
			if(($this->weatherNow >= self::RAINY_THUNDER) and ($this->level->getServer()->lightningTime > 0) and is_int($this->duration / $this->level->getServer()->lightningTime)){
				$players = $this->level->getPlayers();
				if(count($players) > 0){
					$p = $players[array_rand($players)];
					$x = $p->x + mt_rand(-64, 64);
					$z = $p->z + mt_rand(-64, 64);
					$y = $this->level->getHighestBlockAt((int) $x, (int) $z);
					$nbt = Entity::createBaseNBT(new Vector3($x, $y, $z));
					$lightning = Entity::createEntity("Lightning", $this->level, $nbt);
					$lightning->spawnToAll();
				}
			}
		}
		$this->lastUpdate = $currentTick;
	}
	public function setWeather(int $wea, int $duration = 12000) : void{
		$this->level->getServer()->getPluginManager()->callEvent($ev = new WeatherChangeEvent($this->level, $wea, $duration));
		if(!$ev->isCancelled()){
			$this->weatherNow = $ev->getWeather();
			$this->strength1 = mt_rand(90000, 110000); 
			$this->strength2 = mt_rand(30000, 40000);
			$this->duration = $ev->getDuration();
			$this->sendWeatherToAll();
		}
	}
	public function getRandomWeatherData() : array{
		return $this->randomWeatherData;
	}
	public function setRandomWeatherData(array $randomWeatherData) : void{
		$this->randomWeatherData = $randomWeatherData;
	}
	public function getWeather() : int{
		return $this->weatherNow;
	}
	public static function getWeatherFromString(mixed $weather) : int{
		if(is_int($weather)){
			if($weather <= 3){
				return $weather;
			}
			return self::SUNNY;
		}
		switch(strtolower($weather)){
			case "clear":
			case "sunny":
			case "fine":
				return self::SUNNY;
			case "rain":
			case "rainy":
				return self::RAINY;
			case "thunder":
				return self::THUNDER;
			case "rain_thunder":
			case "rainy_thunder":
			case "storm":
				return self::RAINY_THUNDER;
			default:
				return self::SUNNY;
		}
	}
	public function isSunny() : bool{
		return $this->getWeather() === self::SUNNY;
	}
	public function isRainy() : bool{
		return $this->getWeather() === self::RAINY;
	}
	public function isRainyThunder() : bool{
		return $this->getWeather() === self::RAINY_THUNDER;
	}
	public function isThunder() : bool{
		return $this->getWeather() === self::THUNDER;
	}
	public function getStrength() : array{
		return [$this->strength1, $this->strength2];
	}
	public function sendWeather(Player $p) : void{
		$pks = [
			new LevelEventPacket(),
			new LevelEventPacket()
		];
		$pks[0]->position = new Vector3(0, 0, 0);
		$pks[1]->position = new Vector3(0, 0, 0);
		$pks[0]->evid = LevelEventPacket::EVENT_STOP_RAIN;
		$pks[0]->data = $this->strength1;
		$pks[1]->evid = LevelEventPacket::EVENT_STOP_THUNDER;
		$pks[1]->data = $this->strength2;
		switch($this->weatherNow){
			case self::RAIN:
				$pks[0]->evid = LevelEventPacket::EVENT_START_RAIN;
				$pks[0]->data = $this->strength1;
				break;
			case self::RAINY_THUNDER:
				$pks[0]->evid = LevelEventPacket::EVENT_START_RAIN;
				$pks[0]->data = $this->strength1;
				$pks[1]->evid = LevelEventPacket::EVENT_START_THUNDER;
				$pks[1]->data = $this->strength2;
				break;
			case self::THUNDER:
				$pks[1]->evid = LevelEventPacket::EVENT_START_THUNDER;
				$pks[1]->data = $this->strength2;
				break;
			default:
				break;
		}
		foreach($pks as $pk){
			$p->dataPacket($pk);
		}
	}
	public function sendWeatherToAll() : void{
		foreach($this->level->getPlayers() as $player){
			$this->sendWeather($player);
		}
	}
}