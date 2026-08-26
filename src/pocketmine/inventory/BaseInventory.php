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
namespace pocketmine\inventory;
use pocketmine\event\inventory\InventoryOpenEvent;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\item\ItemIds;use pocketmine\level\Level;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;use pocketmine\network\mcpe\protocol\InventoryContentPacket;use pocketmine\network\mcpe\protocol\InventorySlotPacket;use pocketmine\network\mcpe\protocol\PlayerHotbarPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\types\ContainerIds;use pocketmine\Player;use SplFixedArray;use function array_slice;use function count;use function max;use function min;use function spl_object_hash;
abstract class BaseInventory implements Inventory{
	protected $maxStackSize = Inventory::MAX_STACK;
	protected $name;
	protected $title;
	protected $slots;
	protected $viewers = [];
	protected $eventProcessor;
	public function __construct(array $items = [], int $size = null, string $title = null){
		$this->slots = new SplFixedArray($size ?? $this->getDefaultSize());
		$this->title = $title ?? $this->getName();
		$this->setContents($items, false);
	}
	abstract public function getName() : string;
	public function getTitle() : string{
		return $this->title;
	}
	public function getSize() : int{
		return $this->slots->getSize();
	}
	public function setSize(int $size){
		$this->slots->setSize($size);
	}
	abstract public function getDefaultSize() : int;
	public function getMaxStackSize() : int{
		return $this->maxStackSize;
	}
	public function getItem(int $index) : Item{
		return isset($this->slots[$index]) ? clone $this->slots[$index] : ItemFactory::get(Item::AIR, 0, 0);
	}
	public function getContents(bool $includeEmpty = false) : array{
		$contents = [];
		$air = null;
		foreach($this->slots as $i => $slot){
			if($slot !== null){
				$contents[$i] = clone $slot;
			}elseif($includeEmpty){
				$contents[$i] = $air ?? ($air = ItemFactory::get(Item::AIR, 0, 0));
			}
		}
		return $contents;
	}
	public function setContents(array $items, bool $send = true) : void{
		if(count($items) > $this->getSize()){
			$items = array_slice($items, 0, $this->getSize(), true);
		}
		for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
			if(!isset($items[$i])){
				if($this->slots[$i] !== null){
					$this->clear($i, false);
				}
			}else{
				if(!$this->setItem($i, $items[$i], false)){
					$this->clear($i, false);
				}
			}
		}
		if($send){
			$this->sendContents($this->getViewers());
		}
	}
	public function dropContents(Level $level, Vector3 $position) : void{
		foreach($this->getContents() as $item){
			$level->dropItem($position, $item);
		}
		$this->clearAll();
	}
	public function setItem(int $index, Item $item, bool $send = true) : bool{
		if($item->isNull()){
			$item = ItemFactory::get(Item::AIR, 0, 0);
		}else{
			$item = clone $item;
		}
		$oldItem = $this->getItem($index);
		if($this->eventProcessor !== null){
			$newItem = $this->eventProcessor->onSlotChange($this, $index, $oldItem, $item);
			if($newItem === null){
				return false;
			}
		}else{
			$newItem = $item;
		}
		$this->slots[$index] = $newItem->isNull() ? null : $newItem;
		$this->onSlotChange($index, $oldItem, $send);
		return true;
	}
	public function contains(Item $item) : bool{
		$count = max(1, $item->getCount());
		$checkDamage = !$item->hasAnyDamageValue();
		$checkTags = $item->hasCompoundTag();
		foreach($this->getContents() as $i){
			if($item->equals($i, $checkDamage, $checkTags)){
				$count -= $i->getCount();
				if($count <= 0){
					return true;
				}
			}
		}
		return false;
	}
	public function all(Item $item) : array{
		$slots = [];
		$checkDamage = !$item->hasAnyDamageValue();
		$checkTags = $item->hasCompoundTag();
		foreach($this->getContents() as $index => $i){
			if($item->equals($i, $checkDamage, $checkTags)){
				$slots[$index] = $i;
			}
		}
		return $slots;
	}
	public function remove(Item $item) : void{
		$checkDamage = !$item->hasAnyDamageValue();
		$checkTags = $item->hasCompoundTag();
		foreach($this->getContents() as $index => $i){
			if($item->equals($i, $checkDamage, $checkTags)){
				$this->clear($index);
			}
		}
	}
	public function first(Item $item, bool $exact = false) : int{
		$count = $exact ? $item->getCount() : max(1, $item->getCount());
		$checkDamage = $exact || !$item->hasAnyDamageValue();
		$checkTags = $exact || $item->hasCompoundTag();
		foreach($this->getContents() as $index => $i){
			if($item->equals($i, $checkDamage, $checkTags) and ($i->getCount() === $count or (!$exact and $i->getCount() > $count))){
				return $index;
			}
		}
		return -1;
	}
	public function firstEmpty() : int{
		foreach($this->slots as $i => $slot){
			if($slot === null or $slot->isNull()){
				return $i;
			}
		}
		return -1;
	}
    public function firstOccupied() : int{
        for($i = 0; $i < $this->getSize(); $i++){
            if(($item = $this->getItem($i))->getId() !== ItemIds::AIR and $item->getCount() > 0){
                return $i;
            }
        }
        return -1;
    }
	public function isSlotEmpty(int $index) : bool{
		return $this->slots[$index] === null or $this->slots[$index]->isNull();
	}
	public function canAddItem(Item $item) : bool{
		$item = clone $item;
		for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
			$slot = $this->getItem($i);
			if($item->equals($slot)){
				if(($diff = $slot->getMaxStackSize() - $slot->getCount()) > 0){
					$item->setCount($item->getCount() - $diff);
				}
			}elseif($slot->isNull()){
				$item->setCount($item->getCount() - $this->getMaxStackSize());
			}
			if($item->getCount() <= 0){
				return true;
			}
		}
		return false;
	}
	public function addItem(Item ...$slots) : array{
		$itemSlots = [];
		foreach($slots as $slot){
			if(!$slot->isNull()){
				$itemSlots[] = clone $slot;
			}
		}
		$emptySlots = [];
		for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
			$item = $this->getItem($i);
			if($item->isNull()){
				$emptySlots[] = $i;
			}
			foreach($itemSlots as $index => $slot){
				if($slot->equals($item) and $item->getCount() < $item->getMaxStackSize()){
					$amount = min($item->getMaxStackSize() - $item->getCount(), $slot->getCount(), $this->getMaxStackSize());
					if($amount > 0){
						$slot->setCount($slot->getCount() - $amount);
						$item->setCount($item->getCount() + $amount);
						$this->setItem($i, $item);
						if($slot->getCount() <= 0){
							unset($itemSlots[$index]);
						}
					}
				}
			}
			if(count($itemSlots) === 0){
				break;
			}
		}
		if(count($itemSlots) > 0 and count($emptySlots) > 0){
			foreach($emptySlots as $slotIndex){
				foreach($itemSlots as $index => $slot){
					$amount = min($slot->getMaxStackSize(), $slot->getCount(), $this->getMaxStackSize());
					$slot->setCount($slot->getCount() - $amount);
					$item = clone $slot;
					$item->setCount($amount);
					$this->setItem($slotIndex, $item);
					if($slot->getCount() <= 0){
						unset($itemSlots[$index]);
					}
					break;
				}
			}
		}
		return $itemSlots;
	}
	public function removeItem(Item ...$slots) : array{
		$itemSlots = [];
		foreach($slots as $slot){
			if(!$slot->isNull()){
				$itemSlots[] = clone $slot;
			}
		}
		for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
			$item = $this->getItem($i);
			if($item->isNull()){
				continue;
			}
			foreach($itemSlots as $index => $slot){
				if($slot->equals($item, !$slot->hasAnyDamageValue(), $slot->hasCompoundTag())){
					$amount = min($item->getCount(), $slot->getCount());
					$slot->setCount($slot->getCount() - $amount);
					$item->setCount($item->getCount() - $amount);
					$this->setItem($i, $item);
					if($slot->getCount() <= 0){
						unset($itemSlots[$index]);
					}
				}
			}
			if(count($itemSlots) === 0){
				break;
			}
		}
		return $itemSlots;
	}
	public function clear(int $index, bool $send = true) : bool{
		return $this->setItem($index, ItemFactory::get(Item::AIR, 0, 0), $send);
	}
	public function clearAll(bool $send = true) : void{
		for($i = 0, $size = $this->getSize(); $i < $size; ++$i){
			$this->clear($i, false);
		}
		if($send){
			$this->sendContents($this->getViewers());
		}
	}
	public function getViewers() : array{
		return $this->viewers;
	}
	public function removeAllViewers(bool $force = false) : void{
		foreach($this->viewers as $hash => $viewer){
			$viewer->removeWindow($this, $force);
			unset($this->viewers[$hash]);
		}
	}
	public function setMaxStackSize(int $size) : void{
		$this->maxStackSize = $size;
	}
	public function open(Player $who) : bool{
		$ev = new InventoryOpenEvent($this, $who);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}
		$this->onOpen($who);
		return true;
	}
	public function close(Player $who) : void{
		$this->onClose($who);
	}
	public function onOpen(Player $who) : void{
		$this->viewers[spl_object_hash($who)] = $who;
	}
	public function onClose(Player $who) : void{
		unset($this->viewers[spl_object_hash($who)]);
	}
	public function onSlotChange(int $index, Item $before, bool $send) : void{
		if($send){
			$this->sendSlot($index, $this->getViewers());
		}
	}
	public function sendContents($target) : void{
		if($target instanceof Player){
			$target = [$target];
		}
		$items = $this->getContents(true);
		foreach($target as $player){
			if(($id = $player->getWindowId($this)) === ContainerIds::NONE){
				$this->close($player);
				continue;
			}
			
			    $pk = new ContainerSetContentPacket();
		    	$pk->windowid = $id;
		    	$pk->targetEid = $player->getId();
		    	$pk->slots = $items;
                if($this instanceof PlayerInventory){
                    $pk->hotbar = [];
                    for($i = 0; $i < $this->getHotbarSize(); $i++){
                        $pk->slots[] = ItemFactory::get(ItemIds::AIR, 0);
                        $index = $this->getHotbarSlotIndex($i);
                        $pk->hotbar[] = $index <= -1 ? -1 : $index + $this->getHotbarSize();
                    }
                }
			    $player->dataPacket(clone $pk);
			
		}
	}
	public function sendSlot(int $index, $target) : void{
		if($target instanceof Player){
			$target = [$target];
		}
		foreach($target as $player){
			if(($id = $player->getWindowId($this)) === ContainerIds::NONE){
				$this->close($player);
				continue;
			}
			
        		$pk = new ContainerSetSlotPacket();
	        	$pk->slot = $index;
	        	$pk->item = $this->getItem($index);
	        	$pk->windowid = $id;
			    $player->dataPacket(clone $pk);
			
		}
	}
	public function slotExists(int $slot) : bool{
		return $slot >= 0 and $slot < $this->slots->getSize();
	}
	public function getEventProcessor() : ?InventoryEventProcessor{
		return $this->eventProcessor;
	}
	public function setEventProcessor(?InventoryEventProcessor $eventProcessor) : void{
		$this->eventProcessor = $eventProcessor;
	}}