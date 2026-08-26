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
use InvalidArgumentException;use function abs;use function ceil;use function floor;use function max;use function round;use function sqrt;use const PHP_ROUND_HALF_UP;
class Vector3{
	public const SIDE_DOWN = 0;
	public const SIDE_UP = 1;
	public const SIDE_NORTH = 2;
	public const SIDE_SOUTH = 3;
	public const SIDE_WEST = 4;
	public const SIDE_EAST = 5;
	public $x;
	public $y;
	public $z;
	public function __construct($x = 0, $y = 0, $z = 0){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}
	public function getX(){
		return $this->x;
	}
	public function getY(){
		return $this->y;
	}
	public function getZ(){
		return $this->z;
	}
	public function getFloorX() : int{
		return (int) floor($this->x);
	}
	public function getFloorY() : int{
		return (int) floor($this->y);
	}
	public function getFloorZ() : int{
		return (int) floor($this->z);
	}
	public function add($x, $y = 0, $z = 0) : Vector3{
		if($x instanceof Vector3){
			return new Vector3($this->x + $x->x, $this->y + $x->y, $this->z + $x->z);
		}else{
			return new Vector3($this->x + $x, $this->y + $y, $this->z + $z);
		}
	}
	public function subtract($x = 0, $y = 0, $z = 0) : Vector3{
		if($x instanceof Vector3){
			return $this->add(-$x->x, -$x->y, -$x->z);
		}else{
			return $this->add(-$x, -$y, -$z);
		}
	}
	public function multiply(float $number) : Vector3{
		return new Vector3($this->x * $number, $this->y * $number, $this->z * $number);
	}
	public function divide(float $number) : Vector3{
		return new Vector3($this->x / $number, $this->y / $number, $this->z / $number);
	}
	public function ceil() : Vector3{
		return new Vector3((int) ceil($this->x), (int) ceil($this->y), (int) ceil($this->z));
	}
	public function floor() : Vector3{
		return new Vector3((int) floor($this->x), (int) floor($this->y), (int) floor($this->z));
	}
	public function round(int $precision = 0, int $mode = PHP_ROUND_HALF_UP) : Vector3{
		return $precision > 0 ?
			new Vector3(round($this->x, $precision, $mode), round($this->y, $precision, $mode), round($this->z, $precision, $mode)) :
			new Vector3((int) round($this->x, $precision, $mode), (int) round($this->y, $precision, $mode), (int) round($this->z, $precision, $mode));
	}
	public function abs() : Vector3{
		return new Vector3(abs($this->x), abs($this->y), abs($this->z));
	}
	public function getSide(int $side, int $step = 1){
		switch($side){
			case Vector3::SIDE_DOWN:
				return new Vector3($this->x, $this->y - $step, $this->z);
			case Vector3::SIDE_UP:
				return new Vector3($this->x, $this->y + $step, $this->z);
			case Vector3::SIDE_NORTH:
				return new Vector3($this->x, $this->y, $this->z - $step);
			case Vector3::SIDE_SOUTH:
				return new Vector3($this->x, $this->y, $this->z + $step);
			case Vector3::SIDE_WEST:
				return new Vector3($this->x - $step, $this->y, $this->z);
			case Vector3::SIDE_EAST:
				return new Vector3($this->x + $step, $this->y, $this->z);
			default:
				return $this;
		}
	}
	public function down(int $step = 1){
		return $this->getSide(self::SIDE_DOWN, $step);
	}
	public function up(int $step = 1){
		return $this->getSide(self::SIDE_UP, $step);
	}
	public function north(int $step = 1){
		return $this->getSide(self::SIDE_NORTH, $step);
	}
	public function south(int $step = 1){
		return $this->getSide(self::SIDE_SOUTH, $step);
	}
	public function west(int $step = 1){
		return $this->getSide(self::SIDE_WEST, $step);
	}
	public function east(int $step = 1){
		return $this->getSide(self::SIDE_EAST, $step);
	}
	public function asVector3() : Vector3{
		return new Vector3($this->x, $this->y, $this->z);
	}
	public static function getOppositeSide(int $side) : int{
		if($side >= 0 and $side <= 5){
			return $side ^ 0x01;
		}
		throw new InvalidArgumentException("Invalid side $side given to getOppositeSide");
	}
	public function distance(Vector3 $pos) : float{
		return sqrt($this->distanceSquared($pos));
	}
	public function distanceSquared(Vector3 $pos) : float{
		return (($this->x - $pos->x) ** 2) + (($this->y - $pos->y) ** 2) + (($this->z - $pos->z) ** 2);
	}
	public function maxPlainDistance($x = 0, $z = 0) : float{
		if($x instanceof Vector3){
			return $this->maxPlainDistance($x->x, $x->z);
		}elseif($x instanceof Vector2){
			return $this->maxPlainDistance($x->x, $x->y);
		}else{
			return max(abs($this->x - $x), abs($this->z - $z));
		}
	}
	public function length() : float{
		return sqrt($this->lengthSquared());
	}
	public function lengthSquared() : float{
		return $this->x * $this->x + $this->y * $this->y + $this->z * $this->z;
	}
	public function normalize() : Vector3{
		$len = $this->lengthSquared();
		if($len > 0){
			return $this->divide(sqrt($len));
		}
		return new Vector3(0, 0, 0);
	}
	public function dot(Vector3 $v) : float{
		return $this->x * $v->x + $this->y * $v->y + $this->z * $v->z;
	}
	public function cross(Vector3 $v) : Vector3{
		return new Vector3(
			$this->y * $v->z - $this->z * $v->y,
			$this->z * $v->x - $this->x * $v->z,
			$this->x * $v->y - $this->y * $v->x
		);
	}
	public function equals(Vector3 $v) : bool{
		return $this->x == $v->x and $this->y == $v->y and $this->z == $v->z;
	}
	public function getIntermediateWithXValue(Vector3 $v, float $x) : ?Vector3{
		$xDiff = $v->x - $this->x;
		$yDiff = $v->y - $this->y;
		$zDiff = $v->z - $this->z;
		if(($xDiff * $xDiff) < 0.0000001){
			return null;
		}
		$f = ($x - $this->x) / $xDiff;
		if($f < 0 or $f > 1){
			return null;
		}else{
			return new Vector3($x, $this->y + $yDiff * $f, $this->z + $zDiff * $f);
		}
	}
	public function getIntermediateWithYValue(Vector3 $v, float $y) : ?Vector3{
		$xDiff = $v->x - $this->x;
		$yDiff = $v->y - $this->y;
		$zDiff = $v->z - $this->z;
		if(($yDiff * $yDiff) < 0.0000001){
			return null;
		}
		$f = ($y - $this->y) / $yDiff;
		if($f < 0 or $f > 1){
			return null;
		}else{
			return new Vector3($this->x + $xDiff * $f, $y, $this->z + $zDiff * $f);
		}
	}
	public function getIntermediateWithZValue(Vector3 $v, float $z) : ?Vector3{
		$xDiff = $v->x - $this->x;
		$yDiff = $v->y - $this->y;
		$zDiff = $v->z - $this->z;
		if(($zDiff * $zDiff) < 0.0000001){
			return null;
		}
		$f = ($z - $this->z) / $zDiff;
		if($f < 0 or $f > 1){
			return null;
		}else{
			return new Vector3($this->x + $xDiff * $f, $this->y + $yDiff * $f, $z);
		}
	}
	public function setComponents($x, $y, $z){
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		return $this;
	}
	public function __toString(){
		return "Vector3(x=" . $this->x . ",y=" . $this->y . ",z=" . $this->z . ")";
	}
}