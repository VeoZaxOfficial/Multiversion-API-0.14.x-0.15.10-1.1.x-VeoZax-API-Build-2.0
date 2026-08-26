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
use pocketmine\entity\EntityIds;use pocketmine\entity\passive\Horse;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\nbt\NetworkLittleEndianNBTStream;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\ShortTag;use pocketmine\nbt\tag\StringTag;use pocketmine\network\mcpe\protocol\ContainerClosePacket;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\types\WindowTypes;use pocketmine\network\mcpe\protocol\UpdateEquipPacket;use pocketmine\Player;
class HorseInventory extends AbstractHorseInventory{
	protected $holder;
	public function getName() : string{
		return "Horse";
	}
	public function getDefaultSize() : int{
		return 2;
	}
	public function getArmor() : Item{
		return $this->getItem(1);
	}
	public function setArmor(Item $armor) : void{
		$this->setItem(1, $armor);
		foreach($this->viewers as $player){
			$this->sendArmor($player);
		}
	}
	public function getNetworkType() : int{
		return WindowTypes::HORSE;
	}
	public function onSlotChange(int $index, Item $before, bool $send) : void{
		parent::onSlotChange($index, $before, $send);
		if($index === 1){
			foreach($this->viewers as $player){
				$this->sendArmor($player);
			}
			$this->holder->level->broadcastLevelSoundEvent($this->holder, LevelSoundEventPacket::SOUND_ARMOR, -1, EntityIds::HORSE);
		}
	}
	public function getHolder(){
		return $this->holder;
	}
	public function sendArmor(Player $player) : void{
		$air = ItemFactory::get(Item::AIR);
		$pk = new MobArmorEquipmentPacket();
		$pk->entityRuntimeId = $this->getHolder()->getId();
		$pk->head = $air;
		$pk->chest = $this->getItem(1);
		$pk->legs = $air;
		$pk->feet = $air;
        $pk->body = $air;
		$player->sendDataPacket($pk);
	}
	public function onOpen(Player $who) : void{
		parent::onOpen($who);
		$pk = new UpdateEquipPacket();
		$pk->entityUniqueId = $this->holder->getId();
		$pk->windowSlotCount = 0;
		$pk->windowType = $this->getNetworkType();
		$pk->windowId = $who->getWindowId($this);
		$pk->namedtag = (new NetworkLittleEndianNBTStream())->write($this->getNamedtag($who->getProtocol()));
		$who->sendDataPacket($pk);
	    $this->sendArmor($who);
		$this->sendContents($who);
	}
	public function onClose(Player $who) : void{
		$pk = new ContainerClosePacket();
		$pk->windowId = $who->getWindowId($this);
		$pk->windowType = $who->getCurrentWindowType();
		$pk->server = $who->getClosingWindowId() !== $pk->windowId;
		$who->dataPacket($pk);
		parent::onClose($who);
	}
	public function getNamedtag(int $playerProtocol) : CompoundTag{
		
			return new CompoundTag("", [
				new ListTag("slots", [
					new CompoundTag("", [
						new ListTag("acceptedItems", [
							new CompoundTag("", [
								(ItemFactory::get(Item::SADDLE))->nbtSerialize(-1, "slotItem")
							])
						]),
						$this->getSaddle()->nbtSerialize(-1, "item"),
						new IntTag("slotNumber", 0)
					]),
					new CompoundTag("", [
						new ListTag("acceptedItems", [
							new CompoundTag("", [
								(ItemFactory::get(Item::HORSE_ARMOR_DIAMOND))->nbtSerialize(-1, "slotItem")
							]),
							new CompoundTag("", [
								(ItemFactory::get(Item::HORSE_ARMOR_GOLD))->nbtSerialize(-1, "slotItem")
							]),
							new CompoundTag("", [
								(ItemFactory::get(Item::HORSE_ARMOR_IRON))->nbtSerialize(-1, "slotItem")
							]),
							new CompoundTag("", [
								(ItemFactory::get(Item::HORSE_ARMOR_LEATHER))->nbtSerialize(-1, "slotItem")
							])
						]),
						$this->getArmor()->nbtSerialize(-1, "item"),
						new IntTag("slotNumber", 1)
					])
				])
			]);
	    
	}
	public function translateHorseArmorIdToStringName(int $armorId) : string{
		return match($armorId){
			Item::HORSE_ARMOR_DIAMOND => "minecraft:diamond_horse_armor",
			Item::HORSE_ARMOR_GOLD => "minecraft:golden_horse_armor",
			Item::HORSE_ARMOR_IRON => "minecraft:iron_horse_armor",
			Item::HORSE_ARMOR_LEATHER => "minecraft:leather_horse_armor",
			default => "minecraft:leather_horse_armor",
	    };
	}}