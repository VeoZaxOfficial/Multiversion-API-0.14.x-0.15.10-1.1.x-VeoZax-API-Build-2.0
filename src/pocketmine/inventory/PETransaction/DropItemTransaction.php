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
use pocketmine\event\player\PlayerDropItemEvent;use pocketmine\item\Item;use pocketmine\Player;
class DropItemTransaction extends Transaction{
    public function __construct(Item $targetItem){
        $this->targetItem = $targetItem;
    }
	public function revert(Player $source) : void{
	}
    public function execute(TransactionQueue $transactionQueue) : bool{
        $player = $transactionQueue->getPlayer();
        $item = $this->getTargetItem();
        $inventory = $player->getInventory();
        if($inventory->contains($item)){
            $inventory->removeItem($item);
        }elseif($player->isCreative(true)){
            if(Item::getCreativeItemIndex($item, $player->getProtocol()) === -1){
                return $this->error("Player inventory not contains $item");
            }
        }else{
            return $this->error("Player inventory not contains $item");
        }
        $ev = new PlayerDropItemEvent($player, $item);
        $ev->call();
        if($ev->isCancelled()){
            $inventory->addItem($item);
            return true;
        }
        $player->dropItem($item);
        $inventory->sendHeldItem($player);
        $inventory->sendHeldItem($player->getViewers());
        $slot = $inventory->first($item, false);
        if($slot !== -1){
            $inventory->sendSlot($slot, $player);
        }else{
            $inventory->sendContents($player);
        }
        return true;
    }}