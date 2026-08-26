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
use function abs;use function cos;use function sin;use function ceil;use function floor;use function round;use function sqrt;
class Vector2{
	public $x;
	public $y;
	public function __construct(float $x = 0, float $y = 0){
		$this->x = $x;
		$this->y = $y;
	}
	public function getX() : float{
		return $this->x;
	}
	public function getY() : float{
		return $this->y;
	}
	public function getFloorX() : int{
		return (int) floor($this->x);
	}
	public function getFloorY() : int{
		return (int) floor($this->y);
	}
	public function add($x, float $y = 0) : Vector2{
		if($x instanceof Vector2){
			return $this->add($x->x, $x->y);
		}else{
			return new Vector2($this->x + $x, $this->y + $y);
		}
	}
	public function subtract($x, float $y = 0) : Vector2{
		if($x instanceof Vector2){
			return $this->add(-$x->x, -$x->y);
		}else{
			return $this->add(-$x, -$y);
		}
	}
	public function ceil() : Vector2{
		return new Vector2((int) ceil($this->x), (int) ceil($this->y));
	}
	public function floor() : Vector2{
		return new Vector2((int) floor($this->x), (int) floor($this->y));
	}
	public function round() : Vector2{
		return new Vector2(round($this->x), round($this->y));
	}
	public function abs() : Vector2{
		return new Vector2(abs($this->x), abs($this->y));
	}
	public function multiply(float $number) : Vector2{
		return new Vector2($this->x * $number, $this->y * $number);
	}
	public function divide(float $number) : Vector2{
		return new Vector2($this->x / $number, $this->y / $number);
	}
	public function distance($x, float $y = 0) : float{
		if($x instanceof Vector2){
			return sqrt($this->distanceSquared($x->x, $x->y));
		}else{
			return sqrt($this->distanceSquared($x, $y));
		}
	}
	public function distanceSquared($x, float $y = 0) : float{
		if($x instanceof Vector2){
			return $this->distanceSquared($x->x, $x->y);
		}else{
			return (($this->x - $x) ** 2) + (($this->y - $y) ** 2);
		}
	}
	public function length() : float{
		return sqrt($this->lengthSquared());
	}
	public function lengthSquared() : float{
		return $this->x * $this->x + $this->y * $this->y;
	}
	public function normalize() : Vector2{
		$len = $this->lengthSquared();
		if($len > 0){
			return $this->divide(sqrt($len));
		}
		return new Vector2(0, 0);
	}
	public function dot(Vector2 $v) : float{
		return $this->x * $v->x + $this->y * $v->y;
	}
	public static function createRandomDirection(\pocketmine\utils\Random $random) : Vector2{
		$angle = $random->nextFloat() * 2 * M_PI;
		return new Vector2(cos($angle), sin($angle));
	}
	public function __toString(){
		return "Vector2(x=" . $this->x . ",y=" . $this->y . ")";
	}
}