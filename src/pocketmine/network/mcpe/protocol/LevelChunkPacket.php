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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\ChunkPosition;use pocketmine\network\mcpe\protocol\types\DimensionIds;use function count;use function strlen;
class LevelChunkPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LEVEL_CHUNK_PACKET;
	public const ORDER_COLUMNS = 0;
	public const ORDER_LAYERED = 1;
    private $chunkPosition;
    private $order = self::ORDER_COLUMNS;
	private $dimensionId;
	private $subChunkCount;
	private $cacheEnabled;
	private $usedBlobHashes = [];
	private $extraPayload;
    private const MAX_BLOB_HASHES = 64;
	public static function withoutCache(ChunkPosition $chunkPosition, int $order, int $dimensionId, int $subChunkCount, string $payload) : self{
		$result = new self;
		$result->chunkPosition = $chunkPosition;
		$result->order = $order;
		$result->dimensionId = $dimensionId;
		$result->subChunkCount = $subChunkCount;
		$result->extraPayload = $payload;
		$result->cacheEnabled = false;
		return $result;
	}
	public static function withCache(ChunkPosition $chunkPosition, int $order, int $dimensionId, int $subChunkCount, array $usedBlobHashes, string $extraPayload) : self{
		(static function(int ...$hashes){})(...$usedBlobHashes);
		$result = new self;
		$result->chunkPosition = $chunkPosition;
		$result->order = $order;
		$result->dimensionId = $dimensionId;
		$result->subChunkCount = $subChunkCount;
		$result->extraPayload = $extraPayload;
		$result->cacheEnabled = true;
		$result->usedBlobHashes = $usedBlobHashes;
		return $result;
	}
    public function getChunkPosition() : ChunkPosition{
        return $this->chunkPosition;
    }
	public function getOrder() : int{
		return $this->order;
	}
	public function getDimensionId() : int{
		return $this->dimensionId;
	}
	public function getSubChunkCount() : int{
		return $this->subChunkCount;
	}
	public function isCacheEnabled() : bool{
		return $this->cacheEnabled;
	}
	public function getUsedBlobHashes() : array{
		return $this->usedBlobHashes;
	}
	public function getExtraPayload() : string{
		return $this->extraPayload;
	}
	protected function decodePayload() : void{
	    $this->chunkPosition = ChunkPosition::read($this);
	    if($this->getProtocol() < ProtocolInfo::PROTOCOL_92){
	        $this->order = $this->getByte();
	    }
		
		$this->extraPayload = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->get($this->getInt());
	}
	protected function encodePayload() : void{
	    $this->chunkPosition->write($this);
	    if($this->getProtocol() < ProtocolInfo::PROTOCOL_92){
	        $this->putByte($this->order);
	    }
		
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putString($this->extraPayload);
		}else{
		    $this->putInt(strlen($this->extraPayload));
		    $this->put($this->extraPayload);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleLevelChunk($this);
	}}