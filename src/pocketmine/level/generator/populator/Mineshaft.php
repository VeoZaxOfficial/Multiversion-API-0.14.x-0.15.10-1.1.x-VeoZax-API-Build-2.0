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

namespace pocketmine\level\generator\populator;
use pocketmine\block\Block;use pocketmine\level\ChunkManager;use pocketmine\math\Vector3;use pocketmine\utils\Random;
class Mineshaft extends Populator{
    private $level;
    private $random;
	public function populate(ChunkManager $level, $chunkX, $chunkZ, Random $random){
        $this->level = $level;
        $this->random = $random;
        if($random->nextBoundedInt(8) == 1){
            $x = $cx = ($chunkX << 4) + $random->nextBoundedInt(16);
            $z = $cz = ($chunkZ << 4) + $random->nextBoundedInt(16);
            $y = $cy = $random->nextBoundedInt(20) + $random->nextBoundedInt(25) + 10;
            $direction = $random->nextBoundedInt(4);
            $length = $random->nextBoundedInt(35) + 40;
            for($i = 0; $i < $length; $i++){
				if($i % 5 == 0){
					switch($direction){
						case 0: 
							$x = $cx + $i;
							$y = $cy;
							$z = $cz;
							break;
						case 1: 
							$x = $cx - $i;
							$y = $cy;
							$z = $cz;
							break;
						case 2: 
							$x = $cx;
							$y = $cy;
							$z = $cz + $i;
							break;
						case 3: 
							$x = $cx;
							$y = $cy;
							$z = $cz - $i;
							break;
					}
					$this->generateShaft($x, $y, $z, $direction);
					if($this->random->nextRange(0, 3) == 0){
						$this->generateBranch($x, $y, $z, $direction);
					}
				}
            }
        }
    }
    private function generateShaft(int $x, int $y, int $z, int $direction = 0){
		switch($direction){
			case 0:
			case 1:
				for($xx = -2; $xx <= 2; $xx++){
					for($yy = -1; $yy <= 1; $yy++){
						for($zz = -1; $zz <= 1; $zz++){
							$this->level->setBlockIdAt($x + $xx, $y + $yy, $z + $zz, Block::AIR);
						}
					}
				}
				$this->level->setBlockIdAt($x, $y + 1, $z + 1, Block::PLANKS);
				$this->level->setBlockIdAt($x, $y + 1, $z, Block::PLANKS);
				$this->level->setBlockIdAt($x, $y + 1, $z - 1, Block::PLANKS);
				$this->level->setBlockIdAt($x, $y, $z + 1, Block::FENCE);
				$this->level->setBlockIdAt($x, $y - 1, $z + 1, Block::FENCE);
				$this->level->setBlockIdAt($x, $y, $z - 1, Block::FENCE);
				$this->level->setBlockIdAt($x, $y - 1, $z - 1, Block::FENCE);
				break;
			case 2:
			case 3:
				for($xx = -1; $xx <= 1; $xx++){
					for($yy = -1; $yy <= 1; $yy++){
						for($zz = -2; $zz <= 2; $zz++){
							$this->level->setBlockIdAt($x + $xx, $y + $yy, $z + $zz, Block::AIR);
						}
					}
				}
				$this->level->setBlockIdAt($x + 1, $y + 1, $z, Block::PLANKS);
				$this->level->setBlockIdAt($x, $y + 1, $z, Block::PLANKS);
				$this->level->setBlockIdAt($x - 1, $y + 1, $z, Block::PLANKS);
				$this->level->setBlockIdAt($x + 1, $y, $z, Block::FENCE);
				$this->level->setBlockIdAt($x + 1, $y - 1, $z, Block::FENCE);
				$this->level->setBlockIdAt($x - 1, $y, $z, Block::FENCE);
				$this->level->setBlockIdAt($x - 1, $y - 1, $z, Block::FENCE);
				break;
		}
    }
    private function generateBranch(int $x, int $y, int $z, int $direction){
        $length = $this->random->nextBoundedInt(20) + 15;
        switch($direction){
            case 0: 
            case 1: 
                $branchDirection = $this->random->nextRange(2, 3); 
                for($i = 0; $i < $length; $i++){
					if($i % 5 == 0){
						if($branchDirection == 0){
							$this->generateShaft($x, $y, $z + $i, $branchDirection);
						}else{
							$this->generateShaft($x, $y, $z - $i, $branchDirection);
						}
					}
                }
                break;
            case 2: 
            case 3: 
                $branchDirection = $this->random->nextRange(0, 1); 
                for($i = 0; $i < $length; $i++){
					if($i % 5 == 0){
						if($branchDirection == 0){
							$this->generateShaft($x + $i, $y, $z, $branchDirection);
						}else{
							$this->generateShaft($x - $i, $y, $z, $branchDirection);
						}
					}
                }
                break;
        }
    }}