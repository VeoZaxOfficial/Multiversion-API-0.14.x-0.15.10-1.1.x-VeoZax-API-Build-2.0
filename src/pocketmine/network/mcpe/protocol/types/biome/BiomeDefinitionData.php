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
namespace pocketmine\network\mcpe\protocol\types\biome;
use pocketmine\utils\Color;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\types\biome\chunkgen\BiomeDefinitionChunkGenData;use function count;
final class BiomeDefinitionData{
	public function __construct(
		private int $nameIndex,
		private ?int $id,
		private float $temperature,
		private float $downfall,
		private float $redSporeDensity,
		private float $blueSporeDensity,
		private float $ashDensity,
		private float $whiteAshDensity,
		private float $depth,
		private float $scale,
		private Color $mapWaterColor,
		private bool $rain,
		private ?array $tagIndexes,
		private ?BiomeDefinitionChunkGenData $chunkGenData = null
	){}
	public function getNameIndex() : int{ return $this->nameIndex; }
	public function getId() : ?int{ return $this->id; }
	public function getTemperature() : float{ return $this->temperature; }
	public function getDownfall() : float{ return $this->downfall; }
	public function getRedSporeDensity() : float{ return $this->redSporeDensity; }
	public function getBlueSporeDensity() : float{ return $this->blueSporeDensity; }
	public function getAshDensity() : float{ return $this->ashDensity; }
	public function getWhiteAshDensity() : float{ return $this->whiteAshDensity; }
	public function getDepth() : float{ return $this->depth; }
	public function getScale() : float{ return $this->scale; }
	public function getMapWaterColor() : Color{ return $this->mapWaterColor; }
	public function hasRain() : bool{ return $this->rain; }
	public function getTagIndexes() : ?array{ return $this->tagIndexes; }
	public function getChunkGenData() : ?BiomeDefinitionChunkGenData{ return $this->chunkGenData; }
	public static function read(NetworkBinaryStream $in) : self{
		$nameIndex = $in->getLShort();
		$id = $in->readOptional($in->getLShort(...));
		$temperature = $in->getLFloat();
		$downfall = $in->getLFloat();
		$redSporeDensity = $in->getLFloat();
		$blueSporeDensity = $in->getLFloat();
		$ashDensity = $in->getLFloat();
		$whiteAshDensity = $in->getLFloat();
		$depth = $in->getLFloat();
		$scale = $in->getLFloat();
		$mapWaterColor = Color::fromARGB($in->getLInt());
		$rain = $in->getBool();
		$tags = $in->readOptional(function() use ($in) : array{
			$tagIndexes = [];
			for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
				$tagIndexes[] = $in->getLShort();
			}
			return $tagIndexes;
		});
		$chunkGenData = $in->readOptional(fn() => BiomeDefinitionChunkGenData::read($in));
		return new self(
			$nameIndex,
			$id,
			$temperature,
			$downfall,
			$redSporeDensity,
			$blueSporeDensity,
			$ashDensity,
			$whiteAshDensity,
			$depth,
			$scale,
			$mapWaterColor,
			$rain,
			$tags,
			$chunkGenData
		);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putLShort($this->nameIndex);
		$out->writeOptional($this->id, $out->putLShort(...));
		$out->putLFloat($this->temperature);
		$out->putLFloat($this->downfall);
		$out->putLFloat($this->redSporeDensity);
		$out->putLFloat($this->blueSporeDensity);
		$out->putLFloat($this->ashDensity);
		$out->putLFloat($this->whiteAshDensity);
		$out->putLFloat($this->depth);
		$out->putLFloat($this->scale);
		$out->putLInt($this->mapWaterColor->toARGB());
		$out->putBool($this->rain);
		$out->writeOptional($this->tagIndexes, function(array $tagIndexes) use ($out) : void{
			$out->putUnsignedVarInt(count($tagIndexes));
			foreach($tagIndexes as $tag){
				$out->putLShort($tag);
			}
		});
		$out->writeOptional($this->chunkGenData, fn(BiomeDefinitionChunkGenData $chunkGenData) => $chunkGenData->write($out));
	}}