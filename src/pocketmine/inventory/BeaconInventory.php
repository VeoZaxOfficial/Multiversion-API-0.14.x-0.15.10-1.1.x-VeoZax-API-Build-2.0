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


namespace pocketmine\inventory;
use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\types\ContainerIds;use pocketmine\network\mcpe\protocol\types\WindowTypes;use pocketmine\Player;use pocketmine\tile\Beacon;
class BeaconInventory extends ContainerInventory{
	public function __construct(Beacon $tile){
		parent::__construct($tile);
	}
	public function getName() : string{
		return "Beacon";
	}
	public function getDefaultSize() : int{
		return 1;
	}
	public function getResultSlot() : int{
		return 0;
	}
	public function getNetworkType() : int{
		return WindowTypes::BEACON;
	}
	public function onClose(Player $who) : void{
		parent::onClose($who);
		$this->getHolder()->getLevelNonNull()->dropItem($this->getHolder()->add(0.5, 0.5, 0.5), $this->getItem($this->getResultSlot()));
		$this->clear($this->getResultSlot());
	}
	public function sendContents($target) : void{
		if($target instanceof Player){
			$target = [$target];
		}
		foreach($target as $player){
			if(($id = $player->getWindowId($this)) === ContainerIds::NONE){
				$this->close($player);
				continue;
			}
	    	
		    	continue;
			
			parent::sendContents($player);
		}
	}
	public function sendSlot(int $index, $target) : void{
		if($target instanceof Player){
			$target = [$target];
		}
		foreach($target as $player){
			if(($id = $player->getWindowId($this)) === ContainerIds::NONE){
				$this->close($player);
				continue;
			}
	    	
		    	continue;
			
			parent::sendSlot($index, $player);
		}
	}}