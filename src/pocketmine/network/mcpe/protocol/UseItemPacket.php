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
use pocketmine\item\Item;use pocketmine\math\Vector3;use pocketmine\network\mcpe\NetworkSession;
class UseItemPacket extends DataPacket{
	const NETWORK_ID = ProtocolInfo::USE_ITEM_PACKET;
	public $x;
	public $y;
	public $z;
	public $blockId;
	public $face;
	public $playerPos;
	public $clickPos;
	public $slot;
	public $item;
	public function decodePayload(){
		$this->getBlockPosition($this->x, $this->y, $this->z);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_92){
		    $this->blockId = $this->getUnsignedVarInt();
		}
		$this->face = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getByte();
		$this->clickPos = $this->getVector3();
		$this->playerPos = $this->getVector3();
		$this->slot = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getInt();
		$this->item = $this->getSlot();
	}
	public function encodePayload(){
	    $this->putBlockPosition($this->x, $this->y, $this->z);
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_92){
	    	$this->putUnsignedVarInt($this->blockId);
	    }
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($this->face);
	    }else{
	        $this->putByte($this->face);
	    }
		$this->putVector3($this->clickPos);
		$this->putVector3($this->playerPos);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($this->slot);
		}else{
		    $this->putInt($this->slot);
		}
		$this->putSlot($this->item);
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleUseItem($this);
	}}