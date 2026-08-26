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
namespace pocketmine\tile;
use pocketmine\level\Level;use pocketmine\nbt\NetworkLittleEndianNBTStream;use pocketmine\nbt\LittleEndianNBTStream;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\StringTag;use pocketmine\network\mcpe\protocol\BlockActorDataPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;
abstract class Spawnable extends Tile{
	private $spawnCompoundCache = null;
	private $oldSpawnCompoundCache = null;
	private static $nbtWriter = null;
	private static $oldNbtWriter = null;
	public function createSpawnPacket() : BlockActorDataPacket{
		$pk = new BlockActorDataPacket();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->namedtag = $this->getSerializedSpawnCompound();
		return $pk;
	}
	public function spawnTo(Player $player) : bool{
		if($this->closed){
			return false;
		}
		$pk = new BlockActorDataPacket();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->namedtag = $this->getProtocolSerializedSpawnCompound($player->getProtocol());
		$player->dataPacket($pk);
		return true;
	}
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->spawnToAll();
	}
	public function spawnToAll(){
		if($this->closed){
			return;
		}
		$viewers = $this->level->getViewersForPosition($this);
		foreach($viewers as $viewer){
			$this->spawnTo($viewer);
		}
	}
	protected function onChanged() : void{
		$this->spawnCompoundCache = null;
		$this->oldSpawnCompoundCache = null;
		$this->spawnToAll();
		$this->level->clearChunkCache($this->getFloorX() >> 4, $this->getFloorZ() >> 4);
	}
	final public function getSerializedSpawnCompound() : string{
		if($this->spawnCompoundCache === null){
			if(self::$nbtWriter === null){
				self::$nbtWriter = new NetworkLittleEndianNBTStream();
			}
			$this->spawnCompoundCache = self::$nbtWriter->write($this->getSpawnCompound());
		}
		return $this->spawnCompoundCache;
	}
	final public function getSpawnCompound() : CompoundTag{
		$nbt = new CompoundTag("", [
			new StringTag(self::TAG_ID, static::getSaveId()),
			new IntTag(self::TAG_X, $this->x),
			new IntTag(self::TAG_Y, $this->y),
			new IntTag(self::TAG_Z, $this->z)
		]);
		$this->addAdditionalSpawnData($nbt);
		return $nbt;
	}
	public function getProtocolSerializedSpawnCompound(int $playerProtocol) : string{
	    if($playerProtocol < ProtocolInfo::PROTOCOL_90){
			if(self::$oldNbtWriter === null){
				self::$oldNbtWriter = new LittleEndianNBTStream();
			}
			return $this->oldSpawnCompoundCache = self::$oldNbtWriter->write($this->getSpawnCompound());
	    }
		return $this->getSerializedSpawnCompound();
	}
	abstract protected function addAdditionalSpawnData(CompoundTag $nbt) : void;
	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
		return false;
	}}