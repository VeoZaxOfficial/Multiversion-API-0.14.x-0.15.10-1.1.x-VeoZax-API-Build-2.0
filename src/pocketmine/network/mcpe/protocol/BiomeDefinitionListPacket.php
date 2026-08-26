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
namespace pocketmine\network\mcpe\protocol;
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\biome\BiomeDefinitionData;use pocketmine\network\mcpe\protocol\types\biome\BiomeDefinitionEntry;use InvalidArgumentException;use function array_map;use function count;
class BiomeDefinitionListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BIOME_DEFINITION_LIST_PACKET;
	private string $namedtag;
	private array $definitionData = [];
	private array $strings = [];
	public static function create(string $namedtag, array $definitionData = [], array $strings = []) : self{
		$result = new self;
		$result->namedtag = $namedtag;
		$result->definitionData = $definitionData;
		$result->strings = $strings;
		return $result;
	}
	public static function fromDefinitions(array $definitions) : self{
		$stringIndexLookup = [];
		$strings = [];
		$addString = function(string $string) use (&$stringIndexLookup, &$strings) : int{
			if(isset($stringIndexLookup[$string])){
				return $stringIndexLookup[$string];
			}
			$stringIndexLookup[$string] = count($stringIndexLookup);
			$strings[] = $string;
			return $stringIndexLookup[$string];
		};
		$definitionData = array_map(fn(BiomeDefinitionEntry $entry) => new BiomeDefinitionData(
			$addString($entry->getBiomeName()),
			$entry->getId(),
			$entry->getTemperature(),
			$entry->getDownfall(),
			$entry->getRedSporeDensity(),
			$entry->getBlueSporeDensity(),
			$entry->getAshDensity(),
			$entry->getWhiteAshDensity(),
			$entry->getDepth(),
			$entry->getScale(),
			$entry->getMapWaterColor(),
			$entry->hasRain(),
			$entry->getTags() === null ? null : array_map($addString, $entry->getTags()),
			$entry->getChunkGenData(),
		), $definitions);
		return self::create("", $definitionData, $strings);
	}
	private function locateString(int $index) : string{
		return $this->strings[$index] ?? throw new InvalidArgumentException("Unknown string index $index");
	}
	public function buildDefinitionsFromData() : array{
		return array_map(fn(BiomeDefinitionData $data) => new BiomeDefinitionEntry(
			$this->locateString($data->getNameIndex()),
			$data->getId(),
			$data->getTemperature(),
			$data->getDownfall(),
			$data->getRedSporeDensity(),
			$data->getBlueSporeDensity(),
			$data->getAshDensity(),
			$data->getWhiteAshDensity(),
			$data->getDepth(),
			$data->getScale(),
			$data->getMapWaterColor(),
			$data->hasRain(),
			($tagIndexes = $data->getTagIndexes()) === null ? null : array_map($this->locateString(...), $tagIndexes),
			$data->getChunkGenData(),
		), $this->definitionData);
	}
	protected function decodePayload(){
		
		    $this->namedtag = $this->getRemaining();
		
	}
	protected function encodePayload(){
		
		    $this->put($this->namedtag);
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleBiomeDefinitionList($this);
	}}