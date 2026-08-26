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
final class BiomeMultinoiseGenRulesData{
	public function __construct(
		private float $temperature,
		private float $humidity,
		private float $altitude,
		private float $weirdness,
		private float $weight,
	){}
	public function getTemperature() : float{ return $this->temperature; }
	public function getHumidity() : float{ return $this->humidity; }
	public function getAltitude() : float{ return $this->altitude; }
	public function getWeirdness() : float{ return $this->weirdness; }
	public function getWeight() : float{ return $this->weight; }
	public static function read(NetworkBinaryStream $in) : self{
		$temperature = $in->getLFloat();
		$humidity = $in->getLFloat();
		$altitude = $in->getLFloat();
		$weirdness = $in->getLFloat();
		$weight = $in->getLFloat();
		return new self(
			$temperature,
			$humidity,
			$altitude,
			$weirdness,
			$weight
		);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putLFloat($this->temperature);
		$out->putLFloat($this->humidity);
		$out->putLFloat($this->altitude);
		$out->putLFloat($this->weirdness);
		$out->putLFloat($this->weight);
	}}