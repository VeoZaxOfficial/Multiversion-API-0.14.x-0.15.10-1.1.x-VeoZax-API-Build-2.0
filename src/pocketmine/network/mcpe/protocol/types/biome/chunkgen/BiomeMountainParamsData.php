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
final class BiomeMountainParamsData{
	public function __construct(
		private int $steepBlock,
		private bool $northSlopes,
		private bool $southSlopes,
		private bool $westSlopes,
		private bool $eastSlopes,
		private bool $topSlideEnabled,
	){}
	public function getSteepBlock() : int{ return $this->steepBlock; }
	public function hasNorthSlopes() : bool{ return $this->northSlopes; }
	public function hasSouthSlopes() : bool{ return $this->southSlopes; }
	public function hasWestSlopes() : bool{ return $this->westSlopes; }
	public function hasEastSlopes() : bool{ return $this->eastSlopes; }
	public function hasTopSlideEnabled() : bool{ return $this->topSlideEnabled; }
	public static function read(NetworkBinaryStream $in) : self{
		$steepBlock = $in->getLInt();
		$northSlopes = $in->getBool();
		$southSlopes = $in->getBool();
		$westSlopes = $in->getBool();
		$eastSlopes = $in->getBool();
		$topSlideEnabled = $in->getBool();
		return new self(
			$steepBlock,
			$northSlopes,
			$southSlopes,
			$westSlopes,
			$eastSlopes,
			$topSlideEnabled
		);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putLInt($this->steepBlock);
		$out->putBool($this->northSlopes);
		$out->putBool($this->southSlopes);
		$out->putBool($this->westSlopes);
		$out->putBool($this->eastSlopes);
		$out->putBool($this->topSlideEnabled);
	}}