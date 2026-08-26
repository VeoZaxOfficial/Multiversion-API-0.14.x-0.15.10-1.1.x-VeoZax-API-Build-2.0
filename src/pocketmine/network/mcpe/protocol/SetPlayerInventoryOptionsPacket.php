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
namespace pocketmine\network\mcpe\protocol;
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\inventory\InventoryLayout;use pocketmine\network\mcpe\protocol\types\inventory\InventoryLeftTab;use pocketmine\network\mcpe\protocol\types\inventory\InventoryRightTab;
class SetPlayerInventoryOptionsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_PLAYER_INVENTORY_OPTIONS_PACKET;
	private InventoryLeftTab $leftTab;
	private InventoryRightTab $rightTab;
	private bool $filtering;
	private InventoryLayout $inventoryLayout;
	private InventoryLayout $craftingLayout;
	public static function create(InventoryLeftTab $leftTab, InventoryRightTab $rightTab, bool $filtering, InventoryLayout $inventoryLayout, InventoryLayout $craftingLayout) : self{
		$result = new self;
		$result->leftTab = $leftTab;
		$result->rightTab = $rightTab;
		$result->filtering = $filtering;
		$result->inventoryLayout = $inventoryLayout;
		$result->craftingLayout = $craftingLayout;
		return $result;
	}
	public function getLeftTab() : InventoryLeftTab{ return $this->leftTab; }
	public function getRightTab() : InventoryRightTab{ return $this->rightTab; }
	public function isFiltering() : bool{ return $this->filtering; }
	public function getInventoryLayout() : InventoryLayout{ return $this->inventoryLayout; }
	public function getCraftingLayout() : InventoryLayout{ return $this->craftingLayout; }
	protected function decodePayload() : void{
		$this->leftTab = InventoryLeftTab::fromPacket($this->getVarInt());
		$this->rightTab = InventoryRightTab::fromPacket($this->getVarInt());
		$this->filtering = $this->getBool();
		$this->inventoryLayout = InventoryLayout::fromPacket($this->getVarInt());
		$this->craftingLayout = InventoryLayout::fromPacket($this->getVarInt());
	}
	protected function encodePayload() : void{
		$this->putVarInt($this->leftTab->value);
		$this->putVarInt($this->rightTab->value);
		$this->putBool($this->filtering);
		$this->putVarInt($this->inventoryLayout->value);
		$this->putVarInt($this->craftingLayout->value);
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleSetPlayerInventoryOptions($this);
	}}