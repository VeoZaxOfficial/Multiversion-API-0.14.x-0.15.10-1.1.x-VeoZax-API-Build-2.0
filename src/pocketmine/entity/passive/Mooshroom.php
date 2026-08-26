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
namespace pocketmine\entity\passive;
use pocketmine\block\Block;use pocketmine\entity\Entity;use pocketmine\item\Bowl;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\item\Shears;use pocketmine\math\Vector3;use pocketmine\Player;
class Mooshroom extends Cow{
	public const NETWORK_ID = self::MOOSHROOM;
	protected $spawnableBlock = Block::MYCELIUM;
	public function getName() : string{
		return "Mooshroom";
	}
	public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
		if(!$this->isImmobile()){
			if($item instanceof Bowl and !$this->isBaby()){
				$new = ItemFactory::get(Item::MUSHROOM_STEW);
				if($player->isSurvival()){
					$item->pop();
				}
				if($player->getInventory()->canAddItem($new)){
					$player->getInventory()->addItem($new);
				}else{
					$player->dropItem($new);
				}
				return true;
			}elseif($item instanceof Shears and !$this->isBaby()){
				$cow = new Cow($this->level, Entity::createBaseNBT($this));
				$cow->setRotation($this->yaw, $this->pitch);
				$cow->setHealth($this->getHealth());
				$cow->setNameTag($this->getNameTag());
				$cow->setImmobile(!$this->server->mobAiEnabled);
				$item->applyDamage(1);
				for($i = 0; $i < 5; $i++){
					$player->dropItem(ItemFactory::get(Block::RED_MUSHROOM));
				}
				$this->flagForDespawn();
				$cow->spawnToAll();
				return true;
			}
		}
		return parent::onInteract($player, $item, $clickPos);
	}}