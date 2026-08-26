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
namespace pocketmine\item;
use pocketmine\maps\MapData;use pocketmine\maps\MapManager;use pocketmine\nbt\tag\ByteTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\LongTag;use pocketmine\Player;use function boolval;use function intval;
class Map extends Item{
	public const TAG_MAP_IS_SCALING = "map_is_scaling"; 
	public const TAG_MAP_SCALE = "map_scale"; 
	public const TAG_MAP_UUID = "map_uuid"; 
	public const TAG_MAP_DISPLAY_PLAYERS = "map_display_players"; 
	public const TAG_MAP_NAME_INDEX = "map_name_index"; 
	public const TAG_MAP_IS_INIT = "map_is_init"; 
	public function __construct(int $meta = 0){
		parent::__construct(self::FILLED_MAP, $meta, "Map");
	}
	public function getMapData() : ?MapData{
		return MapManager::getMapDataById($this->getMapId());
	}
	public function onUpdate(Player $player) : void{
	}
	public function initMap(Player $player, int $scale) : void{
		$this->setMapId($id = MapManager::getNextId());
		$this->setMapInit(true);
		$this->setMapNameIndex($id + 1);
		$data = new MapData($id);
		$data->setScale($scale);
		$data->setDimension($player->level->getDimensionId());
		$data->calculateMapCenter($player->getFloorX(), $player->getFloorZ());
		$data->onMapCrated($player);
		MapManager::setMapData($data);
	}
	public function getMaxStackSize() : int{
		return 1;
	}
	public function setMapId(int $mapId) : void{
		$this->setNamedTagEntry(new LongTag(self::TAG_MAP_UUID, $mapId));
	}
	public function getMapId() : int{
		return $this->getNamedTag()->getLong(self::TAG_MAP_UUID, 0, true);
	}
	public function setMapNameIndex(int $nameIndex) : void{
		$this->setNamedTagEntry(new IntTag(self::TAG_MAP_NAME_INDEX, $nameIndex));
	}
	public function getMapNameIndex() : int{
		return $this->getNamedTag()->getInt(self::TAG_MAP_NAME_INDEX, 0, true);
	}
	public function setMapDisplayPlayers(bool $value) : void{
		$this->setNamedTagEntry(new ByteTag(self::TAG_MAP_DISPLAY_PLAYERS, intval($value)));
	}
	public function isMapDisplayPlayers() : bool{
		return boolval($this->getNamedTag()->getByte(self::TAG_MAP_DISPLAY_PLAYERS, 0, true));
	}
	public function setMapInit(bool $value) : void{
		$this->setNamedTagEntry(new ByteTag(self::TAG_MAP_IS_INIT, intval($value)));
	}
	public function isMapInit() : bool{
		return boolval($this->getNamedTag()->getByte(self::TAG_MAP_IS_INIT, 0, true));
	}}