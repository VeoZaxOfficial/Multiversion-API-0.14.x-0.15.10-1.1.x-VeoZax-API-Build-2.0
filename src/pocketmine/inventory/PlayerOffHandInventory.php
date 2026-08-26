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
use BadMethodCallException;use pocketmine\Player;use pocketmine\entity\Human;use pocketmine\item\Item;use pocketmine\network\mcpe\protocol\MobEquipmentPacket;use pocketmine\network\mcpe\protocol\types\ContainerIds;
class PlayerOffHandInventory extends BaseInventory{
	protected $holder;
	public function __construct(Human $holder){
		$this->holder = $holder;
		parent::__construct();
	}
	public function getHolder() : Human{
		return $this->holder;
	}
	public function setItemInHand(Item $item) : void{
		$this->setItem(0, $item);
	}
	public function onSlotChange(int $index, Item $before, bool $send) : void{
	    if($send === true){
	    	foreach($this->viewers as $viewer){
		    	$this->sendContents($viewer); 
	    	}
		}
	    foreach($this->holder->getViewers() as $viewer){
		    $this->sendOffhand($viewer);
	    }
	}
    public function sendOffhand(Player $target) : void{
		$pk = new MobEquipmentPacket();
		$pk->entityRuntimeId = $this->holder->getId();
		$pk->item = $this->getItemInHand();
		$pk->inventorySlot = $this->getHeldItemIndex();
		$pk->hotbarSlot = $this->getHeldItemIndex();
		$pk->windowId = ContainerIds::OFFHAND;
		$target->sendDataPacket(clone $pk);
    }
	public function getItemInHand() : Item{
		return $this->getItem(0);
	}
	public function getHeldItemIndex() : int{
		return 0;
	}
	public function getName() : string{
		return "Offhand";
	}
	public function getDefaultSize() : int{
		return 1;
	}
	public function setSize(int $size) : void{
		throw new BadMethodCallException("OffHand can only carry one item at a time");
	}}