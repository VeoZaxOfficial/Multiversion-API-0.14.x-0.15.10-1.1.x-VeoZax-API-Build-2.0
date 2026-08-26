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
use pocketmine\level\Position;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\network\mcpe\protocol\types\WindowTypes;use pocketmine\tile\EnderChest;
class EnderChestInventory extends ChestInventory{
	protected $holder;
	public function __construct(){
		ContainerInventory::__construct(new Position());
	}
	public function getNetworkType() : int{
		return WindowTypes::CONTAINER;
	}
	public function getName() : string{
		return "EnderChest";
	}
	public function getDefaultSize() : int{
		return 27;
	}
	public function setHolderPosition(EnderChest $enderChest){
		$this->holder->setComponents($enderChest->getFloorX(), $enderChest->getFloorY(), $enderChest->getFloorZ());
		$this->holder->setLevel($enderChest->getLevelNonNull());
	}
	protected function getOpenSound() : int{
		return LevelSoundEventPacket::SOUND_ENDERCHEST_OPEN;
	}
	protected function getCloseSound() : int{
		return LevelSoundEventPacket::SOUND_ENDERCHEST_CLOSED;
	}
	public function getHolder(){
		return $this->holder;
	}}