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
final class BiomeCoordinateData{
	public function __construct(
		private int $minValueType,
		private int $minValue,
		private int $maxValueType,
		private int $maxValue,
		private int $gridOffset,
		private int $gridStepSize,
		private int $distribution
	){}
	public function getMinValueType() : int{ return $this->minValueType; }
	public function getMinValue() : int{ return $this->minValue; }
	public function getMaxValueType() : int{ return $this->maxValueType; }
	public function getMaxValue() : int{ return $this->maxValue; }
	public function getGridOffset() : int{ return $this->gridOffset; }
	public function getGridStepSize() : int{ return $this->gridStepSize; }
	public function getDistribution() : int{ return $this->distribution; }
	public static function read(NetworkBinaryStream $in) : self{
		$minValueType = $in->getVarInt();
		$minValue = $in->getLShort();
		$maxValueType = $in->getVarInt();
		$maxValue = $in->getLShort();
		$gridOffset = $in->getLInt();
		$gridStepSize = $in->getLInt();
		$distribution = $in->getVarInt();
		return new self(
			$minValueType,
			$minValue,
			$maxValueType,
			$maxValue,
			$gridOffset,
			$gridStepSize,
			$distribution
		);
	}
	public function write(NetworkBinaryStream $out) : void{
	}}