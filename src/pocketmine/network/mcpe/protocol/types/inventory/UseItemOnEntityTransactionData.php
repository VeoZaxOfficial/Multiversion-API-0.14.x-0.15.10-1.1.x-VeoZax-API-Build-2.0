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
namespace pocketmine\network\mcpe\protocol\types\inventory;
use pocketmine\math\Vector3;use pocketmine\item\Item;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;
class UseItemOnEntityTransactionData extends TransactionData{
	use GetTypeIdFromConstTrait;
	public const ID = InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY;
	public const ACTION_INTERACT = 0;
	public const ACTION_ATTACK = 1;
	public const ACTION_ITEM_INTERACT = 2;
	private $entityRuntimeId;
	private $actionType;
	private $hotbarSlot;
	private $itemInHand;
	private $playerPos;
	private $clickPos;
	public function getEntityRuntimeId() : int{
		return $this->entityRuntimeId;
	}
	public function getActionType() : int{
		return $this->actionType;
	}
	public function getHotbarSlot() : int{
		return $this->hotbarSlot;
	}
	public function getItemInHand() : Item{
		return $this->itemInHand;
	}
	public function getPlayerPos() : Vector3{
		return $this->playerPos;
	}
	public function getClickPos() : Vector3{
		return $this->clickPos;
	}
	protected function decodeData(NetworkBinaryStream $stream) : void{
		$this->entityRuntimeId = $stream->getEntityRuntimeId();
		$this->actionType = $stream->getUnsignedVarInt();
		$this->hotbarSlot = $stream->getVarInt();
		$this->itemInHand = $stream->getSlot();
		$this->playerPos = $stream->getVector3();
		$this->clickPos = $stream->getVector3();
	}
	protected function encodeData(NetworkBinaryStream $stream) : void{
		$stream->putEntityRuntimeId($this->entityRuntimeId);
		$stream->putUnsignedVarInt($this->actionType);
		$stream->putVarInt($this->hotbarSlot);
		$stream->putSlot($this->itemInHand);
		$stream->putVector3($this->playerPos);
		$stream->putVector3($this->clickPos);
	}
	public static function new(array $actions, int $entityRuntimeId, int $actionType, int $hotbarSlot, Item $itemInHand, Vector3 $playerPos, Vector3 $clickPos) : self{
		$result = new self;
		$result->actions = $actions;
		$result->entityRuntimeId = $entityRuntimeId;
		$result->actionType = $actionType;
		$result->hotbarSlot = $hotbarSlot;
		$result->itemInHand = $itemInHand;
		$result->playerPos = $playerPos;
		$result->clickPos = $clickPos;
		return $result;
	}}