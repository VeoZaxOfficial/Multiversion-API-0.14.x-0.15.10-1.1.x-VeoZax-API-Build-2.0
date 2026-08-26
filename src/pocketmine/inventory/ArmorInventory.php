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
use pocketmine\entity\Living;use pocketmine\item\Armor;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\network\mcpe\protocol\ContainerSetContentPacket;use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;use pocketmine\network\mcpe\protocol\InventoryContentPacket;use pocketmine\network\mcpe\protocol\InventorySlotPacket;use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use function array_merge;
class ArmorInventory extends BaseInventory{
	public const SLOT_HEAD = 0;
	public const SLOT_CHEST = 1;
	public const SLOT_LEGS = 2;
	public const SLOT_FEET = 3;
	protected $holder;
	public function __construct(Living $holder){
		$this->holder = $holder;
		parent::__construct();
	}
	public function getHolder() : Living{
		return $this->holder;
	}
	public function getName() : string{
		return "Armor";
	}
	public function getDefaultSize() : int{
		return 4;
	}
	public function getHelmet() : Item{
		return $this->getItem(self::SLOT_HEAD);
	}
	public function getChestplate() : Item{
		return $this->getItem(self::SLOT_CHEST);
	}
	public function getLeggings() : Item{
		return $this->getItem(self::SLOT_LEGS);
	}
	public function getBoots() : Item{
		return $this->getItem(self::SLOT_FEET);
	}
	public function setHelmet(Item $helmet) : bool{
		return $this->setItem(self::SLOT_HEAD, $helmet);
	}
	public function setChestplate(Item $chestplate) : bool{
		return $this->setItem(self::SLOT_CHEST, $chestplate);
	}
	public function setLeggings(Item $leggings) : bool{
		return $this->setItem(self::SLOT_LEGS, $leggings);
	}
	public function setBoots(Item $boots) : bool{
		return $this->setItem(self::SLOT_FEET, $boots);
	}
    public function setItem(int $index, Item $item, bool $send = true) : bool{
        if($item->getId() === Item::AIR){
            return parent::setItem($index, $item, $send);
        }
        if($item instanceof Armor){
            if($item->getArmorSlot() === $index){
                return parent::setItem($index, $item, $send);
            }
        }
        if($index === self::SLOT_HEAD && $item->getId() === Item::SKULL){
            return parent::setItem($index, $item, $send);
        }
        return false;
    }
	public function sendSlot(int $index, $target) : void{
		if($target instanceof Player){
			$target = [$target];
		}
		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->head = $this->getHelmet();
		$pk->chest = $this->getChestplate();
		$pk->legs = $this->getLeggings();
		$pk->feet = $this->getBoots();
        $pk->body = ItemFactory::get(Item::AIR);
		foreach($target as $player){
			if($player === $this->getHolder()){
                
        	    	$pk2 = new ContainerSetSlotPacket();
	            	$pk2->slot = $index;
	            	$pk2->item = $this->getItem($index);
	            	$pk2->windowid = $player->getWindowId($this);
                    $player->dataPacket(clone $pk2);
                
			}else{
				$player->dataPacket(clone $pk);
			}
		}
	}
	public function sendContents($target) : void{
		if($target instanceof Player){
			$target = [$target];
		}
		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->head = $this->getHelmet();
		$pk->chest = $this->getChestplate();
		$pk->legs = $this->getLeggings();
		$pk->feet = $this->getBoots();
        $pk->body = ItemFactory::get(Item::AIR);
		foreach($target as $player){
			if($player === $this->getHolder()){
			    
			        $pk2 = new ContainerSetContentPacket();
		        	$pk2->windowid = $player->getWindowId($this);
		        	$pk2->targetEid = $player->getId();
		        	$pk2->slots = $this->getContents(true);
			        $player->dataPacket(clone $pk2);
			    
			}else{
				$player->dataPacket(clone $pk);
			}
		}
	}
	public function onSlotChange(int $index, Item $before, bool $send) : void{
		$this->sendSlot($index, $this->getViewers());
	}
	public function getViewers() : array{
		return array_merge(parent::getViewers(), $this->holder->getViewers());
	}}