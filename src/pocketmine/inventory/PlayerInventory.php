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
use InvalidArgumentException;use LogicException;use pocketmine\entity\Human;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\event\player\PlayerItemHeldEvent;use pocketmine\network\mcpe\cache\CreativePacketCache;use pocketmine\network\mcpe\protocol\CreativeContentPacket;use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;use pocketmine\network\mcpe\protocol\InventoryContentPacket;use pocketmine\network\mcpe\protocol\MobEquipmentPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\types\ContainerIds;use pocketmine\network\mcpe\protocol\types\CreativeGroup;use pocketmine\Player;use RuntimeException;use SplFixedArray;use function array_search;use function in_array;use function is_array;use function range;
class PlayerInventory extends BaseInventory{
	protected $holder;
	protected $itemInHandIndex = 0;
	protected $hotbar;
	public function __construct(Human $player){
		$this->holder = $player;
		$this->resetHotbar();
		parent::__construct();
	}
	public function getName() : string{
		return "Player";
	}
	public function getDefaultSize() : int{
		return 36;
	}
	public function getHotbarSlotIndex(int $index) : int{
		return $this->hotbar[$index] ?? -1;
	}
	public function setHotbarSlotIndex(int $hotbarSlot, int $inventorySlot){
		if($inventorySlot < -1 or $inventorySlot >= $this->getSize()){
			throw new InvalidArgumentException("Inventory slot index \"$inventorySlot\" is out of range");
		}
		if($inventorySlot !== -1 and ($alreadyEquippedIndex = array_search($inventorySlot, $this->getHotbar(), true)) !== false){
			$this->hotbar[$alreadyEquippedIndex] = $this->hotbar[$hotbarSlot];
		}
		$this->hotbar[$hotbarSlot] = $inventorySlot;
	}
	public function getHotbarSlotItem(int $hotbarSlotIndex) : Item{
		$inventorySlot = $this->getHotbarSlotIndex($hotbarSlotIndex);
		if($inventorySlot !== -1){
			return $this->getItem($inventorySlot);
		}else{
			return ItemFactory::get(Item::AIR, 0, 0);
		}
	}
	public function getHotbar() : array{
		return $this->hotbar->toArray();
	}
	public function resetHotbar() : void{
		$this->hotbar = SplFixedArray::fromArray(range(0, $this->getHotbarSize() - 1, 1));
	}
    public function isHotbarSlot(int $hotbarSlot) : bool{
        return $hotbarSlot >= 0 && $hotbarSlot <= $this->getHotbarSize();
    }
	public function throwIfNotHotbarSlot(int $slot){
		if(!$this->isHotbarSlot($slot)){
			throw new InvalidArgumentException("$slot is not a valid hotbar slot index (expected 0 - " . ($this->getHotbarSize() - 1) . ")");
		}
	}
	public function getHeldItemIndex() : int{
		return $this->itemInHandIndex;
	}
	public function equipItem(int $hotbarSlot, ?int $inventorySlot = null) : bool{
		$holder = $this->getHolder();
		if(!$this->isHotbarSlot($hotbarSlot)){
			if($holder instanceof Player){
				$this->sendContents($holder);
			}
			return false;
		}
		if($holder instanceof Player){
			$ev = new PlayerItemHeldEvent($holder, $inventorySlot === null ? $this->getItem($hotbarSlot) : $this->getItem($inventorySlot), $hotbarSlot);
			$ev->call();
			if($ev->isCancelled()){
				$this->sendHeldItem($holder);
				return false;
			}
		}
		$this->setHeldItemIndex($hotbarSlot, false, $inventorySlot);
		return true;
	}
	public function setHeldItemIndex(int $hotbarSlot, bool $send = true, ?int $inventorySlot = null){
	    $this->throwIfNotHotbarSlot($hotbarSlot);
		$this->itemInHandIndex = $hotbarSlot;
		if($inventorySlot !== null){
            $this->setHotbarSlotIndex($hotbarSlot, $inventorySlot);
		}
		if($this->getHolder() instanceof Player and $send){
			$this->sendHeldItem($this->getHolder());
		}
		$this->sendHeldItem($this->getHolder()->getViewers());
	}
	public function getItemInHand() : Item{
		return $this->getHotbarSlotItem($this->itemInHandIndex);
	}
	public function setItemInHand(Item $item) : bool{
		return $this->setItem($this->getHeldItemSlot(), $item);
	}
	public function getHeldItemSlot() : int{
		return $this->getHotbarSlotIndex($this->itemInHandIndex);
	}
	public function setHeldItemSlot(int $slot){
		if($slot >= -1 and $slot < $this->getSize()){
			$this->setHotbarSlotIndex($this->getHeldItemIndex(), $slot);
		}
	}
	public function sendHeldItem($target){
		$item = $this->getItemInHand();
		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->item = $item;
		$pk->inventorySlot = $this->getHeldItemSlot();
		$pk->hotbarSlot = $this->getHeldItemIndex();
		$pk->windowId = ContainerIds::INVENTORY;
		if(!is_array($target)){
		    
			$target->dataPacket($pk);
			if($this->getHeldItemSlot() !== -1 and $target === $this->getHolder()){
				$this->sendSlot($this->getHeldItemSlot(), $target);
			}
		}else{
		    foreach($target as $player){
		        $packet = clone $pk;
		        
		        $player->dataPacket($packet);
		    }
			if($this->getHeldItemSlot() !== -1 and in_array($this->getHolder(), $target, true)){
				$this->sendSlot($this->getHeldItemSlot(), $this->getHolder());
			}
		}
	}
	public function getHotbarSize() : int{
		return 9;
	}
	public function sendCreativeContents(){
		$holder = $this->getHolder();
		if(!($holder instanceof Player)){
			throw new LogicException("Cannot send creative inventory contents to non-player inventory holder");
		}
		
	   	    $pk = new ContainerSetContentPacket();
	    	$pk->windowid = ContainerIds::CREATIVE;
		    if(!$holder->isSpectator()){
                $creativeItemEntries = CreativePacketCache::getInstance()->getItems($holder->getProtocol());
                foreach($creativeItemEntries as $i => $creativeItemEntry){
                    $pk->slots[$i] = clone $creativeItemEntry->getItem();
                }
		    }
		    $pk->targetEid = $holder->getId();
        
        $holder->dataPacket($pk);
    }
    public function clearAll(bool $send = true) : void{
        $this->resetHotbar();
        parent::clearAll($send);
    }
	public function onSlotChange(int $index, Item $before, bool $send) : void{
	    parent::onSlotChange($index, $before, $send);
		if($index === $this->itemInHandIndex){
			$this->sendHeldItem($this->holder->getViewers());
			if($send && $this->holder instanceof Player){
				$this->sendHeldItem($this->holder);
			}
		}
	}
	public function getHolder(){
		return $this->holder;
	}}