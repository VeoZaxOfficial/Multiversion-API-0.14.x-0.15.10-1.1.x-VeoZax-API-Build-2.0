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
namespace pocketmine\entity\object;
use pocketmine\entity\Entity;use pocketmine\event\entity\ItemDespawnEvent;use pocketmine\event\entity\ItemSpawnEvent;use pocketmine\event\inventory\InventoryPickupItemEvent;use pocketmine\item\Item;use pocketmine\network\mcpe\protocol\AddItemActorPacket;use pocketmine\network\mcpe\protocol\TakeItemActorPacket;use pocketmine\Player;use UnexpectedValueException;use function get_class;
class ItemEntity extends Entity{
	public const NETWORK_ID = self::ITEM;
	protected $owner = "";
	protected $thrower = "";
	protected $pickupDelay = 0;
	protected $item;
	public $width = 0.25;
	public $height = 0.25;
	protected $baseOffset = 0.125;
	protected $gravity = 0.04;
	protected $drag = 0.02;
	public $canCollide = false;
	protected $age = 0;
	protected function initEntity() : void{
		parent::initEntity();
		$this->setMaxHealth(5);
		$this->setHealth($this->namedtag->getShort("Health", (int) $this->getHealth()));
		$this->age = $this->namedtag->getShort("Age", $this->age);
		$this->pickupDelay = $this->namedtag->getShort("PickupDelay", $this->pickupDelay);
		$this->owner = $this->namedtag->getString("Owner", $this->owner);
		$this->thrower = $this->namedtag->getString("Thrower", $this->thrower);
		$itemTag = $this->namedtag->getCompoundTag("Item");
		if($itemTag === null){
			$this->server->getLogger()->debug("Removing " . get_class($this) . " with missing \"Item\" NBT tag at " . $this->getLevel()->getFolderName() . " (" . round($this->x) . ", " . round($this->y) . ", " . round($this->z) . ")");
			$this->item = Item::get(0);
			$this->flagForDespawn();
			return;
		}
		$this->item = Item::nbtDeserialize($itemTag);
		if($this->item->isNull()){
			$this->server->getLogger()->debug("Removing " . get_class($this) . " with invalid Item data at " . $this->getLevel()->getFolderName() . " (" . round($this->x) . ", " . round($this->y) . ", " . round($this->z) . ")");
			$this->item = Item::get(0);
			$this->flagForDespawn();
			return;
		}
		(new ItemSpawnEvent($this))->call();
	}
	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->closed){
			return false;
		}
		$hasUpdate = parent::entityBaseTick($tickDiff);
		if(!$this->isFlaggedForDespawn() and $this->pickupDelay > -1 and $this->pickupDelay < 32767){ 
			$this->pickupDelay -= $tickDiff;
			if($this->pickupDelay < 0){
				$this->pickupDelay = 0;
			}
			$this->age += $tickDiff;
			if($this->age > 6000){
				$ev = new ItemDespawnEvent($this);
				$ev->call();
				if($ev->isCancelled()){
					$this->age = 0;
				}else{
					$this->flagForDespawn();
					$hasUpdate = true;
				}
			}
		}
		return $hasUpdate;
	}
	protected function tryChangeMovement() : void{
		$this->checkObstruction($this->x, $this->y, $this->z);
		parent::tryChangeMovement();
	}
	protected function applyDragBeforeGravity() : bool{
		return true;
	}
	public function saveNBT() : void{
		parent::saveNBT();
		$this->namedtag->setTag($this->item->nbtSerialize(-1, "Item"));
		$this->namedtag->setShort("Health", (int) $this->getHealth());
		$this->namedtag->setShort("Age", $this->age);
		$this->namedtag->setShort("PickupDelay", $this->pickupDelay);
		if($this->owner !== null){
			$this->namedtag->setString("Owner", $this->owner);
		}
		if($this->thrower !== null){
			$this->namedtag->setString("Thrower", $this->thrower);
		}
	}
	public function getItem() : Item{
		return $this->item;
	}
	public function canCollideWith(Entity $entity) : bool{
		return false;
	}
	public function canBeCollidedWith() : bool{
		return false;
	}
	public function getPickupDelay() : int{
		return $this->pickupDelay;
	}
	public function setPickupDelay(int $delay) : void{
		$this->pickupDelay = $delay;
	}
	public function getOwner() : string{
		return $this->owner;
	}
	public function setOwner(string $owner) : void{
		$this->owner = $owner;
	}
	public function getThrower() : string{
		return $this->thrower;
	}
	public function setThrower(string $thrower) : void{
		$this->thrower = $thrower;
	}
	protected function sendSpawnPacket(Player $player) : void{
		$pk = new AddItemActorPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->item = $this->getItem();
		$pk->metadata = $this->propertyManager->getAll();
		$player->dataPacket($pk);
	}
	public function onCollideWithPlayer(Player $player) : void{
		if($this->getPickupDelay() !== 0){
			return;
		}
		$item = $this->getItem();
        if($player->isSurvival()){
            if($player->getOffHandInventory()->getItem(0)->canStackWith($item) && $player->getOffHandInventory()->canAddItem($item)){
                $playerInventory = $player->getOffHandInventory();
	    	}elseif($player->getInventory()->canAddItem($item)){
		    	$playerInventory = $player->getInventory();
	    	}else{
		        return;
	    	}
		}else{
		    $playerInventory = $player->getInventory();
		}
		$ev = new InventoryPickupItemEvent($playerInventory, $this);
		$ev->call();
		if($ev->isCancelled()){
			return;
		}
		switch($item->getId()){
			case Item::WOOD:
				$player->awardAchievement("mineWood");
				break;
			case Item::DIAMOND:
				$player->awardAchievement("diamond");
				break;
		}
		$pk = new TakeItemActorPacket();
		$pk->eid = $player->getId();
		$pk->target = $this->getId();
		$this->server->broadcastPacket($this->getViewers(), $pk);
		$playerInventory->addItem(clone $item);
		$this->flagForDespawn();
	}}