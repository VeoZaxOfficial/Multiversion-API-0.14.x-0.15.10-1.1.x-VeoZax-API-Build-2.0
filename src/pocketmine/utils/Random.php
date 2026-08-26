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
use function time;
class Random{
	public const X = 123456789;
	public const Y = 362436069;
	public const Z = 521288629;
	public const W = 88675123;
	private $x;
	private $y;
	private $z;
	private $w;
	protected $seed;
	public function __construct(int $seed = -1){
		if($seed === -1){
			$seed = time();
		}
		$this->setSeed($seed);
	}
	public function setSeed(int $seed){
		$this->seed = $seed;
		$this->x = self::X ^ $seed;
		$this->y = self::Y ^ ($seed << 17) | (($seed >> 15) & 0x7fffffff) & 0xffffffff;
		$this->z = self::Z ^ ($seed << 31) | (($seed >> 1) & 0x7fffffff) & 0xffffffff;
		$this->w = self::W ^ ($seed << 18) | (($seed >> 14) & 0x7fffffff) & 0xffffffff;
	}
	public function getSeed() : int{
		return $this->seed;
	}
	public function nextInt() : int{
		return $this->nextSignedInt() & 0x7fffffff;
	}
	public function nextSignedInt() : int{
		$t = ($this->x ^ ($this->x << 11)) & 0xffffffff;
		$this->x = $this->y;
		$this->y = $this->z;
		$this->z = $this->w;
		$this->w = ($this->w ^ (($this->w >> 19) & 0x7fffffff)
		                     ^ ($t ^ (($t >> 8) & 0x7fffffff))) & 0xffffffff;
		return $this->w;
	}
	public function nextFloat() : float{
		return $this->nextInt() / 0x7fffffff;
	}
	public function nextSignedFloat() : float{
		return $this->nextSignedInt() / 0x7fffffff;
	}
	public function nextBoolean() : bool{
		return ($this->nextSignedInt() & 0x01) === 0;
	}
	public function nextRange(int $start = 0, int $end = 0x7fffffff) : int{
		return $start + ($this->nextInt() % ($end + 1 - $start));
	}
	public function nextBoundedInt(int $bound) : int{
		return $this->nextInt() % $bound;
	}}