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
use InvalidArgumentException;use function getmypid;
trait EnumTrait{
	use RegistryTrait;
	use NotCloneable;
	use NotSerializable;
	protected static function register(self $member) : void{
		self::_registryRegister($member->name(), $member);
	}
	protected static function registerAll(self ...$members) : void{
		foreach($members as $member){
			self::register($member);
		}
	}
	public static function getAll() : array{
		$result = self::_registryGetAll();
		return $result;
	}
    public static function fromString(string $enumName) : self{
        return self::_registryFromString($enumName);
    }
	private static $nextId = null;
	private $enumName;
	private $runtimeId;
	private function __construct(string $enumName){
		self::verifyName($enumName);
		$this->enumName = $enumName;
		if(self::$nextId === null){
			self::$nextId = getmypid(); 
		}
		$this->runtimeId = self::$nextId++;
	}
	public function name() : string{
		return $this->enumName;
	}
	public function id() : int{
		return $this->runtimeId;
	}
	public function equals(self $other) : bool{
		return $this->enumName === $other->enumName;
	}}