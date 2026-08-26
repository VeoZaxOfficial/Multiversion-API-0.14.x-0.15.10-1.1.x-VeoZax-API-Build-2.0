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
use InvalidArgumentException;use pocketmine\inventory\InventoryHolder;use pocketmine\inventory\ShulkerBoxInventory;use pocketmine\item\Item;use pocketmine\level\Level;use pocketmine\math\Vector3;use pocketmine\nbt\tag\CompoundTag;use pocketmine\Player;
class ShulkerBox extends Spawnable implements InventoryHolder, Container, Nameable{
    use NameableTrait {
        addAdditionalSpawnData as addNameSpawnData;
    }
    use ContainerTrait;
    protected $facing = self::SIDE_UP;
    protected $inventory;
    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
    }
    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, ?int $face = null, ?Item $item = null, ?Player $player = null) : void{
        if($item !== null and $item->hasCustomName()){
            $nbt->setString(Nameable::TAG_CUSTOM_NAME, $item->getCustomName());
        }
        if($face === null){
            $face = 1;
        }
        $nbt->setByte("facing", $face);
    }
    public function getDefaultName() : string{
        return "Shulker Box";
    }
    public function close() : void{
        if ($this->isClosed()) {
            $this->inventory->removeAllViewers(true);
            $this->inventory = null;
            parent::close();
        }
    }
    public function getRealInventory() : ?ShulkerBoxInventory{
        return $this->inventory;
    }
    public function getInventory() : ?ShulkerBoxInventory{
        return $this->inventory;
    }
    public function getFacing() : int{
        return $this->facing;
    }
    public function setFacing(int $face) : void{
        if($face < 0 or $face > 5){
            throw new InvalidArgumentException("Facing must be in range 0-5, not " . $face);
        }
        $this->facing = $face;
    }
    protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
        $nbt->setByte("facing", $this->facing);
        $this->addNameSpawnData($nbt);
    }
    protected function readSaveData(CompoundTag $nbt) : void{
        $this->loadName($nbt);
        $this->inventory = new ShulkerBoxInventory($this);
        $this->loadItems($nbt);
        $this->facing = $nbt->getByte("facing", 1);
    }
    protected function writeSaveData(CompoundTag $nbt) : void{
        $this->saveName($nbt);
        $this->saveItems($nbt);
        $nbt->setByte("facing", $this->facing);
    }
    public function writeBlockData(CompoundTag $nbt){
        $this->saveItems($nbt);
    }}