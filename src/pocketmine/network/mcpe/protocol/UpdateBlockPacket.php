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
use pocketmine\network\mcpe\multiversion\block\BlockPalette;use pocketmine\network\mcpe\NetworkSession;
class UpdateBlockPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_BLOCK_PACKET;
	public const FLAG_NONE      = 0b0000;
	public const FLAG_NEIGHBORS = 0b0001;
	public const FLAG_NETWORK   = 0b0010;
	public const FLAG_NOGRAPHIC = 0b0100;
	public const FLAG_PRIORITY  = 0b1000;
	public const FLAG_ALL = self::FLAG_NEIGHBORS | self::FLAG_NETWORK;
	public const FLAG_ALL_PRIORITY = self::FLAG_ALL | self::FLAG_PRIORITY;
	public const DATA_LAYER_NORMAL = 0;
	public const DATA_LAYER_LIQUID = 1;
	public $x;
	public $z;
	public $y;
	public $blockId;
	public $blockMeta;
	public $flags;
	public $dataLayerId = self::DATA_LAYER_NORMAL;
	protected function decodePayload(){
	    if($this->getProtocol() < ProtocolInfo::PROTOCOL_90){
	        $this->x = $this->getInt();
	        $this->z = $this->getInt();
	        $this->y = $this->getByte();
	    }else{
		    $this->getBlockPosition($this->x, $this->y, $this->z);
	    }
		
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	        	$this->blockId = $this->getUnsignedVarInt();
	        	$aux = $this->getUnsignedVarInt();
	        	$this->blockMeta = $aux & 0x0f;
	        	$this->flags = $aux >> 4;
		    }else{
		        $this->blockId = $this->getByte();
		        $aux = $this->getByte();
	        	$this->blockMeta = $aux & 0x0f;
	        	$this->flags = $aux >> 4;
		    }
		
	}
	protected function encodePayload(){
	    if($this->getProtocol() < ProtocolInfo::PROTOCOL_90){
	        $this->putInt($this->x);
	        $this->putInt($this->z);
	        $this->putByte($this->y);
	    }else{
	    	$this->putBlockPosition($this->x, $this->y, $this->z);
	    }
		
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	        	$this->putUnsignedVarInt($this->blockId);
	        	$this->putUnsignedVarInt(($this->flags << 4) | $this->blockMeta);
		    }else{
		        $this->putByte($this->blockId);
		        $this->putByte(($this->flags << 4) | $this->blockMeta);
		    }
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateBlock($this);
	}}