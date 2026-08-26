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

namespace pocketmine\level\generator\object;
use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\block\Sapling;use pocketmine\level\ChunkManager;use pocketmine\utils\Random;
abstract class Mushroom{
	public $overridable = [
		Block::AIR => true,
		6 => true,
		17 => true,
		18 => true,
		Block::SNOW_LAYER => true,
		Block::LOG2 => true,
		Block::LEAVES2 => true
	];
	public $type = 14;
	public $type2 = 10;
	public $trunkBlock = Block::BROWN_MUSHROOM_BLOCK;
	public $leafBlock = Block::BROWN_MUSHROOM_BLOCK;
	public $treeHeight = 4;
	public static function growMushroom(ChunkManager $level, $x, $y, $z, Random $random, $type = 0){
		switch($type){
			case 1:
				$tree = new RedMushroom();
				break;
			case 0:
				$tree = new BrownMushroom();
				break;
			default:
				$tree = new RedMushroom();
				break;
		}
		if($tree->canPlaceObject($level, $x, $y, $z, $random)){
			$tree->placeObject($level, $x, $y, $z, $random);
		}
	}
	public function canPlaceObject(ChunkManager $level, $x, $y, $z, Random $random){
		return true;
	}
	public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
		$this->placeTrunk($level, $x, $y, $z, $random, $this->treeHeight - 1);
		for($yy = $y - 3 + $this->treeHeight; $yy <= $y + $this->treeHeight; ++$yy){
			$yOff = $yy - ($y + $this->treeHeight);
			$mid = (int) (1 - $yOff / 2);
			for($xx = $x - $mid; $xx <= $x + $mid; ++$xx){
				$xOff = abs($xx - $x);
				for($zz = $z - $mid; $zz <= $z + $mid; ++$zz){
					$zOff = abs($zz - $z);
					if($xOff === $mid and $zOff === $mid and ($yOff === 0 or $random->nextBoundedInt(2) === 0)){
						continue;
					}
					if(!BlockFactory::$solid[$level->getBlockIdAt($xx, $yy, $zz)]){
						$level->setBlockIdAt($xx, $yy, $zz, $this->leafBlock);
						$level->setBlockDataAt($xx, $yy, $zz, $this->type);
					}
				}
			}
		}
	}
	protected function placeTrunk(ChunkManager $level, $x, $y, $z, Random $random, $trunkHeight){
		$level->setBlockIdAt($x, $y - 1, $z, Block::DIRT);
		for($yy = 0; $yy < $trunkHeight + 1; ++$yy){
			$blockId = $level->getBlockIdAt($x, $y + $yy, $z);
			if(isset($this->overridable[$blockId])){
				$level->setBlockIdAt($x, $y + $yy, $z, $this->trunkBlock);
				$level->setBlockDataAt($x, $y + $yy, $z, $this->type2);
			}
		}
		return $y === 0 ? -1 : ++$y;
	}}