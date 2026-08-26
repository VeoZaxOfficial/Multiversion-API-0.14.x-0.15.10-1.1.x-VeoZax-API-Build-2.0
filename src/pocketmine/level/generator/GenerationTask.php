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

namespace pocketmine\level\generator;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;use pocketmine\level\SimpleChunkManager;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;
class GenerationTask extends AsyncTask{
	public $state;
	public $levelId;
	public $chunk;
	public $chunkClass;
	public function __construct(Level $level, Chunk $chunk){
		$this->state = true;
		$this->levelId = $level->getId();
		$this->chunk = $chunk->fastSerialize();
		$this->chunkClass = get_class($chunk);
	}
	public function onRun() : void{
		$managerContext = ThreadLocalManagerContext::fetch($this->levelId);
		$generatorContext = ThreadLocalGeneratorContext::fetch($this->levelId);
		if($managerContext === null or $generatorContext === null){
			$this->state = false;
			return;
		}
		$manager = $managerContext->getManager();
		$generator = $generatorContext->getGenerator();
		$chunk = $this->chunkClass;
		$chunk = $chunk::fastDeserialize($this->chunk);
		if($chunk === null){
			return;
		}
		$manager->setChunk($chunk->getX(), $chunk->getZ(), $chunk);
		$generator->generateChunk($chunk->getX(), $chunk->getZ());
		$chunk = $manager->getChunk($chunk->getX(), $chunk->getZ());
		$chunk->setGenerated();
		$this->chunk = $chunk->fastSerialize();
		$manager->setChunk($chunk->getX(), $chunk->getZ(), null);
	}
	public function onCompletion(Server $server){
		$level = $server->getLevel($this->levelId);
		if($level !== null){
			if($this->state === false){
				$level->registerGenerator();
				return;
			}
			$chunk = $this->chunkClass;
			$chunk = $chunk::fastDeserialize($this->chunk);
			if($chunk === null){
				return;
			}
			$level->generateChunkCallback($chunk->getX(), $chunk->getZ(), $chunk);
		}
	}}