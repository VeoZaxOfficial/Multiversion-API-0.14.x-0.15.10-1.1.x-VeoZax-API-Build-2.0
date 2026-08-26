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
use pocketmine\network\mcpe\NetworkSession;
class MobEffectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_EFFECT_PACKET;
	public const EVENT_ADD = 1;
	public const EVENT_MODIFY = 2;
	public const EVENT_REMOVE = 3;
	public $entityRuntimeId;
	public $eventId;
	public $effectId;
	public $amplifier = 0;
	public $particles = true;
	public $duration = 0;
	public $tick = 0;
	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->eventId = $this->getByte();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	   	    $this->effectId = $this->getVarInt();
	    	$this->amplifier = $this->getVarInt();
		}else{
		    $this->effectId = $this->getByte();
		    $this->amplifier = $this->getByte();
		}
		$this->particles = $this->getBool();
		$this->duration = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getInt();
		
	}
	protected function encodePayload(){
        $this->putEntityRuntimeId($this->entityRuntimeId);
        $this->putByte($this->eventId);
        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($this->effectId);
	    	$this->putVarInt($this->amplifier);
        }else{
            $this->putByte($this->effectId);
            $this->putByte($this->amplifier);
        }
        $this->putBool($this->particles);
        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    $this->putVarInt($this->duration);
        }else{
            $this->putInt($this->duration);
        }
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleMobEffect($this);
	}}