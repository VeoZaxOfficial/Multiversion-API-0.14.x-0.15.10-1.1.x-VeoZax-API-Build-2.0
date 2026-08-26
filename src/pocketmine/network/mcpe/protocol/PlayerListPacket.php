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
use pocketmine\utils\Color;use pocketmine\entity\Skin;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\PlayerListEntry;use pocketmine\network\mcpe\protocol\types\SerializedSkin;use function count;
class PlayerListPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_LIST_PACKET;
	public const TYPE_ADD = 0;
	public const TYPE_REMOVE = 1;
	public $entries = [];
	public $type;
    public static function getPESkinId(Skin $skin) : string{
		$skinId = $skin->getSkinId();
		if(SerializedSkin::isSkinIdPE($skinId)){
			return $skinId;
		}
        $type = "Custom";
		switch ($skin->getGeometryName()){
			case "geometry.humanoid.customSlim":
			    $type = "CustomSlim";
			    break;
			case "geometry.humanoid.custom":
			default:
			    $type = "Custom";
			    break;
		}
		return "Standard_" . $type;
	}
	public function clean(){
		$this->entries = [];
		return parent::clean();
	}
	protected function decodePayload(){
		$this->type = $this->getByte();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$count = $this->getUnsignedVarInt();
		}else{
		    $count = $this->getInt();
		}
		for($i = 0; $i < $count; ++$i){
			$entry = new PlayerListEntry();
			if($this->type === self::TYPE_ADD){
				$entry->uuid = $this->getUUID();
				$entry->entityUniqueId = $this->getEntityUniqueId();
				$entry->username = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
				
			    	$skinId = $this->getString();
			    	$skinData = $this->getString();
			    	$entry->skin = new Skin(
				    	$skinId,
				    	$skinData
			    	);
				
			}else{
				$entry->uuid = $this->getUUID();
			}
			$this->entries[$i] = $entry;
		}
		
	}
	protected function encodePayload(){
        $this->putByte($this->type);
        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putUnsignedVarInt(count($this->entries));
        }else{
            $this->putInt(count($this->entries));
        }
		foreach($this->entries as $entry){
			if($this->type === self::TYPE_ADD){
				$this->putUUID($entry->uuid);
				$this->putEntityUniqueId($entry->entityUniqueId);
				if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			    	$this->putString($entry->username);
				}else{
				    $this->putShortString($entry->username);
				}
				
				    $skinId = self::getPESkinId($entry->skin);
				    $skinData = $entry->skin->getClientFriendlySkinData($this->getProtocol());
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				        $this->putString($skinId);
				        $this->putString($skinData);
				    }else{
				        $this->putShortString($skinId);
				        $this->putShortString($skinData);
				    }
				
			}else{
				$this->putUUID($entry->uuid);
			}
		}
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerList($this);
	}}