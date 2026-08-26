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
final class BiomeOverworldGenRulesData{
	public function __construct(
		private array $hillTransformations,
		private array $mutateTransformations,
		private array $riverTransformations,
		private array $shoreTransformations,
		private array $preHillsEdges,
		private array $postShoreEdges,
		private array $climates,
	){}
	public function getHillTransformations() : array{ return $this->hillTransformations; }
	public function getMutateTransformations() : array{ return $this->mutateTransformations; }
	public function getRiverTransformations() : array{ return $this->riverTransformations; }
	public function getShoreTransformations() : array{ return $this->shoreTransformations; }
	public function getPreHillsEdges() : array{ return $this->preHillsEdges; }
	public function getPostShoreEdges() : array{ return $this->postShoreEdges; }
	public function getClimates() : array{ return $this->climates; }
	public static function read(NetworkBinaryStream $in) : self{
		$hillTransformations = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$hillTransformations[] = BiomeWeightedData::read($in);
		}
		$mutateTransformations = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$mutateTransformations[] = BiomeWeightedData::read($in);
		}
		$riverTransformations = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$riverTransformations[] = BiomeWeightedData::read($in);
		}
		$shoreTransformations = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$shoreTransformations[] = BiomeWeightedData::read($in);
		}
		$preHillsEdges = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$preHillsEdges[] = BiomeConditionalTransformationData::read($in);
		}
		$postShoreEdges = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$postShoreEdges[] = BiomeConditionalTransformationData::read($in);
		}
		$climates = [];
		for($i = 0, $count = $in->getUnsignedVarInt(); $i < $count; ++$i){
			$climates[] = BiomeWeightedTemperatureData::read($in);
		}
		return new self(
			$hillTransformations,
			$mutateTransformations,
			$riverTransformations,
			$shoreTransformations,
			$preHillsEdges,
			$postShoreEdges,
			$climates
		);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putUnsignedVarInt(count($this->hillTransformations));
		foreach($this->hillTransformations as $transformation){
			$transformation->write($out);
		}
		$out->putUnsignedVarInt(count($this->mutateTransformations));
		foreach($this->mutateTransformations as $transformation){
			$transformation->write($out);
		}
		$out->putUnsignedVarInt(count($this->riverTransformations));
		foreach($this->riverTransformations as $transformation){
			$transformation->write($out);
		}
		$out->putUnsignedVarInt(count($this->shoreTransformations));
		foreach($this->shoreTransformations as $transformation){
			$transformation->write($out);
		}
		$out->putUnsignedVarInt(count($this->preHillsEdges));
		foreach($this->preHillsEdges as $edge){
			$edge->write($out);
		}
		$out->putUnsignedVarInt(count($this->postShoreEdges));
		foreach($this->postShoreEdges as $edge){
			$edge->write($out);
		}
		$out->putUnsignedVarInt(count($this->climates));
		foreach($this->climates as $climate){
			$climate->write($out);
		}
	}}