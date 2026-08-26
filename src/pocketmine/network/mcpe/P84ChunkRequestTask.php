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
namespace pocketmine\network\mcpe;
use pocketmine\block\BlockFactory;use pocketmine\level\format\Chunk;use pocketmine\level\Level;use pocketmine\network\mcpe\chunk\ChunkConverter;use pocketmine\network\mcpe\protocol\legacy\p84\BatchPacket as P84BatchPacket;use pocketmine\network\mcpe\protocol\legacy\p84\FullChunkDataPacket as P84FullChunkDataPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;use pocketmine\tile\Spawnable;use function pack;use function strlen;use function zlib_encode;use const ZLIB_ENCODING_DEFLATE;
class P84ChunkRequestTask extends AsyncTask{
	private $levelId;
	private $chunk;
	private $chunkX;
	private $chunkZ;
	private $tiles;
	private $compressionLevel;
	public function __construct(Level $level, Player $player, int $chunkX, int $chunkZ, Chunk $chunk){
		$this->levelId = $level->getId();
		$this->chunkX = $chunkX;
		$this->chunkZ = $chunkZ;
		$this->compressionLevel = $level->getServer()->networkCompressionLevel;
		$tiles = "";
		foreach($chunk->getTiles() as $tile){
			if($tile instanceof Spawnable){
				$tiles .= $tile->getProtocolSerializedSpawnCompound(ProtocolInfo::PROTOCOL_81);
			}
		}
		$this->tiles = $tiles;
		$this->chunk = $chunk->fastSerialize();
		$this->storeLocal($player);
	}
	public function onRun() : void{
		BlockFactory::init();
		$chunk = Chunk::fastDeserialize($this->chunk);
		$data = ChunkConverter::buildLegacyChunkPayload($chunk) . $this->tiles;
		$p84Pk = new P84FullChunkDataPacket();
		$p84Pk->chunkX = $this->chunkX;
		$p84Pk->chunkZ = $this->chunkZ;
		$p84Pk->order = P84FullChunkDataPacket::ORDER_COLUMNS;
		$p84Pk->data  = $data;
		$p84Pk->encode();
		$p84Batch = new P84BatchPacket();
		$p84Batch->payload = zlib_encode(
			pack('N', strlen($p84Pk->buffer)) . $p84Pk->buffer,
			ZLIB_ENCODING_DEFLATE,
			$this->compressionLevel
		);
		$p84Batch->encode();
		$this->setResult($p84Batch->buffer);
	}
	public function onCompletion(Server $server) : void{
		$player = $this->fetchLocal();
		if($player === null || !$player->isConnected()){
			return;
		}
		$level = $server->getLevel($this->levelId);
		if(!($level instanceof Level) || $player->getLevel() !== $level){
			return;
		}
		if(!$this->hasResult()){
			$server->getLogger()->error("Protocol 84 chunk request for world #" . $this->levelId . ", x=" . $this->chunkX . ", z=" . $this->chunkZ . " doesn't have any result data");
			return;
		}
		$player->finishP84ChunkSend($this->chunkX, $this->chunkZ, $this->getResult());
	}}