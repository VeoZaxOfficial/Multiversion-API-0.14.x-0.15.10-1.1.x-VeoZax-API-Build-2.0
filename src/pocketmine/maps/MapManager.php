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
namespace pocketmine\maps;
use pocketmine\nbt\BigEndianNBTStream;use pocketmine\nbt\LittleEndianNBTStream;use pocketmine\nbt\tag\CompoundTag;use pocketmine\Server;use function file_get_contents;use function file_put_contents;use function is_file;use function mkdir;
class MapManager{
	protected static $maps = [];
	protected static $mapIdCounter = 0;
	private function __construct(){
	}
	public static function setMapData(MapData $map) : void{
		self::$maps[$map->getId()] = $map;
	}
	public static function unsetMapData(int $mapId) : void{
		unset(self::$maps[$mapId]);
	}
	public static function getMapDataById(int $id) : ?MapData{
		if(!isset(self::$maps[$id])){
			self::loadMapData($id);
		}
		return self::$maps[$id] ?? null;
	}
	public static function getNextId() : int{
		return self::$mapIdCounter++;
	}
	public static function loadIdCounts() : void{
		$path = Server::getInstance()->getDataPath() . "maps/";
		if(!is_dir($path)){
			return;
		}
		$stream = new LittleEndianNBTStream();
		if(is_file($path . "idcounts.dat")){
			$data = $stream->read(file_get_contents($path . "idcounts.dat"));
			self::$mapIdCounter = $data->getInt("map", 0);
		}
	}
	public static function loadMapData(int $id) : void{
		@mkdir($path = Server::getInstance()->getDataPath() . "maps/");
		$stream = new BigEndianNBTStream();
		if(is_file($fp = $path . "map_" . strval($id) . ".dat")){
			$data = $stream->readCompressed(file_get_contents($fp));
			$mp = new MapData($id);
			$mp->readSaveData($data);
			self::setMapData($mp);
		}
	}
	public static function saveMaps() : void{
		if(self::$mapIdCounter === 0 and count(self::$maps) === 0){
			return;
		}
		$path = Server::getInstance()->getDataPath() . "maps/";
		@mkdir($path, 0777);
		$stream = new LittleEndianNBTStream();
		$idcounts = new CompoundTag();
		$idcounts->setInt("map", self::$mapIdCounter);
		file_put_contents($path . "idcounts.dat", $stream->write($idcounts));
		$stream = new BigEndianNBTStream();
		foreach(self::$maps as $data){
			if(!$data->isVirtual() and $data->isDirty()){
				$tag = new CompoundTag("data");
				$data->writeSaveData($tag);
				file_put_contents($path . "map_" . strval($data->getId()) . ".dat", $stream->writeCompressed($tag));
			}
		}
	}
	public static function resetMaps() : void{
		self::$maps = [];
	}}