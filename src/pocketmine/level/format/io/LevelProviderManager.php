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
namespace pocketmine\level\format\io;
use InvalidArgumentException;use pocketmine\level\format\io\leveldb\LevelDB;use pocketmine\level\format\io\region\Anvil;use pocketmine\level\format\io\region\McRegion;use pocketmine\level\format\io\region\PMAnvil;use ReflectionClass;use ReflectionException;use function strtolower;use function trim;
abstract class LevelProviderManager{
	protected static $providers = [];
	public static function init() : void{
		self::addProvider(Anvil::class);
		self::addProvider(McRegion::class);
		self::addProvider(PMAnvil::class);
		self::addProvider(LevelDB::class);
	}
	public static function addProvider(string $class){
		try{
			$reflection = new ReflectionClass($class);
		}catch(ReflectionException $e){
			throw new InvalidArgumentException("Class $class does not exist");
		}
		if(!$reflection->implementsInterface(LevelProvider::class)){
			throw new InvalidArgumentException("Class $class does not implement " . LevelProvider::class);
		}
		if(!$reflection->isInstantiable()){
			throw new InvalidArgumentException("Class $class cannot be constructed");
		}
		self::$providers[strtolower($class::getProviderName())] = $class;
	}
	public static function getProvider(string $path){
		foreach(self::$providers as $provider){
			if($provider::isValid($path)){
				return $provider;
			}
		}
		return null;
	}
	public static function getProviderByName(string $name){
		return self::$providers[trim(strtolower($name))] ?? null;
	}}