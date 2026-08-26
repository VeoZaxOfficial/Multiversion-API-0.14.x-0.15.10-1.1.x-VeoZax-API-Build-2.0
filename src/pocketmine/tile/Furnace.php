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
use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\event\inventory\FurnaceBurnEvent;use pocketmine\event\inventory\FurnaceSmeltEvent;use pocketmine\inventory\FurnaceInventory;use pocketmine\inventory\FurnaceRecipe;use pocketmine\inventory\Inventory;use pocketmine\inventory\InventoryEventProcessor;use pocketmine\inventory\InventoryHolder;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\level\Level;use pocketmine\nbt\tag\CompoundTag;use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;use function ceil;use function max;
class Furnace extends Spawnable implements InventoryHolder, Container, Nameable{
	use NameableTrait {
		addAdditionalSpawnData as addNameSpawnData;
	}
	use ContainerTrait;
	public const TAG_BURN_TIME = "BurnTime";
	public const TAG_COOK_TIME = "CookTime";
	public const TAG_MAX_TIME = "MaxTime";
	protected $inventory;
	private $burnTime;
	private $cookTime;
	private $maxTime;
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		if($this->burnTime > 0){
			$this->scheduleUpdate();
		}
	}
	protected function readSaveData(CompoundTag $nbt) : void{
		$this->burnTime = max(0, $nbt->getShort(self::TAG_BURN_TIME, 0, true));
		$this->cookTime = $nbt->getShort(self::TAG_COOK_TIME, 0, true);
		if($this->burnTime === 0){
			$this->cookTime = 0;
		}
		$this->maxTime = $nbt->getShort(self::TAG_MAX_TIME, 0, true);
		if($this->maxTime === 0){
			$this->maxTime = $this->burnTime;
		}
		$this->loadName($nbt);
		$this->inventory = new FurnaceInventory($this);
		$this->loadItems($nbt);
		$this->inventory->setEventProcessor(new class($this) implements InventoryEventProcessor{
			private $furnace;
			public function __construct(Furnace $furnace){
				$this->furnace = $furnace;
			}
			public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem, Item $newItem) : ?Item{
				$this->furnace->scheduleUpdate();
				return $newItem;
			}
		});
	}
	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setShort(self::TAG_BURN_TIME, $this->burnTime);
		$nbt->setShort(self::TAG_COOK_TIME, $this->cookTime);
		$nbt->setShort(self::TAG_MAX_TIME, $this->maxTime);
		$this->saveName($nbt);
		$this->saveItems($nbt);
	}
	public function getDefaultName() : string{
		return "Furnace";
	}
	public function close() : void{
		if(!$this->closed){
			$this->inventory->removeAllViewers(true);
			$this->inventory = null;
			parent::close();
		}
	}
	public function getInventory(){
		return $this->inventory;
	}
	public function getRealInventory(){
		return $this->getInventory();
	}
	protected function checkFuel(Item $fuel){
		$ev = new FurnaceBurnEvent($this, $fuel, $fuel->getFuelTime());
		$ev->call();
		if($ev->isCancelled()){
			return;
		}
		$this->maxTime = $this->burnTime = $ev->getBurnTime();
		if($this->getBlock()->getId() === Block::FURNACE){
			$this->getLevelNonNull()->setBlock($this, BlockFactory::get(Block::BURNING_FURNACE, $this->getBlock()->getDamage()), true);
		}
		if($this->burnTime > 0 and $ev->isBurning()){
			$fuel->pop();
			$this->inventory->setFuel($fuel);
		}
	}
	protected function getFuelTicksLeft() : int{
		return $this->maxTime > 0 ? (int) ceil($this->burnTime / $this->maxTime * 200) : 0;
	}
	public function onUpdate() : bool{
		if($this->closed){
			return false;
		}
		$this->timings->startTiming();
		$prevCookTime = $this->cookTime;
		$prevFuelTicksLeft = $this->getFuelTicksLeft();
		$ret = false;
		$fuel = $this->inventory->getFuel();
		$raw = $this->inventory->getSmelting();
		$product = $this->inventory->getResult();
		$smelt = $this->server->getCraftingManager()->matchFurnaceRecipe($raw);
		$canSmelt = ($smelt instanceof FurnaceRecipe and $raw->getCount() > 0 and (($smelt->getResult()->equals($product) and $product->getCount() < $product->getMaxStackSize()) or $product->isNull()));
		if($this->burnTime <= 0 and $canSmelt and $fuel->getFuelTime() > 0 and $fuel->getCount() > 0){
			$this->checkFuel($fuel);
		}
		if($this->burnTime > 0){
			--$this->burnTime;
			if($smelt instanceof FurnaceRecipe and $canSmelt){
				++$this->cookTime;
				if($this->cookTime >= 200){ 
					$product = ItemFactory::get($smelt->getResult()->getId(), $smelt->getResult()->getDamage(), $product->getCount() + 1);
					$ev = new FurnaceSmeltEvent($this, $raw, $product);
					$ev->call();
					if(!$ev->isCancelled()){
						$this->inventory->setResult($ev->getResult());
						$raw->pop();
						$this->inventory->setSmelting($raw);
					}
					$this->cookTime -= 200;
				}
			}elseif($this->burnTime <= 0){
				$this->burnTime = $this->cookTime = $this->maxTime = 0;
			}else{
				$this->cookTime = 0;
			}
			$ret = true;
		}else{
			if($this->getBlock()->getId() === Block::BURNING_FURNACE){
				$this->getLevelNonNull()->setBlock($this, BlockFactory::get(Block::FURNACE, $this->getBlock()->getDamage()), true);
			}
			$this->burnTime = $this->cookTime = $this->maxTime = 0;
		}
		$packets = [];
		if($prevCookTime !== $this->cookTime){
			$pk = new ContainerSetDataPacket();
			$pk->property = ContainerSetDataPacket::PROPERTY_FURNACE_TICK_COUNT;
			$pk->value = $this->cookTime;
			$packets[] = $pk;
		}
		$fuelTicksLeft = $this->getFuelTicksLeft();
		if($prevFuelTicksLeft !== $fuelTicksLeft){
			$pk = new ContainerSetDataPacket();
			$pk->property = ContainerSetDataPacket::PROPERTY_FURNACE_LIT_TIME;
			$pk->value = $fuelTicksLeft;
			$packets[] = $pk;
		}
		if(!empty($packets)){
			foreach($this->getInventory()->getViewers() as $player){
				$windowId = $player->getWindowId($this->getInventory());
				if($windowId > 0){
					foreach($packets as $pk){
						$pk->windowId = $windowId;
						$player->dataPacket(clone $pk);
					}
				}
			}
		}
		$this->timings->stopTiming();
		return $ret;
	}
	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		$nbt->setShort(self::TAG_BURN_TIME, $this->burnTime);
		$nbt->setShort(self::TAG_COOK_TIME, $this->cookTime);
		$this->addNameSpawnData($nbt);
	}}