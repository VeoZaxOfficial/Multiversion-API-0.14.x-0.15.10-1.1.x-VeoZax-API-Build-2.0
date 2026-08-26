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
namespace pocketmine\network\mcpe\protocol\types;
use InvalidArgumentException;use InvalidStateException;use pocketmine\inventory\BeaconInventory;use pocketmine\inventory\EnchantInventory;use pocketmine\inventory\AnvilInventory;use pocketmine\inventory\CraftingGrid;use pocketmine\inventory\transaction\action\CreativeInventoryAction;use pocketmine\inventory\transaction\action\DropItemAction;use pocketmine\inventory\transaction\action\InventoryAction;use pocketmine\inventory\transaction\action\SlotChangeAction;use pocketmine\item\Item;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use UnexpectedValueException;
class NetworkInventoryAction{
	public const SOURCE_CONTAINER = 0;
	public const SOURCE_WORLD = 2; 
	public const SOURCE_CREATIVE = 3;
	public const SOURCE_UNTRACKED_INTERACTION_UI = 100;
	public const SOURCE_TODO = 99999;
	public const SOURCE_TYPE_CRAFTING_ADD_INGREDIENT = -2;
	public const SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT = -3;
	public const SOURCE_TYPE_CRAFTING_RESULT = -4;
	public const SOURCE_TYPE_CRAFTING_USE_INGREDIENT = -5;
	public const SOURCE_TYPE_ANVIL_INPUT = -10;
	public const SOURCE_TYPE_ANVIL_MATERIAL = -11;
	public const SOURCE_TYPE_ANVIL_RESULT = -12;
	public const SOURCE_TYPE_ANVIL_OUTPUT = -13;
	public const SOURCE_TYPE_ENCHANT_INPUT = -15;
	public const SOURCE_TYPE_ENCHANT_MATERIAL = -16;
	public const SOURCE_TYPE_ENCHANT_OUTPUT = -17;
	public const SOURCE_TYPE_TRADING_INPUT_1 = -20;
	public const SOURCE_TYPE_TRADING_INPUT_2 = -21;
	public const SOURCE_TYPE_TRADING_USE_INPUTS = -22;
	public const SOURCE_TYPE_TRADING_OUTPUT = -23;
	public const SOURCE_TYPE_BEACON = -24;
	public const SOURCE_TYPE_CONTAINER_DROP_CONTENTS = -100;
	public const ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM = 0;
	public const ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM = 1;
	public const ACTION_MAGIC_SLOT_DROP_ITEM = 0;
	public const ACTION_MAGIC_SLOT_PICKUP_ITEM = 1;
	public $sourceType;
	public $windowId;
	public $sourceFlags = 0;
	public $inventorySlot;
	public $oldItem;
	public $newItem;
	public $newItemStackId = null;
	public function read(NetworkBinaryStream $packet, bool $hasItemStackIds){
		$this->sourceType = $packet->getUnsignedVarInt();
		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$this->windowId = $packet->getVarInt();
				break;
			case self::SOURCE_WORLD:
				$this->sourceFlags = $packet->getUnsignedVarInt();
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_UNTRACKED_INTERACTION_UI:
			case self::SOURCE_TODO:
				$this->windowId = $packet->getVarInt();
				break;
			default:
				throw new UnexpectedValueException("Unknown inventory action source type $this->sourceType");
		}
		$this->inventorySlot = $packet->getUnsignedVarInt();
		$this->oldItem = $packet->getSlot();
		$this->newItem = $packet->getSlot();
        
		return $this;
	}
	public function write(NetworkBinaryStream $packet, bool $hasItemStackIds){
		$packet->putUnsignedVarInt($this->sourceType);
		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$packet->putVarInt($this->windowId);
				break;
			case self::SOURCE_WORLD:
				$packet->putUnsignedVarInt($this->sourceFlags);
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_UNTRACKED_INTERACTION_UI:
			case self::SOURCE_TODO:
				$packet->putVarInt($this->windowId);
				break;
			default:
				throw new InvalidArgumentException("Unknown inventory action source type $this->sourceType");
		}
		$packet->putUnsignedVarInt($this->inventorySlot);
		$packet->putSlot($this->oldItem);
		$packet->putSlot($this->newItem);
		
	}
	public function createInventoryAction(Player $player){
		if($this->oldItem->equalsExact($this->newItem)){
			return null;
		}
		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$slot = 0;
				if($this->inventorySlot === 27 && ($player->getWindow(Player::BEACON_WINDOW_ID) instanceof BeaconInventory)){
					$window = $player->getWindow(Player::BEACON_WINDOW_ID);
				}elseif(($this->inventorySlot === 14 or $this->inventorySlot === 15) && ($player->findEnchantInventory() instanceof EnchantInventory)){
					$window = $player->findEnchantInventory();
					$slot = ($this->inventorySlot === 14 ? 0 : 1);
		    	}elseif($this->windowId === ContainerIds::UI and $this->inventorySlot > 0){
					if($this->inventorySlot === 50){
						return null; 
					}
					if($this->inventorySlot >= 28 and $this->inventorySlot <= 31){
						$window = $player->getCraftingGrid();
						if($window->getGridWidth() !== CraftingGrid::SIZE_SMALL){
							throw new UnexpectedValueException("Expected small crafting grid");
						}
						$slot = $this->inventorySlot - 28;
					}elseif($this->inventorySlot >= 32 and $this->inventorySlot <= 40){
						$window = $player->getCraftingGrid();
						if($window->getGridWidth() !== CraftingGrid::SIZE_BIG){
							throw new UnexpectedValueException("Expected big crafting grid");
						}
						$slot = $this->inventorySlot - 32;
					}else{
						throw new UnexpectedValueException("Unhandled magic UI slot offset $this->inventorySlot");
					}
				}else{
					$window = $player->getWindow($this->windowId);
					$slot = $this->inventorySlot;
				}
				if($window !== null){
					return new SlotChangeAction($window, $slot, $this->oldItem, $this->newItem);
				}
				throw new UnexpectedValueException("Player " . $player->getName() . " has no open container with window ID $this->windowId");
			case self::SOURCE_WORLD:
				if($this->inventorySlot !== self::ACTION_MAGIC_SLOT_DROP_ITEM){
					throw new UnexpectedValueException("Only expecting drop-item world actions from the client!");
				}
				return new DropItemAction($this->newItem);
			case self::SOURCE_CREATIVE:
				switch($this->inventorySlot){
					case self::ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM:
						$type = CreativeInventoryAction::TYPE_DELETE_ITEM;
						break;
					case self::ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM:
						$type = CreativeInventoryAction::TYPE_CREATE_ITEM;
						break;
					default:
						throw new UnexpectedValueException("Unexpected creative action type $this->inventorySlot");
				}
				return new CreativeInventoryAction($this->oldItem, $this->newItem, $type);
			case self::SOURCE_UNTRACKED_INTERACTION_UI:
			case self::SOURCE_TODO:
				switch($this->windowId){
					case self::SOURCE_TYPE_BEACON:
						$window = $player->getWindow(Player::BEACON_WINDOW_ID);
						if(!($window instanceof BeaconInventory)){
							return null;
						}
						$this->inventorySlot = 0;
					    return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_ANVIL_INPUT: 
						$window = $player->getWindow(Player::BEACON_WINDOW_ID);
						if(!($window instanceof BeaconInventory)){
							return null;
						}
                        return new class($this->oldItem, $this->newItem, CreativeInventoryAction::TYPE_DELETE_ITEM) extends CreativeInventoryAction{
							public function isValid(Player $source) : bool{
								return true;
							}
						};
					case self::SOURCE_TYPE_CRAFTING_ADD_INGREDIENT:
					case self::SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT:
					case self::SOURCE_TYPE_CONTAINER_DROP_CONTENTS: 
						return new SlotChangeAction($player->getCraftingGrid(), $this->inventorySlot, $this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_CRAFTING_RESULT:
					case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
						return null;
				}
				throw new UnexpectedValueException("Player " . $player->getName() . " has no open container with window ID $this->windowId");
			default:
				throw new UnexpectedValueException("Unknown inventory source type $this->sourceType");
		}
	}}