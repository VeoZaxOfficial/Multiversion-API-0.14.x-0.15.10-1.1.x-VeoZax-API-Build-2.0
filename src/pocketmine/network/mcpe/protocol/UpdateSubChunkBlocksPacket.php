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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\UpdateSubChunkBlocksPacketEntry;use function count;
class UpdateSubChunkBlocksPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_SUB_CHUNK_BLOCKS_PACKET;
	private int $subChunkX;
	private int $subChunkY;
	private int $subChunkZ;
	private array $layer0Updates;
	private array $layer1Updates;
	public static function create(int $subChunkX, int $subChunkY, int $subChunkZ, array $layer0, array $layer1) : self{
		$result = new self;
		$result->subChunkX = $subChunkX;
		$result->subChunkY = $subChunkY;
		$result->subChunkZ = $subChunkZ;
		$result->layer0Updates = $layer0;
		$result->layer1Updates = $layer1;
		return $result;
	}
	public function getSubChunkX() : int{ return $this->subChunkX; }
	public function getSubChunkY() : int{ return $this->subChunkY; }
	public function getSubChunkZ() : int{ return $this->subChunkZ; }
	public function getLayer0Updates() : array{ return $this->layer0Updates; }
	public function getLayer1Updates() : array{ return $this->layer1Updates; }
	protected function decodePayload() : void{
		$this->subChunkX = $this->subChunkY = $this->subChunkZ = 0;
		$this->getBlockPosition($this->subChunkX, $this->subChunkY, $this->subChunkZ);
		$this->layer0Updates = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->layer0Updates[] = UpdateSubChunkBlocksPacketEntry::read($this);
		}
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->layer1Updates[] = UpdateSubChunkBlocksPacketEntry::read($this);
		}
	}
	protected function encodePayload() : void{
		$this->putBlockPosition($this->subChunkX, $this->subChunkY, $this->subChunkZ);
		$this->putUnsignedVarInt(count($this->layer0Updates));
		foreach($this->layer0Updates as $update){
			$update->write($this);
		}
		$this->putUnsignedVarInt(count($this->layer1Updates));
		foreach($this->layer1Updates as $update){
			$update->write($this);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateSubChunkBlocks($this);
	}}