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
use pocketmine\math\Vector3;use pocketmine\item\Item;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;
class UseItemTransactionData extends TransactionData{
	use GetTypeIdFromConstTrait;
	public const ID = InventoryTransactionPacket::TYPE_USE_ITEM;
	public const ACTION_CLICK_BLOCK = 0;
	public const ACTION_CLICK_AIR = 1;
	public const ACTION_BREAK_BLOCK = 2;
	private $actionType;
	private $triggerType;
	private $blockPos;
	private $face;
	private $hotbarSlot;
	private $itemInHand;
	private $playerPos;
	private $clickPos;
	private $blockRuntimeId;
	private $clientInteractPrediction;
	public function getActionType() : int{
		return $this->actionType;
	}
	public function getTriggerType() : TriggerType{
		return $this->triggerType;
	}
	public function getBlockPos() : Vector3{
		return $this->blockPos;
	}
	public function getFace() : int{
		return $this->face;
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
	public function getBlockRuntimeId() : int{
		return $this->blockRuntimeId;
	}
    public function getClientInteractPrediction() : PredictedResult{
		return $this->clientInteractPrediction;
    }
	protected function decodeData(NetworkBinaryStream $stream) : void{
		$this->actionType = $stream->getUnsignedVarInt();
        
		$x = $y = $z = 0;
		$stream->getBlockPosition($x, $y, $z);
		$this->blockPos = new Vector3($x, $y, $z);
		$this->face = $stream->getVarInt();
		$this->hotbarSlot = $stream->getVarInt();
		$this->itemInHand = $stream->getSlot();
		$this->playerPos = $stream->getVector3();
		$this->clickPos = $stream->getVector3();
		
	}
	protected function encodeData(NetworkBinaryStream $stream) : void{
		$stream->putUnsignedVarInt($this->actionType);
		
		$stream->putBlockPosition($this->blockPos->x, $this->blockPos->y, $this->blockPos->z);
		$stream->putVarInt($this->face);
		$stream->putVarInt($this->hotbarSlot);
		$stream->putSlot($this->itemInHand);
		$stream->putVector3($this->playerPos);
		$stream->putVector3($this->clickPos);
		
	}
	public static function new(array $actions, int $actionType, TriggerType $triggerType, Vector3 $blockPos, int $face, int $hotbarSlot, Item $itemInHand, Vector3 $playerPos, Vector3 $clickPos, int $blockRuntimeId, PredictedResult $clientInteractPrediction) : self{
		$result = new self;
		$result->actions = $actions;
		$result->actionType = $actionType;
		$result->triggerType = $triggerType;
		$result->blockPos = $blockPos;
		$result->face = $face;
		$result->hotbarSlot = $hotbarSlot;
		$result->itemInHand = $itemInHand;
		$result->playerPos = $playerPos;
		$result->clickPos = $clickPos;
		$result->blockRuntimeId = $blockRuntimeId;
		$result->clientInteractPrediction = $clientInteractPrediction;
		return $result;
	}}