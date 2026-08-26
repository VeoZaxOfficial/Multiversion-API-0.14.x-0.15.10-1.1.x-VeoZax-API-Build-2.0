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
use pocketmine\item\Item;use pocketmine\math\Vector3;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\AbilitiesData;use pocketmine\network\mcpe\protocol\types\DeviceOS;use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;use pocketmine\network\mcpe\protocol\types\EntityLink;use pocketmine\network\mcpe\protocol\types\GameMode;use pocketmine\utils\UUID;use function count;
class AddPlayerPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_PLAYER_PACKET;
	public $uuid;
	public $username;
	public $thirdPartyName = "";
	public $platform = 0;
	public $entityUniqueId = null; 
	public $entityRuntimeId;
	public $platformChatId = "";
	public $position;
	public $motion;
	public $pitch = 0.0;
	public $yaw = 0.0;
	public $headYaw = null; 
	public $item;
	public $gameMode = GameMode::SURVIVAL;
	public $metadata = [];
	public $syncedProperties;
    public $abilitiesData;
	public $uvarint1 = 0;
	public $uvarint2 = 0;
	public $uvarint3 = 0;
	public $uvarint4 = 0;
	public $uvarint5 = 0;
	public $long1 = 0;
	public $links = [];
	public $deviceId = ""; 
	public $buildPlatform = DeviceOS::UNKNOWN;
	protected function decodePayload(){
		$this->uuid = $this->getUUID();
		$this->username = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
		
		if( $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ){
	    	$this->entityUniqueId = $this->getEntityUniqueId();
		}
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->pitch = $this->getLFloat();
	    	$this->yaw = $this->getLFloat();
	    	$this->headYaw = $this->getLFloat();
		}else{
	    	$this->yaw = $this->getFloat();
	    	$this->headYaw = $this->getFloat();
	    	$this->pitch = $this->getFloat();
		}
		$this->item = $this->getSlot();
		
		$this->metadata = $this->getEntityMetadata();
        
	}
	protected function encodePayload(){
		$this->putUUID($this->uuid);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putString($this->username);
		}else{
		    $this->putShortString($this->username);
		}
		
		if( $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ){
	    	$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		}
		$this->putEntityRuntimeId($this->entityRuntimeId);
		
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
            $this->putLFloat($this->pitch);
            $this->putLFloat($this->yaw);
            $this->putLFloat($this->headYaw ?? $this->yaw);
		}else{
            $this->putFloat($this->yaw);
            $this->putFloat($this->headYaw ?? $this->yaw);
            $this->putFloat($this->pitch);
		}
		$this->putSlot($this->item);
		
		$this->putEntityMetadata($this->metadata);
        
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPlayer($this);
	}}