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
namespace pocketmine\level\light;
use pocketmine\block\BlockFactory;use pocketmine\level\ChunkManager;use pocketmine\level\utils\SubChunkIteratorManager;use SplQueue;
abstract class LightUpdate{
	protected $level;
	protected $updateNodes = [];
	protected $spreadQueue;
	protected $spreadVisited = [];
	protected $removalQueue;
	protected $removalVisited = [];
	protected $subChunkHandler;
	public function __construct(ChunkManager $level){
		$this->level = $level;
		$this->removalQueue = new SplQueue();
		$this->spreadQueue = new SplQueue();
		$this->subChunkHandler = new SubChunkIteratorManager($this->level);
	}
	protected $touchedChunks = [];
	abstract protected function getLight(int $x, int $y, int $z) : int;
	abstract protected function setLight(int $x, int $y, int $z, int $level);
	private function writeLight(int $x, int $y, int $z, int $level) : void{
		$this->setLight($x, $y, $z, $level);
		$this->touchedChunks[((($x >> 4) & 0xFFFFFFFF) << 32) | (($z >> 4) & 0xFFFFFFFF)] = true;
	}
	public function getTouchedChunks() : array{
		return array_keys($this->touchedChunks);
	}
	public function setAndUpdateLight(int $x, int $y, int $z, int $newLevel){
		$this->updateNodes[((($x) & 0xFFFFFFF) << 36) | ((( $y) & 0xff) << 28) | (( $z) & 0xFFFFFFF)] = [$x, $y, $z, $newLevel];
	}
	private function prepareNodes() : void{
		foreach($this->updateNodes as $blockHash => [$x, $y, $z, $newLevel]){
			if($this->subChunkHandler->moveTo($x, $y, $z)){
				$oldLevel = $this->getLight($x, $y, $z);
				if($oldLevel !== $newLevel){
					$this->writeLight($x, $y, $z, $newLevel);
					if($oldLevel < $newLevel){ 
						$this->spreadVisited[$blockHash] = true;
						$this->spreadQueue->enqueue([$x, $y, $z]);
					}else{ 
						$this->removalVisited[$blockHash] = true;
						$this->removalQueue->enqueue([$x, $y, $z, $oldLevel]);
					}
				}
			}
		}
	}
	public function execute(){
		$this->prepareNodes();
		while(!$this->removalQueue->isEmpty()){
			list($x, $y, $z, $oldAdjacentLight) = $this->removalQueue->dequeue();
			$points = [
				[$x + 1, $y, $z],
				[$x - 1, $y, $z],
				[$x, $y + 1, $z],
				[$x, $y - 1, $z],
				[$x, $y, $z + 1],
				[$x, $y, $z - 1]
			];
			foreach($points as list($cx, $cy, $cz)){
				if($this->subChunkHandler->moveTo($cx, $cy, $cz)){
					$this->computeRemoveLight($cx, $cy, $cz, $oldAdjacentLight);
				}
			}
		}
		while(!$this->spreadQueue->isEmpty()){
			list($x, $y, $z) = $this->spreadQueue->dequeue();
			unset($this->spreadVisited[((($x) & 0xFFFFFFF) << 36) | ((( $y) & 0xff) << 28) | (( $z) & 0xFFFFFFF)]);
			if(!$this->subChunkHandler->moveTo($x, $y, $z)){
				continue;
			}
			$newAdjacentLight = $this->getLight($x, $y, $z);
			if($newAdjacentLight <= 0){
				continue;
			}
			$points = [
				[$x + 1, $y, $z],
				[$x - 1, $y, $z],
				[$x, $y + 1, $z],
				[$x, $y - 1, $z],
				[$x, $y, $z + 1],
				[$x, $y, $z - 1]
			];
			foreach($points as list($cx, $cy, $cz)){
				if($this->subChunkHandler->moveTo($cx, $cy, $cz)){
					$this->computeSpreadLight($cx, $cy, $cz, $newAdjacentLight);
				}
			}
		}
	}
	protected function computeRemoveLight(int $x, int $y, int $z, int $oldAdjacentLevel){
		$current = $this->getLight($x, $y, $z);
		if($current !== 0 and $current < $oldAdjacentLevel){
			$this->writeLight($x, $y, $z, 0);
			if(!isset($this->removalVisited[$index = ((($x) & 0xFFFFFFF) << 36) | ((( $y) & 0xff) << 28) | (( $z) & 0xFFFFFFF)])){
				$this->removalVisited[$index] = true;
				if($current > 1){
					$this->removalQueue->enqueue([$x, $y, $z, $current]);
				}
			}
		}elseif($current >= $oldAdjacentLevel){
			if(!isset($this->spreadVisited[$index = ((($x) & 0xFFFFFFF) << 36) | ((( $y) & 0xff) << 28) | (( $z) & 0xFFFFFFF)])){
				$this->spreadVisited[$index] = true;
				$this->spreadQueue->enqueue([$x, $y, $z]);
			}
		}
	}
	protected function computeSpreadLight(int $x, int $y, int $z, int $newAdjacentLevel){
		$current = $this->getLight($x, $y, $z);
		$potentialLight = $newAdjacentLevel - BlockFactory::$lightFilter[$this->subChunkHandler->currentSubChunk->getBlockId($x & 0x0f, $y & 0x0f, $z & 0x0f)];
		if($current < $potentialLight){
			$this->writeLight($x, $y, $z, $potentialLight);
			if(!isset($this->spreadVisited[$index = ((($x) & 0xFFFFFFF) << 36) | ((( $y) & 0xff) << 28) | (( $z) & 0xFFFFFFF)]) and $potentialLight > 1){
				$this->spreadVisited[$index] = true;
				$this->spreadQueue->enqueue([$x, $y, $z]);
			}
		}
	}}