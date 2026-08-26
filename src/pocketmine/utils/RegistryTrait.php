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
namespace pocketmine\utils;
use ArgumentCountError;use Error;use InvalidArgumentException;use function array_map;use function count;use function mb_strtoupper;use function preg_match;
trait RegistryTrait{
	private static $members = null;
	private static function verifyName(string $name) : void{
		if(preg_match('/^(?!\d)[A-Za-z\d_]+$/u', $name) === 0){
			throw new InvalidArgumentException("Invalid member name \"$name\", should only contain letters, numbers and underscores, and must not start with a number");
		}
	}
	private static function _registryRegister(string $name, object $member) : void{
		self::verifyName($name);
		$upperName = mb_strtoupper($name);
		if(isset(self::$members[$upperName])){
			throw new InvalidArgumentException("\"$upperName\" is already reserved");
		}
		self::$members[$upperName] = $member;
	}
	abstract protected static function setup() : void;
	protected static function checkInit() : void{
		if(self::$members === null){
			self::$members = [];
			self::setup();
		}
	}
	private static function _registryFromString(string $name) : object{
		self::checkInit();
		$upperName = mb_strtoupper($name);
		if(!isset(self::$members[$upperName])){
			throw new InvalidArgumentException("No such registry member: " . self::class . "::" . $upperName);
		}
		return self::preprocessMember(self::$members[$upperName]);
	}
	protected static function preprocessMember(object $member) : object{
		return $member;
	}
	public static function __callStatic($name, $arguments){
		if(count($arguments) > 0){
			throw new ArgumentCountError("Expected exactly 0 arguments, " . count($arguments) . " passed");
		}
		try{
			return self::_registryFromString($name);
		}catch(InvalidArgumentException $e){
			throw new Error($e->getMessage(), 0, $e);
		}
	}
	private static function _registryGetAll() : array{
		self::checkInit();
		return array_map(function(object $o) : object{
			return self::preprocessMember($o);
		}, self::$members);
	}}