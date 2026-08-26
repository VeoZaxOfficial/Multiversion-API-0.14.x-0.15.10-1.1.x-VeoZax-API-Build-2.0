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


namespace pocketmine\command\defaults;
use pocketmine\command\CommandSender;use pocketmine\lang\TranslationContainer;use pocketmine\level\Level;use pocketmine\level\weather\Weather;use pocketmine\Player;use pocketmine\utils\TextFormat;use function count;use function max;use function min;use function mt_rand;
class WeatherCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.weather.description",
			"%pocketmine.command.weather.usage"
		);
		$this->setPermission("pocketmine.command.weather");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) < 1){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}
		if($sender instanceof Player){
			$wea = Weather::getWeatherFromString($args[0]);
			if(!isset($args[1])) $duration = mt_rand(min($sender->getServer()->weatherRandomDurationMin, $sender->getServer()->weatherRandomDurationMax), max($sender->getServer()->weatherRandomDurationMin, $sender->getServer()->weatherRandomDurationMax));
			else $duration = (int) $args[1];
			if($wea >= 0 and $wea <= 3){
				$sender->getLevel()->getWeather()->setWeather($wea, $duration);
				$sender->sendMessage(new TranslationContainer("pocketmine.command.weather.changed", [$sender->getLevel()->getFolderName()]));
				return true;
			}else{
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%pocketmine.command.weather.invalid"));
				return false;
			}
		}
		if(count($args) < 2){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}
		$level = $sender->getServer()->getLevelByName($args[0]);
		if(!$level instanceof Level){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%pocketmine.command.weather.invalid.level"));
			return false;
		}
		$wea = Weather::getWeatherFromString($args[1]);
		if(!isset($args[1])) $duration = mt_rand(min($sender->getServer()->weatherRandomDurationMin, $sender->getServer()->weatherRandomDurationMax), max($sender->getServer()->weatherRandomDurationMin, $sender->getServer()->weatherRandomDurationMax));
		else $duration = (int) $args[1];
		if($wea >= 0 and $wea <= 3){
			$level->getWeather()->setWeather($wea, $duration);
			$sender->sendMessage(new TranslationContainer("pocketmine.command.weather.changed", [$level->getFolderName()]));
			return true;
		}else{
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%pocketmine.command.weather.invalid"));
			return false;
		}
	}}