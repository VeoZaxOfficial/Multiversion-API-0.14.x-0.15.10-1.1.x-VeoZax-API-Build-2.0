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
namespace pocketmine\inventory\transaction\action;
use pocketmine\event\inventory\InventoryClickEvent;use pocketmine\inventory\Inventory;use pocketmine\inventory\transaction\InventoryTransaction;use pocketmine\item\Item;use pocketmine\Player;use function spl_object_hash;
class SlotChangeAction extends InventoryAction{
	protected $inventory;
	private $inventorySlot;
	public function __construct(Inventory $inventory, int $inventorySlot, Item $sourceItem, Item $targetItem){
		parent::__construct($sourceItem, $targetItem);
		$this->inventory = $inventory;
		$this->inventorySlot = $inventorySlot;
	}
	public function getInventory() : Inventory{
		return $this->inventory;
	}
	public function getSlot() : int{
		return $this->inventorySlot;
	}
	public function isValid(Player $source) : bool{
		return (
			$this->inventory->slotExists($this->inventorySlot) and
			$this->inventory->getItem($this->inventorySlot)->equalsExact($this->sourceItem)
		);
	}
  public function onPreExecute(Player $source) : bool{
    $ev = new InventoryClickEvent($this->inventory, $source, $this->inventorySlot, $this->sourceItem);
		$ev->call();
    return !$ev->isCancelled();
	}
	public function onAddToTransaction(InventoryTransaction $transaction) : void{
		$transaction->addInventory($this->inventory);
	}
	public function execute(Player $source) : bool{
		return $this->inventory->setItem($this->inventorySlot, $this->targetItem, false);
	}
	public function onExecuteSuccess(Player $source) : void{
		$viewers = $this->inventory->getViewers();
		unset($viewers[spl_object_hash($source)]);
		$this->inventory->sendSlot($this->inventorySlot, $viewers);
	}
	public function onExecuteFail(Player $source) : void{
		$this->inventory->sendSlot($this->inventorySlot, $source);
	}}