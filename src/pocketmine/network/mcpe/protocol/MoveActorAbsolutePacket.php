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
use pocketmine\math\Vector3;use pocketmine\network\mcpe\NetworkSession;use function chr;use function ord;use function pack;use function unpack;
class MoveActorAbsolutePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_ACTOR_ABSOLUTE_PACKET;
	public const FLAG_GROUND = 0x01;
	public const FLAG_TELEPORT = 0x02;
	public $entityRuntimeId;
	public $flags = 0;
	public $position;
	public $pitch;
	public $yaw;
	public $headYaw; 
	protected function decodePayload(){
		
	    	$this->entityRuntimeId = $this->getEntityRuntimeId();
	    	$this->position = $this->getVector3();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	        	$this->pitch = $this->getByteRotation();
	        	$this->headYaw = $this->getByteRotation();
	        	$this->yaw = $this->getByteRotation();
	    	}else{
	    	    $this->pitch = $this->getByteRotation();
	        	$this->yaw = $this->getByteRotation();
	        	$this->headYaw = $this->getByteRotation();
	    	}
	    	$this->flags = 0;
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
                $onGround = $this->getBool();
                $isTeleported = $this->getBool();
                if($onGround){
                    $this->flags |= self::FLAG_GROUND;
                }
                if($isTeleported){
                    $this->flags |= self::FLAG_TELEPORT;
                }
            }
		
	}
	protected function encodePayload(){
		
	    	$this->putEntityRuntimeId($this->entityRuntimeId);
	    	$this->putVector3($this->position);
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	        	$this->putByteRotation($this->pitch);
	        	$this->putByteRotation($this->headYaw);
	        	$this->putByteRotation($this->yaw);
	    	}else{
	    	    $this->putByteRotation($this->pitch);
	        	$this->putByteRotation($this->yaw);
	        	$this->putByteRotation($this->headYaw);
	    	}
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
				$this->putBool(($this->flags & self::FLAG_GROUND) !== 0);
				$this->putBool(($this->flags & self::FLAG_TELEPORT) !== 0);
            }
		
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleMoveActorAbsolute($this);
	}}