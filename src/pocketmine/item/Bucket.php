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
namespace pocketmine\item;
use pocketmine\block\Air;use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\block\Liquid;use pocketmine\entity\Living;use pocketmine\event\player\PlayerBucketEmptyEvent;use pocketmine\event\player\PlayerBucketFillEvent;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\types\DimensionIds;use pocketmine\Player;
class Bucket extends Item implements Consumable{
	public function __construct(int $meta = 0){
		parent::__construct(self::BUCKET, $meta, "Bucket");
	}
	public function getMaxStackSize() : int{
		return $this->meta === Block::AIR ? 16 : 1; 
	}
	public function getFuelTime() : int{
		if($this->meta === Block::LAVA or $this->meta === Block::FLOWING_LAVA){
			return 20000;
		}
		return 0;
	}
	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : bool{
		$resultBlock = BlockFactory::get($this->meta);
		if($resultBlock instanceof Air){
			if($blockClicked instanceof Liquid and $blockClicked->getDamage() === 0){
				$stack = clone $this;
				$stack->pop();
				$resultItem = ItemFactory::get(Item::BUCKET, $blockClicked->getFlowingForm()->getId());
				$ev = new PlayerBucketFillEvent($player, $blockReplace, $face, $this, $resultItem);
				$ev->call();
				if(!$ev->isCancelled()){
					$player->getLevelNonNull()->setBlock($blockClicked, BlockFactory::get(Block::AIR), true, true);
					$player->getLevelNonNull()->broadcastLevelSoundEvent($blockClicked->add(0.5, 0.5, 0.5), $blockClicked->getBucketFillSound());
					if($player->isSurvival()){
						if($stack->getCount() === 0){
							$player->getInventory()->setItemInHand($ev->getItem());
						}else{
							$player->getInventory()->setItemInHand($stack);
							$player->getInventory()->addItem($ev->getItem());
						}
					}else{
						$player->getInventory()->addItem($ev->getItem());
					}
					return true;
				}else{
					$player->getInventory()->sendContents($player);
				}
			}
		}elseif($resultBlock instanceof Liquid and $blockReplace->canBeReplaced()){
			$ev = new PlayerBucketEmptyEvent($player, $blockReplace, $face, $this, ItemFactory::get(Item::BUCKET));
			$ev->call();
			if(!$ev->isCancelled()){
				if(!($player->getLevelNonNull()->getDimensionId() === DimensionIds::NETHER and $resultBlock->getId() === self::FLOWING_WATER)){
			    	$player->getLevelNonNull()->setBlock($blockReplace, $resultBlock->getFlowingForm(), true, true);
			    	$player->getLevelNonNull()->broadcastLevelSoundEvent($blockReplace->add(0.5, 0.5, 0.5), $resultBlock->getBucketEmptySound());
				}
				if($player->isSurvival()){
					$player->getInventory()->setItemInHand($ev->getItem());
				}
				return true;
			}else{
				$player->getInventory()->sendContents($player);
			}
		}
		return false;
	}
	public function getResidue(){
		return ItemFactory::get(Item::BUCKET, 0, 1);
	}
	public function getAdditionalEffects() : array{
		return [];
	}
	public function canBeConsumed() : bool{
		return $this->meta === 1; 
	}
	public function onConsume(Living $consumer){
		$consumer->removeAllEffects();
	}}