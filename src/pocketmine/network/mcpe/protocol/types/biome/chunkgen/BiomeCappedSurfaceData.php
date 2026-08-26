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
use pocketmine\network\mcpe\NetworkBinaryStream;use function count;
final class BiomeCappedSurfaceData{
	public function __construct(
		private array $floorBlocks,
		private array $ceilingBlocks,
		private ?int $seaBlock,
		private ?int $foundationBlock,
		private ?int $beachBlock,
	){}
	public function getFloorBlocks() : array{ return $this->floorBlocks; }
	public function getCeilingBlocks() : array{ return $this->ceilingBlocks; }
	public function getSeaBlock() : ?int{ return $this->seaBlock; }
	public function getFoundationBlock() : ?int{ return $this->foundationBlock; }
	public function getBeachBlock() : ?int{ return $this->beachBlock; }
	public static function read(NetworkBinaryStream $in) : self{
		$floorBlocks = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$floorBlocks[] = $in->getLInt();
		}
		$ceilingBlocks = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$ceilingBlocks[] = $in->getLInt();
		}
		$seaBlock = $in->readOptional($in->getLInt(...));
		$foundationBlock = $in->readOptional($in->getLInt(...));
		$beachBlock = $in->readOptional($in->getLInt(...));
		return new self(
			$floorBlocks,
			$ceilingBlocks,
			$seaBlock,
			$foundationBlock,
			$beachBlock
		);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putUnsignedVarInt(count($this->floorBlocks));
		foreach($this->floorBlocks as $block){
			$out->putLInt($block);
		}
		$out->putUnsignedVarInt(count($this->ceilingBlocks));
		foreach($this->ceilingBlocks as $block){
			$out->putLInt($block);
		}
		$out->writeOptional($this->seaBlock, $out->putLInt(...));
		$out->writeOptional($this->foundationBlock, $out->putLInt(...));
		$out->writeOptional($this->beachBlock, $out->putLInt(...));
	}}