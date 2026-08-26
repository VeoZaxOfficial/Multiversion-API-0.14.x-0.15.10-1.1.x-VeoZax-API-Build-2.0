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
use pocketmine\block\Block;use pocketmine\block\Planks;use pocketmine\level\ChunkManager;use pocketmine\level\generator\object\PopulatorObject;use pocketmine\math\Vector3;use pocketmine\utils\BuildingUtils;use pocketmine\utils\Random;
class SwampHut extends PopulatorObject{
	private $level;
    private $overridable = [
        Block::AIR => true,
        Block::SAPLING => true,
        Block::LOG => true,
        Block::LEAVES => true,
        Block::STONE => true,
        Block::DANDELION => true,
        Block::POPPY => true,
        Block::WATER => true,
		Block::STILL_WATER => true,
        Block::LOG2 => true,
        Block::LEAVES2 => true,
        Block::CACTUS => true
    ];
	public function canPlaceObject(ChunkManager $level, $x, $y, $z, Random $random){
        $this->level = $level;
        for ($xx = $x - 2; $xx <= $x + 2; $xx++)
            for ($yy = $y; $yy <= $y + 3; $yy++)
                for ($zz = $z - 2; $zz <= $z + 2; $zz++)
                    if (!isset($this->overridable[$level->getBlockIdAt($xx, $yy, $zz)]))
                        return false;
        return true;
	}
	public function placeObject(ChunkManager $level, $x, $y, $z, Random $random){
		$this->level = $level;
		$yOffset = mt_rand(1, 3);
		$firstPos = new Vector3($x, $y + $yOffset, $z);
		BuildingUtils::fill($level, $firstPos->add(-3, 0, -2), $firstPos->add(3, 0, 2), Block::get(Block::WOODEN_PLANK, Planks::SPRUCE)); 
		BuildingUtils::walls($level, $firstPos->add(-3, 1, -2), $firstPos->add(2, 3, 2), Block::get(Block::WOODEN_PLANK, Planks::SPRUCE)); 
		BuildingUtils::fill($level, $firstPos->add(-3, 3, -2), $firstPos->add(-3, -$yOffset, -2), Block::get(Block::WOOD, Planks::SPRUCE)); 
		BuildingUtils::fill($level, $firstPos->add(2, 3, 2), $firstPos->add(2, -$yOffset, 2), Block::get(Block::WOOD, Planks::SPRUCE));
		BuildingUtils::fill($level, $firstPos->add(2, 3, -2), $firstPos->add(2, -$yOffset, -2), Block::get(Block::WOOD, Planks::SPRUCE));
		BuildingUtils::fill($level, $firstPos->add(-3, 3, 2), $firstPos->add(-3, -$yOffset, 2), Block::get(Block::WOOD, Planks::SPRUCE));
		BuildingUtils::fill($level, $firstPos->add(-3, 3, -2), $firstPos->add(2, 3, 2), Block::get(Block::WOODEN_PLANK, Planks::SPRUCE)); 
		$this->placeBlock($firstPos->x + 4, $firstPos->y, $firstPos->z + 1, Block::WOODEN_PLANK, Planks::SPRUCE); 
		$this->placeBlock($firstPos->x + 4, $firstPos->y, $firstPos->z, Block::WOODEN_PLANK, Planks::SPRUCE);
		$this->placeBlock($firstPos->x + 4, $firstPos->y, $firstPos->z - 1, Block::WOODEN_PLANK, Planks::SPRUCE);
		$this->placeBlock($firstPos->x + 3, $firstPos->y + 1, $firstPos->z + 2, Block::FENCE); 
		$this->placeBlock($firstPos->x + 3, $firstPos->y + 1, $firstPos->z - 2, Block::FENCE);
		$this->placeBlock($firstPos->x + 2, $firstPos->y + 2, $firstPos->z + 1, Block::FENCE); 
		$this->placeBlock($firstPos->x + 2, $firstPos->y + 2, $firstPos->z - 1, Block::AIR); 
		$this->placeBlock($firstPos->x + 2, $firstPos->y + 1, $firstPos->z - 1, Block::AIR);
		$this->placeBlock($firstPos->x - 3, $firstPos->y + 2, $firstPos->z, Block::FENCE); 
		$this->placeBlock($firstPos->x - 1, $firstPos->y + 2, $firstPos->z + 2, Block::FLOWER_POT_BLOCK); 
		$this->placeBlock($firstPos->x, $firstPos->y + 2, $firstPos->z + 2, Block::AIR);
		$this->placeBlock($firstPos->x - 1, $firstPos->y + 2, $firstPos->z - 2, Block::AIR); 
		$this->placeBlock($firstPos->x, $firstPos->y + 2, $firstPos->z - 2, Block::AIR);
		$this->placeBlock($firstPos->x - 2, $firstPos->y + 1, $firstPos->z, Block::CAULDRON_BLOCK); 
		$this->placeBlock($firstPos->x - 2, $firstPos->y + 1, $firstPos->z - 1, Block::WORKBENCH);
		BuildingUtils::fill($level, $firstPos->add(-4, 3, -3), $firstPos->add(3, 3, -3), Block::get(Block::SPRUCE_STAIRS, 2)); 
		BuildingUtils::fill($level, $firstPos->add(-4, 3, -3), $firstPos->add(-4, 3, 3), Block::get(Block::SPRUCE_STAIRS, 0)); 
		BuildingUtils::fill($level, $firstPos->add(3, 3, -3), $firstPos->add(3, 3, 3), Block::get(Block::SPRUCE_STAIRS, 1)); 
		BuildingUtils::fill($level, $firstPos->add(-4, 3, 3), $firstPos->add(3, 3, 3), Block::get(Block::SPRUCE_STAIRS, 3)); 
	}
    public function placeBlock($x, $y, $z, $id = 0, $meta = 0){
        $this->level->setBlockIdAt($x, $y, $z, $id);
        $this->level->setBlockDataAt($x, $y, $z, $meta);
    }}