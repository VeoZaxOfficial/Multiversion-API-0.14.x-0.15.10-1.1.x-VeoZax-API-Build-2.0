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
use pocketmine\block\Block;use pocketmine\block\Wood2;use pocketmine\level\ChunkManager;use pocketmine\utils\Random;
class AcaciaTree extends Tree{
	public function __construct(){
		$this->trunkBlock = Block::WOOD2;
		$this->leafBlock = Block::LEAVES2;
		$this->type = Wood2::ACACIA;
	}
	public function placeObject(ChunkManager $world, $source_x, $source_y, $source_z, Random $random){
		$this->treeHeight = $random->nextBoundedInt(3) + $random->nextBoundedInt(3) + 5;
		$d = ($random->nextFloat() * M_PI * 2.0); 
		$dx = (int) (cos($d) + 1.5) - 1;
		$dz = (int) (sin($d) + 1.5) - 1;
		if(abs($dx) > 0 && abs($dz) > 0){ 
			if($random->nextBoolean()){
				$dx = 0;
			}else{
				$dz = 0;
			}
		}
		$twist_height = $this->treeHeight - 1 - $random->nextBoundedInt(4);
		$twist_count = $random->nextBoundedInt(3) + 1;
		$center_x = $source_x;
		$center_z = $source_z;
		$trunk_top_y = 0;
		for($y = 0; $y < $this->treeHeight; ++$y){
			if($twist_count > 0 && $y >= $twist_height){
				$center_x += $dx;
				$center_z += $dz;
				--$twist_count;
			}
			$material = $world->getBlockIdAt($center_x, $source_y + $y, $center_z);
			if($material === Block::AIR || $material === Block::LEAVES){
				$trunk_top_y = $source_y + $y;
				$world->setBlockIdAt($center_x, $source_y + $y, $center_z, $this->trunkBlock);
			}
		}
		for($x = -3; $x <= 3; ++$x){
			$abs_x = abs($x);
			for($z = -3; $z <= 3; ++$z){
				$abs_z = abs($z);
				if($abs_x < 3 || $abs_z < 3){
					$this->setLeaves($center_x + $x, $trunk_top_y, $center_z + $z, $world);
				}
				if($abs_x < 2 && $abs_z < 2){
					$this->setLeaves($center_x + $x, $trunk_top_y + 1, $center_z + $z, $world);
				}
				if(($abs_x === 2 && $abs_z === 0) || ($abs_x === 0 && $abs_z === 2)){
					$this->setLeaves($center_x + $x, $trunk_top_y + 1, $center_z + $z, $world);
				}
			}
		}
		$d = $random->nextFloat() * M_PI * 2.0;
		$dx_b = (int) (cos($d) + 1.5) - 1;
		$dz_b = (int) (sin($d) + 1.5) - 1;
		if(abs($dx_b) > 0 && abs($dz_b) > 0){
			if($random->nextBoolean()){
				$dx_b = 0;
			}else{
				$dz_b = 0;
			}
		}
		if($dx !== $dx_b || $dz !== $dz_b){
			$center_x = $source_x;
			$center_z = $source_z;
			$branch_height = $twist_height - 1 - $random->nextBoundedInt(2);
			$twist_count = $random->nextBoundedInt(3) + 1;
			$trunk_top_y = 0;
			for($y = $branch_height + 1; $y < $this->treeHeight; ++$y){
				if($twist_count > 0){
					$center_x += $dx_b;
					$center_z += $dz_b;
					$material = $world->getBlockIdAt($center_x, $source_y + $y, $center_z);
					if($material === Block::AIR || $material === Block::LEAVES){
						$trunk_top_y = $source_y + $y;
						$world->setBlockIdAt($center_x, $source_y + $y, $center_z, $this->trunkBlock);
					}
					--$twist_count;
				}
			}
			if($trunk_top_y > 0){
				for($x = -2; $x <= 2; ++$x){
					for($z = -2; $z <= 2; ++$z){
						if(abs($x) < 2 || abs($z) < 2){
							$this->setLeaves($center_x + $x, $trunk_top_y, $center_z + $z, $world);
						}
					}
				}
				for($x = -1; $x <= 1; ++$x){
					for($z = -1; $z <= 1; ++$z){
						$this->setLeaves($center_x + $x, $trunk_top_y + 1, $center_z + $z, $world);
					}
				}
			}
		}
		$world->setBlockIdAt($source_x, $source_y - 1, $source_z, Block::DIRT);
		$world->setBlockIdAt($source_x, $source_y, $source_z, $this->trunkBlock);
		$world->setBlockDataAt($source_x, $source_y, $source_z, 0);
		return true;
	}
	private function setLeaves(int $x, int $y, int $z, ChunkManager $world) : void{
		if($world->getBlockIdAt($x, $y, $z) === Block::AIR){
			$world->setBlockIdAt($x, $y, $z, $this->leafBlock);
		}
	}}