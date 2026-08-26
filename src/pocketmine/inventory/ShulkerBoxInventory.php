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
use pocketmine\network\mcpe\protocol\BlockEventPacket;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\network\mcpe\protocol\types\WindowTypes;use pocketmine\Player;use pocketmine\tile\ShulkerBox;use function count;
class ShulkerBoxInventory extends ContainerInventory{
	protected $holder;
	public function __construct(ShulkerBox $tile){
		parent::__construct($tile);
	}
	public function getName() : string{
		return "Shulker Box";
	}
	public function getDefaultSize() : int{
		return 27;
	}
	public function getNetworkType() : int{
		return WindowTypes::CONTAINER;
	}
	public function getHolder(){
		return $this->holder;
	}
	protected function getOpenSound() : int{
		return LevelSoundEventPacket::SOUND_SHULKERBOX_OPEN;
	}
	protected function getCloseSound() : int{
		return LevelSoundEventPacket::SOUND_SHULKERBOX_CLOSED;
	}
	public function onOpen(Player $who) : void{
		parent::onOpen($who);
		if(count($this->getViewers()) === 1 and $this->getHolder()->isValid()){
			$this->broadcastBlockEventPacket(true);
			$this->getHolder()->getLevelNonNull()->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), $this->getOpenSound());
		}
	}
	public function onClose(Player $who) : void{
		if(count($this->getViewers()) === 1 and $this->getHolder()->isValid()){
			$this->broadcastBlockEventPacket(false);
			$this->getHolder()->getLevelNonNull()->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), $this->getCloseSound());
		}
		parent::onClose($who);
	}
	protected function broadcastBlockEventPacket(bool $isOpen) : void{
		$holder = $this->getHolder();
		$pk = new BlockEventPacket();
		$pk->x = (int) $holder->x;
		$pk->y = (int) $holder->y;
		$pk->z = (int) $holder->z;
		$pk->eventType = 1; 
		$pk->eventData = $isOpen ? 1 : 0;
		$holder->getLevelNonNull()->broadcastPacketToViewers($holder, $pk);
	}}