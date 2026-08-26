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
class MovePlayerPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVE_PLAYER_PACKET;
	public const MODE_NORMAL = 0;
	public const MODE_RESET = 1;
	public const MODE_TELEPORT = 2;
	public const MODE_PITCH = 3; 
	public $entityRuntimeId;
	public $position;
	public $pitch;
	public $yaw;
	public $headYaw;
	public $mode = self::MODE_NORMAL;
	public $onGround = false; 
	public $ridingEid = 0;
	public $teleportCause = 0;
	public $teleportItem = 0;
	public $tick = 0;
	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->position = $this->getVector3();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->pitch = $this->getLFloat();
	    	$this->yaw = $this->getLFloat();
	    	$this->headYaw = $this->getLFloat();
		}else{
	    	$this->yaw = $this->getFloat();
	    	$this->headYaw = $this->getFloat();
	    	$this->pitch = $this->getFloat();
		}
		$this->mode = $this->getByte();
		$this->onGround = $this->getBool();
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	    	$this->ridingEid = $this->getEntityRuntimeId();
		    if($this->mode === MovePlayerPacket::MODE_TELEPORT){
		    	$this->teleportCause = $this->getLInt();
		    	$this->teleportItem = $this->getLInt();
	    	}
		    
		}
	}
	protected function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3($this->position);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
            $this->putLFloat($this->pitch);
            $this->putLFloat($this->yaw);
            $this->putLFloat($this->headYaw);
		}else{
            $this->putFloat($this->yaw);
            $this->putFloat($this->headYaw);
            $this->putFloat($this->pitch);
		}
        $this->putByte($this->mode);
        $this->putBool($this->onGround);
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	    	$this->putEntityRuntimeId($this->ridingEid);
	    	if($this->mode === MovePlayerPacket::MODE_TELEPORT){
                $this->putLInt($this->teleportCause);
                $this->putLInt($this->teleportItem);
	    	}
		    
		}
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleMovePlayer($this);
	}}