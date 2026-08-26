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
use pocketmine\block\BlockFactory;use pocketmine\level\format\Chunk;use pocketmine\level\Level;use pocketmine\network\mcpe\chunk\ChunkConverter;use pocketmine\network\mcpe\protocol\legacy\p70\BatchPacket as LegBatchPacket;use pocketmine\network\mcpe\protocol\legacy\p70\FullChunkDataPacket as LegFullChunkDataPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;use pocketmine\tile\Spawnable;use function pack;use function strlen;use function zlib_encode;use const ZLIB_ENCODING_DEFLATE;
class LegacyChunkRequestTask extends AsyncTask{
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
				$tiles .= $tile->getProtocolSerializedSpawnCompound(ProtocolInfo::PROTOCOL_70);
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
		$legPk = new LegFullChunkDataPacket();
		$legPk->chunkX = $this->chunkX;
		$legPk->chunkZ = $this->chunkZ;
		$legPk->order  = LegFullChunkDataPacket::ORDER_COLUMNS;
		$legPk->data   = $data;
		$legPk->encode();
		$legBatch = new LegBatchPacket();
		$legBatch->payload = zlib_encode(
			pack('N', strlen($legPk->buffer)) . $legPk->buffer,
			ZLIB_ENCODING_DEFLATE,
			$this->compressionLevel
		);
		$legBatch->encode();
		$this->setResult($legBatch->buffer);
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
			$server->getLogger()->error("Legacy chunk request for world #" . $this->levelId . ", x=" . $this->chunkX . ", z=" . $this->chunkZ . " doesn't have any result data");
			return;
		}
		$player->finishLegacyChunkSend($this->chunkX, $this->chunkZ, $this->getResult());
	}}