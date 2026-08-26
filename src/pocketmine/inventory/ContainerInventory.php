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
use pocketmine\entity\Entity;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\ContainerClosePacket;use pocketmine\network\mcpe\protocol\ContainerOpenPacket;use pocketmine\Player;
abstract class ContainerInventory extends BaseInventory{
	protected $holder;
	public function __construct(Vector3 $holder, array $items = [], int $size = null, string $title = null){
		$this->holder = $holder;
		parent::__construct($items, $size, $title);
	}
	public function onOpen(Player $who) : void{
		parent::onOpen($who);
		$pk = new ContainerOpenPacket();
		$pk->windowId = $who->getWindowId($this);
		$pk->type = $this->getNetworkType();
		$pk->slots = $this->getSize();
		$holder = $this->getHolder();
		$pk->x = $pk->y = $pk->z = 0;
		$pk->entityUniqueId = -1;
		if($holder instanceof Entity){
			$pk->entityUniqueId = $holder->getId();
		}elseif($holder instanceof Vector3){
			$pk->x = $holder->getFloorX();
			$pk->y = $holder->getFloorY();
			$pk->z = $holder->getFloorZ();
		}
		$who->setCurrentWindowType($pk->type);
		$who->dataPacket($pk);
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
	abstract public function getNetworkType() : int;
	public function getHolder(){
		return $this->holder;
	}}