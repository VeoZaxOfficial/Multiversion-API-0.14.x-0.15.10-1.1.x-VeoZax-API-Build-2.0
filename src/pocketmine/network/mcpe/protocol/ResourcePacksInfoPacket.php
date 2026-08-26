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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\ResourcesAPI\ResourcePack;use pocketmine\utils\UUID;use function count;
class ResourcePacksInfoPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACKS_INFO_PACKET;
	public $mustAccept = false; 
	public $hasAddons = false;
	public $hasScripts = false; 
	public $worldTemplateId;
	public $worldTemplateVersion;
	public $forceServerPacks = false;
	public $behaviorPackEntries = [];
	public $resourcePackEntries = [];
	public $cdnUrls = [];
	protected function decodePayload(){
		$this->mustAccept = $this->getBool();
		
		
	    	$behaviorPackCount = $this->getLShort();
	    	while($behaviorPackCount-- > 0){
		    	$this->getString();
		    	$this->getString();
		    	$this->getLLong();
		    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		        	$this->getString();
		        	
		    	}
			}
		
		$resourcePackCount = $this->getLShort();
		while($resourcePackCount-- > 0){
			
				$this->getString();
			
			$this->getString();
            $this->getLLong();
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		    	$this->getString();
		    	
			}
		}
		
	}
	protected function encodePayload(){
        $this->putBool($this->mustAccept);
		
		
            $this->putLShort(count($this->behaviorPackEntries));
		    foreach($this->behaviorPackEntries as $entry){
		    	$this->putString($entry->getPackId());
		    	$this->putString($entry->getPackVersion());
                $this->putLLong($entry->getPackSize());
                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		        	$this->putString(""); 
			        
		    	}
			}
		
        $this->putLShort(count($this->resourcePackEntries));
		foreach($this->resourcePackEntries as $entry){
			
			    $this->putString($entry->getPackId());
			
			$this->putString($entry->getPackVersion());
            $this->putLLong($entry->getPackSize());
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		    	$this->putString($entry->getEncryptionKey() ?? ""); 
		    	
			}
		}
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePacksInfo($this);
	}}