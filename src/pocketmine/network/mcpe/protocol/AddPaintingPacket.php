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
use pocketmine\math\Vector3;use pocketmine\network\mcpe\NetworkSession;
class AddPaintingPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_PAINTING_PACKET;
	public $entityUniqueId = null;
	public $entityRuntimeId;
	public $x;
	public $y;
	public $z;
	public $direction;
	public $title;
	protected function decodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->entityUniqueId = $this->getEntityUniqueId();
	    }
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		
		    $this->getBlockPosition($this->x, $this->y, $this->z);
		
		$this->direction = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getInt();
		$this->title = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
	}
	protected function encodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
	    }
		$this->putEntityRuntimeId($this->entityRuntimeId);
		
		    $this->putBlockPosition($this->x, $this->y, $this->z);
		
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    $this->putVarInt($this->direction);
	    	$this->putString($this->title);
		}else{
		    $this->putInt($this->direction);
		    $this->putShortString($this->title);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPainting($this);
	}}