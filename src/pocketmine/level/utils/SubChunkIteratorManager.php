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
namespace pocketmine\level\utils;
use pocketmine\level\ChunkManager;use pocketmine\level\format\Chunk;use pocketmine\level\format\EmptySubChunk;use pocketmine\level\format\SubChunkInterface;
class SubChunkIteratorManager{
	public $level;
	public $currentChunk;
	public $currentSubChunk;
	protected $currentX;
	protected $currentY;
	protected $currentZ;
	protected $allocateEmptySubs = true;
	public function __construct(ChunkManager $level, bool $allocateEmptySubs = true){
		$this->level = $level;
		$this->allocateEmptySubs = $allocateEmptySubs;
	}
	public function moveTo(int $x, int $y, int $z) : bool{
		if($this->currentChunk === null or $this->currentX !== ($x >> 4) or $this->currentZ !== ($z >> 4)){
			$this->currentX = $x >> 4;
			$this->currentZ = $z >> 4;
			$this->currentSubChunk = null;
			$this->currentChunk = $this->level->getChunk($this->currentX, $this->currentZ);
			if($this->currentChunk === null){
				return false;
			}
		}
		if($this->currentSubChunk === null or $this->currentY !== ($y >> 4)){
			$this->currentY = $y >> 4;
			$this->currentSubChunk = $this->currentChunk->getSubChunk($y >> 4, $this->allocateEmptySubs);
			if($this->currentSubChunk instanceof EmptySubChunk){
				return false;
			}
		}
		return true;
	}
	public function invalidate() : void{
		$this->currentChunk = null;
		$this->currentSubChunk = null;
	}}