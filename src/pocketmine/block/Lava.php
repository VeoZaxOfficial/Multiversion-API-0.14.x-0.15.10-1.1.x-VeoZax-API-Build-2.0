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
use pocketmine\entity\Entity;use pocketmine\event\entity\EntityCombustByBlockEvent;use pocketmine\event\entity\EntityDamageByBlockEvent;use pocketmine\event\entity\EntityDamageEvent;use pocketmine\item\Item;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\Player;
class Lava extends Liquid{
	protected $id = self::FLOWING_LAVA;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getLightLevel() : int{
		return 15;
	}
	public function getName() : string{
		return "Lava";
	}
	public function getStillForm() : Block{
		return BlockFactory::get(Block::STILL_LAVA, $this->meta);
	}
	public function getFlowingForm() : Block{
		return BlockFactory::get(Block::FLOWING_LAVA, $this->meta);
	}
	public function getBucketFillSound() : int{
		return LevelSoundEventPacket::SOUND_BUCKET_FILL_LAVA;
	}
	public function getBucketEmptySound() : int{
		return LevelSoundEventPacket::SOUND_BUCKET_EMPTY_LAVA;
	}
	public function tickRate() : int{
		return 30;
	}
	public function getFlowDecayPerBlock() : int{
		return 2; 
	}
	protected function checkForHarden(){
		$colliding = null;
		for($side = 1; $side <= 5; ++$side){ 
			$blockSide = $this->getSide($side);
			if($blockSide instanceof Water){
				$colliding = $blockSide;
				break;
			}
		}
		if($colliding !== null){
			if($this->getDamage() === 0){
				$this->liquidCollide($colliding, BlockFactory::get(Block::OBSIDIAN));
			}elseif($this->getDamage() <= 4){
				$this->liquidCollide($colliding, BlockFactory::get(Block::COBBLESTONE));
			}
		}
	}
	protected function flowIntoBlock(Block $block, int $newFlowDecay) : void{
		if($block instanceof Water){
			$block->liquidCollide($this, BlockFactory::get(Block::STONE));
		}else{
			parent::flowIntoBlock($block, $newFlowDecay);
		}
	}
	public function onEntityCollide(Entity $entity) : void{
		$entity->fallDistance *= 0.5;
		$ev = new EntityDamageByBlockEvent($this, $entity, EntityDamageEvent::CAUSE_LAVA, 4);
		$entity->attack($ev);
		$ev = new EntityCombustByBlockEvent($this, $entity, 15);
		$ev->call();
		if(!$ev->isCancelled()){
			$entity->setOnFire($ev->getDuration());
		}
		$entity->resetFallDistance();
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$ret = $this->getLevelNonNull()->setBlock($this, $this, true, false);
		$this->getLevelNonNull()->scheduleDelayedBlockUpdate($this, $this->tickRate());
		return $ret;
	}}