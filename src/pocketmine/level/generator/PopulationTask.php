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
namespace pocketmine\level\generator;
use pocketmine\level\format\Chunk;use pocketmine\level\Level;use pocketmine\level\SimpleChunkManager;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;
class PopulationTask extends AsyncTask{
	public $state;
	public $levelId;
	public $chunk;
	public $chunk0;
	public $chunk1;
	public $chunk2;
	public $chunk3;
	public $chunk5;
	public $chunk6;
	public $chunk7;
	public $chunk8;
	public function __construct(Level $level, Chunk $chunk){
		$this->state = true;
		$this->levelId = $level->getId();
		$this->chunk = $chunk->fastSerialize();
		foreach($level->getAdjacentChunks($chunk->getX(), $chunk->getZ()) as $i => $c){
			$this->{"chunk$i"} = $c !== null ? $c->fastSerialize() : null;
		}
	}
	public function onRun() : void{
        $managerContext = ThreadLocalGeneratorContext::fetch($this->levelId);
        $generatorContext = ThreadLocalManagerContext::fetch($this->levelId);
        if($managerContext === null or $generatorContext === null){
            $this->state = false;
            return;
        }
        $manager = $generatorContext->getManager();
        $generator = $managerContext->getGenerator();
		$chunks = [];
		$chunk = Chunk::fastDeserialize($this->chunk);
		for($i = 0; $i < 9; ++$i){
			if($i === 4){
				continue;
			}
			$xx = -1 + $i % 3;
			$zz = -1 + (int) ($i / 3);
			$ck = $this->{"chunk$i"};
			if($ck === null){
				$chunks[$i] = new Chunk($chunk->getX() + $xx, $chunk->getZ() + $zz);
			}else{
				$chunks[$i] = Chunk::fastDeserialize($ck);
			}
		}
		$manager->setChunk($chunk->getX(), $chunk->getZ(), $chunk);
		if(!$chunk->isGenerated()){
			$generator->generateChunk($chunk->getX(), $chunk->getZ());
			$chunk->setGenerated();
		}
		foreach($chunks as $c){
			if($c !== null){
				$manager->setChunk($c->getX(), $c->getZ(), $c);
				if(!$c->isGenerated()){
					$generator->generateChunk($c->getX(), $c->getZ());
					$c = $manager->getChunk($c->getX(), $c->getZ());
					$c->setGenerated();
				}
			}
		}
		$generator->populateChunk($chunk->getX(), $chunk->getZ());
		$chunk = $manager->getChunk($chunk->getX(), $chunk->getZ());
		$chunk->recalculateHeightMap();
		$chunk->populateSkyLight();
		$chunk->setLightPopulated();
		$chunk->setPopulated();
		$this->chunk = $chunk->fastSerialize();
		$manager->setChunk($chunk->getX(), $chunk->getZ(), null);
		foreach($chunks as $i => $c){
			if($c !== null){
				$c = $chunks[$i] = $manager->getChunk($c->getX(), $c->getZ());
				if(!$c->hasChanged()){
					$chunks[$i] = null;
				}
			}else{
				$chunks[$i] = null;
			}
		}
		$manager->cleanChunks();
		for($i = 0; $i < 9; ++$i){
			if($i === 4){
				continue;
			}
			$this->{"chunk$i"} = $chunks[$i] !== null ? $chunks[$i]->fastSerialize() : null;
		}
	}
	public function onCompletion(Server $server){
		$level = $server->getLevel($this->levelId);
		if($level !== null){
			if(!$this->state){
				$level->registerGeneratorToWorker($this->workerId);
			}
			$chunk = Chunk::fastDeserialize($this->chunk);
			for($i = 0; $i < 9; ++$i){
				if($i === 4){
					continue;
				}
				$c = $this->{"chunk$i"};
				if($c !== null){
					$c = Chunk::fastDeserialize($c);
					$level->generateChunkCallback($c->getX(), $c->getZ(), $this->state ? $c : null);
				}
			}
			$level->generateChunkCallback($chunk->getX(), $chunk->getZ(), $this->state ? $chunk : null);
		}
	}}