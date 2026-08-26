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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\Experiments;use pocketmine\ResourcesAPI\ResourcePack;use function count;
class ResourcePackStackPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::RESOURCE_PACK_STACK_PACKET;
	public $mustAccept = false;
	public $behaviorPackStack = [];
	public $resourcePackStack = [];
	public $isExperimental = false;
	public $baseGameVersion = ProtocolInfo::MINECRAFT_VERSION_NETWORK;
	public $experiments;
	public $useVanillaEditorPacks;
	protected function decodePayload(){
		$this->mustAccept = $this->getBool();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	    	$behaviorPackCount = $this->getUnsignedVarInt();
		}else{
			$behaviorPackCount = $this->getLShort();
		}
		while($behaviorPackCount-- > 0){
			$this->getString();
			$this->getString();
			
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
		    $resourcePackCount = $this->getUnsignedVarInt();
		}else{
			$resourcePackCount = $this->getLShort();
		}
		while($resourcePackCount-- > 0){
			$this->getString();
			$this->getString();
			
		}
		
	}
	protected function encodePayload(){
        $this->putBool($this->mustAccept);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
			$this->putUnsignedVarInt(count($this->behaviorPackStack));
		}else{
	    	$this->putLShort(count($this->behaviorPackStack));
		}
		foreach($this->behaviorPackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
			$this->putUnsignedVarInt(count($this->resourcePackStack));
		}else{
		    $this->putLShort(count($this->resourcePackStack));
		}
		foreach($this->resourcePackStack as $entry){
			$this->putString($entry->getPackId());
			$this->putString($entry->getPackVersion());
			
		}
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleResourcePackStack($this);
	}}