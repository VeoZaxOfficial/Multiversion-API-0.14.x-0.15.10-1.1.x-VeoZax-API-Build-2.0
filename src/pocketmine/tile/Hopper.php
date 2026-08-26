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
namespace pocketmine\tile;
use pocketmine\inventory\ShulkerBoxInventory;use pocketmine\inventory\HopperInventory;use pocketmine\inventory\FurnaceInventory;use pocketmine\nbt\tag\CompoundTag;use pocketmine\item\Item;use pocketmine\entity\object\ItemEntity as DroppedItem;use pocketmine\inventory\InventoryHolder;use pocketmine\tile\Furnace;use pocketmine\math\Vector3;use pocketmine\math\AxisAlignedBB;
class Hopper extends Spawnable implements InventoryHolder, Container, Nameable{
    use ContainerTrait;
    use NameableTrait;
    private const TAG_TRANSFER_COOLDOWN = "TransferCooldown";
    private $inventory;
    private $transferCooldown = 0;
    public function readSaveData(CompoundTag $nbt) : void{
        $this->inventory = new HopperInventory($this);
        $this->loadItems($nbt);
        $this->loadName($nbt);
        $this->transferCooldown = $nbt->getInt(self::TAG_TRANSFER_COOLDOWN, 0);
        $this->scheduleUpdate();
    }
    protected function writeSaveData(CompoundTag $nbt) : void{
        $this->saveItems($nbt);
        $this->saveName($nbt);
        $nbt->setInt(self::TAG_TRANSFER_COOLDOWN, $this->transferCooldown);
    }
    public function close() : void{
        if(!$this->closed){
            $this->inventory->removeAllViewers(true);
            $this->inventory = null;
            parent::close();
        }
    }
    public function getDefaultName() : string{
        return "Hopper";
    }
    public function getInventory(){
        return $this->inventory;
    }
    public function getRealInventory(){
        return $this->inventory;
    }
    public function resetCooldownTicks(){
        $this->transferCooldown = 8;
    }
    public function onUpdate(): bool{
		$pickupCollisionBox = new AxisAlignedBB(
		    $this->getBlock()->getX(),
		    $this->getBlock()->getY() + 1,
		    $this->getBlock()->getZ(),
		    $this->getBlock()->getX() + 1,
		    $this->getBlock()->getY() + 1.75,
		    $this->getBlock()->getZ() + 1
		);
		foreach($this->getBlock()->getLevel()->getNearbyEntities($pickupCollisionBox) as $entity){
			if($entity->isClosed() || $entity->isFlaggedForDespawn() || !$entity instanceof DroppedItem){
				continue;
			}
			$item = $entity->getItem();
			if(!$this->inventory->canAddItem($item)){
				continue;
			}
			$this->inventory->addItem($item);
			$entity->flagForDespawn();
        }
        if($this->transferCooldown > 0){ 
            $this->transferCooldown--;
            return true;
        }
        $source = $this->getLevel()->getTile($this->getBlock()->getSide(Vector3::SIDE_UP));
        if($source instanceof ShulkerBox){
            return true;
        }
        if($source instanceof Tile and $source instanceof InventoryHolder and !($source instanceof Furnace)){
            $inventory = $source->getInventory();
            $item = clone $inventory->getItem($inventory->firstOccupied());
            $item->setCount(1);
            if($this->inventory->canAddItem($item)){
                $this->inventory->addItem($item);
                $inventory->removeItem($item);
                $this->resetCooldownTicks();
                if($source instanceof Hopper){
                    $source->resetCooldownTicks();
                }
            }
        }
        if(!($this->getLevel()->getTile($this->getBlock()->getSide(Vector3::SIDE_DOWN)) instanceof Hopper)){
            $target = $this->getLevel()->getTile($this->getBlock()->getSide($this->getBlock()->getDamage()));
            if($target instanceof Tile and $target instanceof InventoryHolder){
                $inv = $target->getInventory();
                foreach($this->inventory->getContents() as $item){
                    if($item->getId() === Item::AIR or $item->getCount() < 1){
                        continue;
                    }
                    $targetItem = clone $item;
                    $targetItem->setCount(1);
                    if(!(($targetItem->getId() === Item::SHULKER_BOX or $targetItem->getId() === Item::UNDYED_SHULKER_BOX) && $inv instanceof ShulkerBoxInventory)){
                        if($inv instanceof FurnaceInventory){
                            $smelting = $inv->getSmelting();
                            if(!$smelting->isNull()){
                                if(!$smelting->equals($targetItem, true, false) or $smelting->getCount() >= $smelting->getMaxStackSize()){
                                    break;
                                }
                            }
                        }
                        if($inv->canAddItem($targetItem)){
                            $this->inventory->removeItem($targetItem);
                            $inv->addItem($targetItem);
                            $this->resetCooldownTicks();
                            if($target instanceof Hopper){
                                $target->resetCooldownTicks();
                            }
                            break;
                        }
                    }
                }
            }
        }
        return true;
    }}