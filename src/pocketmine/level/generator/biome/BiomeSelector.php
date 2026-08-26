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

namespace pocketmine\level\generator\biome;
use pocketmine\level\biome\Biome;use pocketmine\level\generator\noise\Simplex;use pocketmine\utils\Random;
class BiomeSelector{
	private $fallback;
	private $temperature;
	private $rainfall;
	private $river;
	private $ocean;
	private $hills;
	private $biomes = [];
	private $map = [];
	private $lookup;
	public function __construct(Random $random, callable $lookup, Biome $fallback){
		$this->fallback = $fallback;
		$this->lookup = $lookup;
		$this->temperature = new Simplex($random, 2, 1 / 8, 1 / 2048);
		$this->rainfall = new Simplex($random, 2, 1 / 8, 1 / 2048);
		$this->river = new Simplex($random, 6, 1 / 2, 1 / 1024);
		$this->ocean = new Simplex($random, 6, 1 / 2, 1 / 2048);
		$this->hills = new Simplex($random, 2, 1 / 2, 1 / 2048);
	}
	public function recalculate(){
	}
	public function addBiome(Biome $biome){
		$this->biomes[$biome->getId()] = $biome;
	}
	public function getTemperature($x, $z){
		return $this->temperature->noise2D($x, $z, true);
	}
	public function getRainfall($x, $z){
		return $this->rainfall->noise2D($x, $z, true);
	}
	public function getRiver($x, $z){
		return $this->river->noise2D($x, $z, true);
	}
	public function getOcean($x, $z){
		return $this->ocean->noise2D($x, $z, true);
	}
	public function getHills($x, $z){
		return $this->hills->noise2D($x, $z, true);
	}
	public function pickBiome($x, $z){
		$temperature = $this->getTemperature($x, $z);
		$rainfall = $this->getRainfall($x, $z);
		$River = $this->getRiver($x, $z);
		$ocean = $this->getOcean($x, $z);
		$hills = $this->getHills($x, $z);
		$biomeId = call_user_func($this->lookup, $temperature, $rainfall, $River, $ocean, $hills);
		return isset($this->biomes[$biomeId]) ? $this->biomes[$biomeId] : $this->fallback;
	}}