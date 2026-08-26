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
use InvalidArgumentException;use pocketmine\level\generator\ender\Ender;use pocketmine\level\generator\hell\Nether;use pocketmine\level\generator\normal\Normal;use function array_keys;use function is_subclass_of;use function strtolower;
final class GeneratorManager{
	private static $list = [];
	public static function registerDefaultGenerators() : void{
		self::addGenerator(Flat::class, "flat");
    self::addGenerator(VoidGenerator::class, "void");
		self::addGenerator(Normal::class, "normal");
		self::addGenerator(Normal::class, "default");
		self::addGenerator(Nether::class, "hell");
		self::addGenerator(Nether::class, "nether");
		self::addGenerator(Ender::class, "ender");
	}
	public static function addGenerator(string $class, string $name, bool $overwrite = false) : void{
		if(!is_subclass_of($class, Generator::class)){
			throw new InvalidArgumentException("Class $class does not extend " . Generator::class);
		}
		if(!$overwrite and isset(self::$list[$name = strtolower($name)])){
			throw new InvalidArgumentException("Alias \"$name\" is already assigned");
		}
		self::$list[$name] = $class;
	}
	public static function getGeneratorList() : array{
		return array_keys(self::$list);
	}
	public static function getGenerator(string $name, bool $throwOnMissing = false){
		if(isset(self::$list[$name = strtolower($name)])){
			return self::$list[$name];
		}
		if($throwOnMissing){
			throw new InvalidArgumentException("Alias \"$name\" does not map to any known generator");
		}
		return Normal::class;
	}
	public static function getGeneratorName(string $class) : string{
		foreach(self::$list as $name => $c){
			if($c === $class){
				return $name;
			}
		}
		return "unknown";
	}
	private function __construct(){
	}}