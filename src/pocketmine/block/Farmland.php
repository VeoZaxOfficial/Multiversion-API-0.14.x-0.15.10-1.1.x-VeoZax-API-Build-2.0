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
namespace pocketmine\block;
use pocketmine\entity\Entity;use pocketmine\entity\Living;use pocketmine\event\block\BlockFormEvent;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\math\AxisAlignedBB;use pocketmine\math\Vector3;
class Farmland extends Transparent{
	protected $id = self::FARMLAND;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getName() : string{
		return "Farmland";
	}
	public function getHardness() : float{
		return 0.6;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_SHOVEL;
	}
	protected function recalculateBoundingBox() : ?AxisAlignedBB{
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 1, 
			$this->z + 1
		);
	}
	public function onNearbyBlockChange() : void{
		if($this->level !== null && $this->level->getName() === "VZSMP") return;
		if($this->getSide(Vector3::SIDE_UP)->isSolid()){
			$this->level->setBlock($this, BlockFactory::get(Block::DIRT), true);
		}
	}
	public function ticksRandomly() : bool{
		return true;
	}
	public function onRandomTick() : void{
		if($this->level !== null && $this->level->getName() === "VZSMP"){
			if($this->meta < 7){
				$this->meta = 7;
				$this->level->setBlock($this, $this, false, false);
			}
			return;
		}
		if(!$this->canHydrate()){
			if($this->meta > 0){
				$this->meta--;
				$this->level->setBlock($this, $this, false, false);
			}else{
				$this->level->setBlock($this, BlockFactory::get(Block::DIRT), false, true);
			}
		}elseif($this->meta < 7){
			$this->meta = 7;
			$this->level->setBlock($this, $this, false, false);
		}
	}
	protected function canHydrate() : bool{
		$start = $this->add(-4, 0, -4);
		$end = $this->add(4, 1, 4);
		for($y = $start->y; $y <= $end->y; ++$y){
			for($z = $start->z; $z <= $end->z; ++$z){
				for($x = $start->x; $x <= $end->x; ++$x){
					$id = $this->level->getBlockIdAt($x, $y, $z);
					if($id === Block::STILL_WATER or $id === Block::FLOWING_WATER){
						return true;
					}
				}
			}
		}
		return false;
	}
	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			ItemFactory::get(Item::DIRT)
		];
	}
	public function isAffectedBySilkTouch() : bool{
		return false;
	}
	public function getPickedItem() : Item{
		return ItemFactory::get(Item::DIRT);
	}
	public function onEntityFallenUpon(Entity $entity, float $fallDistance) : void{
		if($entity instanceof Living){
			if($this->level->random->nextFloat() < ($fallDistance - 0.5)){
				$ev = new BlockFormEvent($this, BlockFactory::get(Block::DIRT));
				$ev->call();
				if(!$ev->isCancelled()){
					$this->level->setBlock($this, $ev->getNewState(), true);
				}
			}
		}
	}}