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

namespace pocketmine\level\generator\normal\object;
use pocketmine\block\Block;use pocketmine\level\ChunkManager;use pocketmine\level\generator\object\PopulatorObject;use pocketmine\utils\Random;
class Well extends PopulatorObject{
    private $level;
    private $overridable = [
        Block::AIR => true,
        Block::SAPLING => true,
        Block::LOG => true,
        Block::LEAVES => true,
        Block::STONE => true,
        Block::DANDELION => true,
        Block::POPPY => true,
        Block::SAND => true,
        Block::LOG2 => true,
        Block::LEAVES2 => true,
        Block::CACTUS => true
    ];
    private $directions = [
        [1, 1],
        [1, -1],
        [-1, -1],
        [-1, 1]
    ];
    public function canPlaceObject(ChunkManager $level, $x, $y, $z, Random $random){
        $this->level = $level;
        for ($xx = $x - 2; $xx <= $x + 2; $xx++){
            for ($yy = $y; $yy <= $y + 3; $yy++){
                for ($zz = $z - 2; $zz <= $z + 2; $zz++){
					$id = $level->getBlockIdAt($xx, $yy, $zz);
                    if (!isset($this->overridable[$id])){
                        return false;
					}
				}
			}
		}
        return true;
    }
    public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
        $this->level = $level;
        foreach ($this->directions as $direction) {
            for ($yy = $y; $yy < $y + 3; $yy++)
                $this->placeBlock($x + $direction [0], $yy, $z + $direction [1], Block::SANDSTONE);
            $this->placeBlock($x + ($direction [0] * 2), $y, $z + $direction [1], Block::SANDSTONE);
            $this->placeBlock($x + $direction [0], $y, $z + ($direction [1] * 2), Block::SANDSTONE);
            $this->placeBlock($x + ($direction [0] * 2), $y, $z + ($direction [1] * 2), Block::SANDSTONE);
            $this->placeBlock($x + ($direction [0] * 2), $y, $z, Block::STONE_SLAB, 1);
            $this->placeBlock($x, $y, $z + ($direction [1] * 2), Block::STONE_SLAB, 1);
            $this->placeBlock($x + $direction [0], $y, $z, Block::WATER);
            $this->placeBlock($x, $y, $z + $direction [1], Block::WATER);
        }
        for ($xx = $x - 1; $xx <= $x + 1; $xx++)
            for ($zz = $z - 1; $zz <= $z + 1; $zz++)
                $this->placeBlock($xx, $y + 3, $zz);
        $this->placeBlock($x, $y + 3, $z, Block::SANDSTONE, 1);
		$this->placeBlock($x + 1, $y + 3, $z, Block::STONE_SLAB, 1);
		$this->placeBlock($x - 1, $y + 3, $z, Block::STONE_SLAB, 1);
		$this->placeBlock($x, $y + 3, $z + 1, Block::STONE_SLAB, 1);
		$this->placeBlock($x, $y + 3, $z - 1, Block::STONE_SLAB, 1);
		$this->placeBlock($x + 1, $y + 3, $z + 1, Block::STONE_SLAB, 1);
		$this->placeBlock($x + 1, $y + 3, $z - 1, Block::STONE_SLAB, 1);
		$this->placeBlock($x - 1, $y + 3, $z + 1, Block::STONE_SLAB, 1);
		$this->placeBlock($x - 1, $y + 3, $z - 1, Block::STONE_SLAB, 1);
        $this->placeBlock($x, $y, $z, Block::WATER);
    }
    public function placeBlock($x, $y, $z, $id = 0, $meta = 0){
        $this->level->setBlockIdAt($x, $y, $z, $id);
        $this->level->setBlockDataAt($x, $y, $z, $meta);
    }
}