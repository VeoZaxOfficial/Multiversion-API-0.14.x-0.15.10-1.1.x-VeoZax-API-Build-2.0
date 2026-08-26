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
namespace pocketmine\math;
use function in_array;
final class Facing{
	private function __construct(){
	}
	public const FLAG_AXIS_POSITIVE = 1;
	public const DOWN =   Axis::Y << 1;
	public const UP =    (Axis::Y << 1) | self::FLAG_AXIS_POSITIVE;
	public const NORTH =  Axis::Z << 1;
	public const SOUTH = (Axis::Z << 1) | self::FLAG_AXIS_POSITIVE;
	public const WEST =   Axis::X << 1;
	public const EAST =  (Axis::X << 1) | self::FLAG_AXIS_POSITIVE;
	public const ALL = [
		self::DOWN,
		self::UP,
		self::NORTH,
		self::SOUTH,
		self::WEST,
		self::EAST
	];
	public const HORIZONTAL = [
		self::NORTH,
		self::SOUTH,
		self::WEST,
		self::EAST
	];
	private const CLOCKWISE = [
		Axis::Y => [
			self::NORTH => self::EAST,
			self::EAST => self::SOUTH,
			self::SOUTH => self::WEST,
			self::WEST => self::NORTH
		],
		Axis::Z => [
			self::UP => self::EAST,
			self::EAST => self::DOWN,
			self::DOWN => self::WEST,
			self::WEST => self::UP
		],
		Axis::X => [
			self::UP => self::NORTH,
			self::NORTH => self::DOWN,
			self::DOWN => self::SOUTH,
			self::SOUTH => self::UP
		]
	];
	public static function axis(int $direction) : int{
		return $direction >> 1; 
	}
	public static function isPositive(int $direction) : bool{
		return ($direction & self::FLAG_AXIS_POSITIVE) === self::FLAG_AXIS_POSITIVE;
	}
	public static function opposite(int $direction) : int{
		return $direction ^ self::FLAG_AXIS_POSITIVE;
	}
	public static function rotate(int $direction, int $axis, bool $clockwise) : int{
		if(!isset(self::CLOCKWISE[$axis])){
			throw new \InvalidArgumentException("Invalid axis $axis");
		}
		if(!isset(self::CLOCKWISE[$axis][$direction])){
			throw new \InvalidArgumentException("Cannot rotate facing \"" . self::toString($direction) . "\" around axis \"" . Axis::toString($axis) . "\"");
		}
		$rotated = self::CLOCKWISE[$axis][$direction];
		return $clockwise ? $rotated : self::opposite($rotated);
	}
	public static function rotateY(int $direction, bool $clockwise) : int{
		return self::rotate($direction, Axis::Y, $clockwise);
	}
	public static function rotateZ(int $direction, bool $clockwise) : int{
		return self::rotate($direction, Axis::Z, $clockwise);
	}
	public static function rotateX(int $direction, bool $clockwise) : int{
		return self::rotate($direction, Axis::X, $clockwise);
	}
	public static function validate(int $facing) : void{
		if(!in_array($facing, self::ALL, true)){
			throw new \InvalidArgumentException("Invalid direction $facing");
		}
	}
	public static function toString(int $facing) : string{
		$result = [
			self::DOWN => "down",
			self::UP => "up",
			self::NORTH => "north",
			self::SOUTH => "south",
			self::WEST => "west",
			self::EAST => "east"
		][$facing] ?? null;
		if($result === null){
			throw new \InvalidArgumentException("Invalid facing $facing");
		}
		return $result;
	}}