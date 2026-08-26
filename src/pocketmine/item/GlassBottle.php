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
use pocketmine\block\Block;use pocketmine\block\Water;use pocketmine\event\player\PlayerDropItemEvent;use pocketmine\math\Vector3;use pocketmine\Player;
class GlassBottle extends Item{
	public function __construct(int $meta = 0){
		parent::__construct(self::GLASS_BOTTLE, $meta, "Glass Bottle");
	}
	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : bool{
		if($blockClicked instanceof Water){
			$waterPotion = ItemFactory::get(Item::POTION, 0, 1);
			$stack = clone $this;
			if($player->hasFiniteResources() && $this->getCount() === 0){
				$player->getInventory()->setItemInHand($waterPotion);
				return true;
			}
			$this->pop();
			foreach($player->getInventory()->addItem($waterPotion) as $remains){
				$dropEvent = new PlayerDropItemEvent($player, $remains);
				$dropEvent->call();
				if($dropEvent->isCancelled()){
					$player->getInventory()->setItemInHand($stack);
					return false;
				}
				$player->dropItem($dropEvent->getItem());
			}
			return true;
		}
		return false;
	}}