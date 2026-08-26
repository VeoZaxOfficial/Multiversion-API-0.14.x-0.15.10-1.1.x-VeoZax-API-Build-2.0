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
namespace pocketmine\inventory\PETransaction;
use pocketmine\inventory\AnvilInventory;use pocketmine\inventory\EnchantInventory;use pocketmine\inventory\Inventory;use pocketmine\item\Item;use pocketmine\item\ItemIds;use pocketmine\Player;
use function abs;
class Transaction{
    protected $inventory;
    protected $slot;
    protected $targetItem;
    protected $failures = 0;
    protected $errorLog = [];
    public function __construct(Inventory $inventory, int $slot, Item $targetItem){
        $this->inventory = $inventory;
        $this->slot = $slot;
        $this->targetItem = $targetItem;
    }
    public function getInventory() : ?Inventory{
        return $this->inventory;
    }
    public function getSlot() : int{
        return $this->slot;
    }
    public function getTargetItem() : Item{
        return clone $this->targetItem;
    }
    public function getSourceItem() : Item{
        return $this->inventory->getItem($this->slot);
    }
    public function setTargetItem(Item $item) : void{
        $this->targetItem = $item;
    }
    public function addFailure() : int{
        return ++$this->failures;
    }
	public function revert(Player $source) : void{
		if($this->getInventory() instanceof AnvilInventory || $this->getInventory() instanceof EnchantInventory){
			return;
		}
		$this->inventory->sendSlot($this->slot, $source);
	}
    public function getChange() : ?array{
        $sourceItem = $this->getInventory()->getItem($this->slot);
        if($sourceItem->equalsExact($this->targetItem)){
            return null;
        }elseif($sourceItem->equals($this->targetItem, true, true)){
            $item = clone $sourceItem;
            $countDiff = $this->targetItem->getCount() - $sourceItem->getCount();
            $item->setCount(abs($countDiff));
            if($countDiff < 0){     
                return ["in" => null,
                    "out" => $item];
            }elseif($countDiff > 0){ 
                return ["in" => $item,
                    "out" => null];
            }else{
                return null;
            }
        }elseif($sourceItem->getId() !== ItemIds::AIR && $this->targetItem->getId() === ItemIds::AIR){
            return ["in" => null,
                "out" => clone $sourceItem];
        }elseif($sourceItem->getId() === ItemIds::AIR && $this->targetItem->getId() !== ItemIds::AIR){
            return ["in" => $this->getTargetItem(),
                "out" => null];
        }else{
            return ["in" => $this->getTargetItem(),
                "out" => clone $sourceItem];
        }
    }
    public function execute(TransactionQueue $transactionQueue) : bool{
        $change = $this->getChange();
        $player = $transactionQueue->getPlayer();
        if($change === null){
            $this->getInventory()->setItem($this->getSlot(), $this->getTargetItem(), false);
            return true;
        }
        if($change["out"] instanceof Item){
            if(!$this->getInventory()->getItem($this->getSlot())->equals($change["out"], $change["out"]->hasAnyDamageValue(), !$change["out"]->hasNamedTag())){
                return $this->error("Player inventory not contains " . $change["out"] . " in slot " . $this->getSlot() . ". Have " . $this->getInventory()->getItem($this->getSlot()));
            }
        }
        if($change["in"] instanceof Item){
            if($transactionQueue->getInventory()->contains($change["in"])){
            }elseif($player->isCreative(true) and Item::getCreativeItemIndex($change["in"], $player->getProtocol()) !== -1){
                $transactionQueue->getInventory()->addItem($change["in"]);
            }else{
                return $this->error("Transaction inventory not contains " . $change["in"] . ". Transaction inventory contents: " . implode("; ", $transactionQueue->getInventory()->getContents()));
            }
        }
        if($change["out"] instanceof Item){
            $transactionQueue->getInventory()->addItem($change["out"]);
        }
        if($change["in"] instanceof Item){
            $transactionQueue->getInventory()->removeItem($change["in"]);
        }
        $this->getInventory()->setItem($this->getSlot(), $this->getTargetItem(), false);
        return true;
    }
    public function error(string $error) : bool{
        $this->errorLog[] = $error;
        return false;
    }
    public function getLastError() : ?string{
        return array_shift($this->errorLog);
    }}