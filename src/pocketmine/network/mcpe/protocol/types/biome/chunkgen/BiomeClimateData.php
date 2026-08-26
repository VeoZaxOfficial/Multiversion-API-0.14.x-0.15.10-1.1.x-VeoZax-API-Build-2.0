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
namespace pocketmine\network\mcpe\protocol\types\biome\chunkgen;
use pocketmine\network\mcpe\NetworkBinaryStream;
final class BiomeClimateData{
	public function __construct(
		private float $temperature,
		private float $downfall,
		private float $redSporeDensity,
		private float $blueSporeDensity,
		private float $ashDensity,
		private float $whiteAshDensity,
		private float $snowAccumulationMin,
		private float $snowAccumulationMax,
	){}
	public function getTemperature() : float{ return $this->temperature; }
	public function getDownfall() : float{ return $this->downfall; }
	public function getRedSporeDensity() : float{ return $this->redSporeDensity; }
	public function getBlueSporeDensity() : float{ return $this->blueSporeDensity; }
	public function getAshDensity() : float{ return $this->ashDensity; }
	public function getWhiteAshDensity() : float{ return $this->whiteAshDensity; }
	public function getSnowAccumulationMin() : float{ return $this->snowAccumulationMin; }
	public function getSnowAccumulationMax() : float{ return $this->snowAccumulationMax; }
	public static function read(NetworkBinaryStream $in) : self{
		$temperature = $in->getLFloat();
		$downfall = $in->getLFloat();
		$redSporeDensity = $in->getLFloat();
		$blueSporeDensity = $in->getLFloat();
		$ashDensity = $in->getLFloat();
		$whiteAshDensity = $in->getLFloat();
		$snowAccumulationMin = $in->getLFloat();
		$snowAccumulationMax = $in->getLFloat();
		return new self(
			$temperature,
			$downfall,
			$redSporeDensity,
			$blueSporeDensity,
			$ashDensity,
			$whiteAshDensity,
			$snowAccumulationMin,
			$snowAccumulationMax
		);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putLFloat($this->temperature);
		$out->putLFloat($this->downfall);
		$out->putLFloat($this->redSporeDensity);
		$out->putLFloat($this->blueSporeDensity);
		$out->putLFloat($this->ashDensity);
		$out->putLFloat($this->whiteAshDensity);
		$out->putLFloat($this->snowAccumulationMin);
		$out->putFloat($this->snowAccumulationMax);
	}}