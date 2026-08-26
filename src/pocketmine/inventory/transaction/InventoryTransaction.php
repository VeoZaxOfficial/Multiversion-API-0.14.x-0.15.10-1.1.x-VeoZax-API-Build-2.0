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
namespace pocketmine\inventory\transaction;
use InvalidArgumentException;use pocketmine\event\inventory\InventoryTransactionEvent;use pocketmine\inventory\Inventory;use pocketmine\inventory\transaction\action\InventoryAction;use pocketmine\inventory\transaction\action\SlotChangeAction;use pocketmine\item\Item;use pocketmine\Player;use function array_keys;use function assert;use function count;use function get_class;use function min;use function shuffle;use function spl_object_hash;
class InventoryTransaction{
	protected $hasExecuted = false;
	protected $source;
	protected $inventories = [];
	protected $actions = [];
	public function __construct(Player $source, array $actions = []){
		$this->source = $source;
		foreach($actions as $action){
			$this->addAction($action);
		}
	}
	public function getSource() : Player{
		return $this->source;
	}
	public function getInventories() : array{
		return $this->inventories;
	}
	public function getActions() : array{
		return $this->actions;
	}
	public function addAction(InventoryAction $action) : void{
		if(!isset($this->actions[$hash = spl_object_hash($action)])){
			$this->actions[$hash] = $action;
			$action->onAddToTransaction($this);
		}else{
			throw new InvalidArgumentException("Tried to add the same action to a transaction twice");
		}
	}
	private function shuffleActions() : void{
		$keys = array_keys($this->actions);
		shuffle($keys);
		$actions = [];
		foreach($keys as $key){
			$actions[$key] = $this->actions[$key];
		}
		$this->actions = $actions;
	}
	public function addInventory(Inventory $inventory) : void{
		if(!isset($this->inventories[$hash = spl_object_hash($inventory)])){
			$this->inventories[$hash] = $inventory;
		}
	}
	protected function matchItems(array &$needItems, array &$haveItems) : void{
		foreach($this->actions as $key => $action){
			if(!$action->getTargetItem()->isNull()){
				$needItems[] = $action->getTargetItem();
			}
			if(!$action->isValid($this->source)){
        $this->sendInventories();
				throw new TransactionValidationException("Action " . get_class($action) . " is not valid in the current transaction");
			}
			if(!$action->getSourceItem()->isNull()){
				$haveItems[] = $action->getSourceItem();
			}
		}
		foreach($needItems as $i => $needItem){
			foreach($haveItems as $j => $haveItem){
				if($needItem->equals($haveItem)){
					$amount = min($needItem->getCount(), $haveItem->getCount());
					$needItem->setCount($needItem->getCount() - $amount);
					$haveItem->setCount($haveItem->getCount() - $amount);
					if($haveItem->getCount() === 0){
						unset($haveItems[$j]);
					}
					if($needItem->getCount() === 0){
						unset($needItems[$i]);
						break;
					}
				}
			}
		}
	}
	protected function squashDuplicateSlotChanges() : void{
		$slotChanges = [];
		$inventories = [];
		$slots = [];
		foreach($this->actions as $key => $action){
			if($action instanceof SlotChangeAction){
				$slotChanges[$h = (spl_object_hash($action->getInventory()) . "@" . $action->getSlot())][] = $action;
				$inventories[$h] = $action->getInventory();
				$slots[$h] = $action->getSlot();
			}
		}
		foreach($slotChanges as $hash => $list){
			if(count($list) === 1){ 
				continue;
			}
			$inventory = $inventories[$hash];
			$slot = $slots[$hash];
			if(!$inventory->slotExists($slot)){ 
				throw new TransactionValidationException("Slot $slot does not exist in inventory " . get_class($inventory));
			}
			$sourceItem = $inventory->getItem($slot);
			$targetItem = $this->findResultItem($sourceItem, $list);
			if($targetItem === null){
				throw new TransactionValidationException("Failed to compact " . count($list) . " duplicate actions");
			}
			foreach($list as $action){
				unset($this->actions[spl_object_hash($action)]);
			}
			if(!$targetItem->equalsExact($sourceItem)){
				$this->addAction(new SlotChangeAction($inventory, $slot, $sourceItem, $targetItem));
			}
		}
	}
	protected function findResultItem(Item $needOrigin, array $possibleActions) : ?Item{
		assert(!empty($possibleActions));
		foreach($possibleActions as $i => $action){
			if($action->getSourceItem()->equalsExact($needOrigin)){
				$newList = $possibleActions;
				unset($newList[$i]);
				if(empty($newList)){
					return $action->getTargetItem();
				}
				$result = $this->findResultItem($action->getTargetItem(), $newList);
				if($result !== null){
					return $result;
				}
			}
		}
		return null;
	}
	public function validate() : void{
		$this->squashDuplicateSlotChanges();
		$haveItems = [];
		$needItems = [];
		$this->matchItems($needItems, $haveItems);
		if(count($this->actions) === 0){
			throw new TransactionValidationException("Inventory transaction must have at least one action to be executable");
		}
		if(count($haveItems) > 0){
			throw new TransactionValidationException("Transaction does not balance (tried to destroy some items)");
		}
		if(count($needItems) > 0){
			throw new TransactionValidationException("Transaction does not balance (tried to create some items)");
		}
	}
	protected function sendInventories() : void{
		foreach($this->inventories as $inventory){
			$inventory->sendContents($this->source);
		}
	}
	protected function callExecuteEvent() : bool{
		$ev = new InventoryTransactionEvent($this);
		$ev->call();
		return !$ev->isCancelled();
	}
	public function execute() : bool{
		if($this->hasExecuted()){
			$this->sendInventories();
			return false;
		}
		$this->shuffleActions();
		$this->validate();
		if(!$this->callExecuteEvent()){
			$this->sendInventories();
			return false;
		}
		foreach($this->actions as $action){
			if(!$action->onPreExecute($this->source)){
				$this->sendInventories();
				return false;
			}
		}
		foreach($this->actions as $action){
			if($action->execute($this->source)){
				$action->onExecuteSuccess($this->source);
			}else{
				$action->onExecuteFail($this->source);
			}
		}
		$this->hasExecuted = true;
		return true;
	}
	public function hasExecuted() : bool{
		return $this->hasExecuted;
	}}