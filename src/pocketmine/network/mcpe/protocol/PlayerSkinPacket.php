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
use pocketmine\entity\Skin;use pocketmine\network\mcpe\NetworkSession;use pocketmine\utils\UUID;
class PlayerSkinPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_SKIN_PACKET;
	public $uuid;
	public $oldSkinName = "";
	public $newSkinName = "";
	public $skin;
	public $premiumSkin = false;
	protected function decodePayload(){
		$this->uuid = $this->getUUID();
        
	    	$skinId = $this->getString();
	    	$this->newSkinName = $this->getString();
	    	$this->oldSkinName = $this->getString();
	    	
	    	$skinData = $this->getString();
	    	
	    	$capeData = "";
	    	
	    	$geometryModel = $this->getString();
	    	$geometryData = $this->getString();
	    	$this->skin = new Skin($skinId, $skinData, $capeData, $geometryModel, $geometryData);
            
        
	}
	protected function encodePayload(){
		$this->putUUID($this->uuid);
        
    		$this->putString($this->skin->getSkinId());
	    	$this->putString($this->newSkinName);
	    	$this->putString($this->oldSkinName);
			$skinData = $this->skin->getClientFriendlySkinData($this->getProtocol());
	    	
	    	$this->putString($skinData);
	    	
	    	$this->putString($this->skin->getGeometryName());
	    	$this->putString($this->skin->getGeometryData());
            
        
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerSkin($this);
	}}