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
namespace pocketmine\level\generator\noise\bukkit;
abstract class BaseOctaveGenerator{
	protected array $octaves;
	protected float $x_scale = 1.0;
	protected float $y_scale = 1.0;
	protected float $z_scale = 1.0;
	protected function __construct(array $octaves){
		$this->octaves = $octaves;
	}
	public function setScale(float $scale) : void{
		$this->setXScale($scale);
		$this->setYScale($scale);
		$this->setZScale($scale);
	}
	public function getXScale() : float{
		return $this->x_scale;
	}
	public function setXScale(float $scale) : void{
		$this->x_scale = $scale;
	}
	public function getYScale() : float{
		return $this->y_scale;
	}
	public function setYScale(float $scale) : void{
		$this->y_scale = $scale;
	}
	public function getZScale() : float{
		return $this->z_scale;
	}
	public function setZScale(float $scale) : void{
		$this->z_scale = $scale;
	}
	public function getOctaves() : array{
		$octaves = [];
		foreach($this->octaves as $key => $value){
			$octaves[$key] = clone $value;
		}
		return $octaves;
	}}