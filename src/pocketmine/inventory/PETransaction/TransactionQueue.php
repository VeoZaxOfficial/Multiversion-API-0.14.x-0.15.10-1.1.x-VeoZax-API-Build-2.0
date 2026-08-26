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
use pocketmine\event\inventory\InventoryClickEvent;use pocketmine\event\inventory\InventoryTransactionEvent;use pocketmine\inventory\ContainerInventory;use pocketmine\inventory\CraftingGrid;use pocketmine\inventory\PlayerInventory;use pocketmine\Player;use SplQueue;use function get_class;
class TransactionQueue{
    public const DEFAULT_ALLOWED_RETRIES = 5;
    protected $player;
    protected $transactionQueue;
    protected $transactionInventory;
    public function __construct(Player $player){
        $this->player = $player;
        $this->transactionInventory = new class($player, 6) extends CraftingGrid{};
        $this->transactionQueue = new SplQueue();
    }
    public function getPlayer() : Player{
        return $this->player;
    }
    public function getSource() : Player{
        return $this->player;
    }
    public function getTransactions() : ?SplQueue{
    	return $this->transactionQueue;
    }
    public function getInventory() : CraftingGrid{
        return $this->transactionInventory;
    }
    public function addTransaction(Transaction $transaction) : void{
        $this->transactionQueue->enqueue($transaction);
    }
    public function onCloseWindow() : void{
        while(!$this->transactionQueue->isEmpty()){
            $this->transactionQueue->dequeue()->execute($this);
        }
        foreach($this->transactionInventory->getContents() as $item){
            $this->transactionInventory->removeItem($item);
            $this->player->getInventory()->addItem($item);
        }
    }
  public function execute() : void{
    $failed = [];
    if($this->transactionQueue->isEmpty()){
      return;
    }
    ($ev = new InventoryTransactionEvent($this))->call();
    while(!$this->transactionQueue->isEmpty()){
      $transaction = $this->transactionQueue->dequeue();
      if(!$transaction instanceof DropItemTransaction){
        if($transaction->getInventory() instanceof ContainerInventory || $transaction->getInventory() instanceof PlayerInventory){
          ($event = new InventoryClickEvent($transaction->getInventory(), $this->player, $transaction->getSlot(), $transaction->getInventory()->getItem($transaction->getSlot())))->call();
          if($event->isCancelled()){
            $ev->setCancelled();
          }
        }
      }
      if($ev->isCancelled()){
        $transaction->revert($this->player); 
        continue;
      }elseif(!$transaction->execute($this)){
        $this->player->getServer()->getLogger()->debug("Can't execute " . get_class($transaction) . ": " . $transaction->getLastError());
        if($transaction->addFailure() >= self::DEFAULT_ALLOWED_RETRIES){
          $failed[] = $transaction;
        }else{
          $this->addTransaction($transaction);
        }
        continue;
      }
    }
    foreach($failed as $f){
      $f->revert($this->player);
    }
  }}