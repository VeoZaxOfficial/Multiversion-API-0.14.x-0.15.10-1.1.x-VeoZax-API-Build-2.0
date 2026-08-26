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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\PlayerPermissions;use pocketmine\utils\Binary;use function pack;
class AdventureSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADVENTURE_SETTINGS_PACKET;
	public const PERMISSION_NORMAL = 0;
	public const PERMISSION_OPERATOR = 1;
	public const PERMISSION_HOST = 2;
	public const PERMISSION_AUTOMATION = 3;
	public const PERMISSION_ADMIN = 4;
	public const BITFLAG_SECOND_SET = 1 << 16;
	public const PRE_V_0_15_0_AUTO_JUMP = 0x40;
	public const PRE_V_0_15_0_ALLOW_FLIGHT = 0x80;
	public const PRE_V_0_15_0_NO_CLIP = 0x100;
	public const WORLD_IMMUTABLE = 0x01;
	public const NO_PVP = 0x02;
	public const AUTO_JUMP = 0x20;
	public const ALLOW_FLIGHT = 0x40;
	public const NO_CLIP = 0x80;
	public const WORLD_BUILDER = 0x100;
	public const FLYING = 0x200;
	public const MUTED = 0x400;
	public const MINE = 0x01 | self::BITFLAG_SECOND_SET;
	public const DOORS_AND_SWITCHES = 0x02 | self::BITFLAG_SECOND_SET;
	public const OPEN_CONTAINERS = 0x04 | self::BITFLAG_SECOND_SET;
	public const ATTACK_PLAYERS = 0x08 | self::BITFLAG_SECOND_SET;
	public const ATTACK_MOBS = 0x10 | self::BITFLAG_SECOND_SET;
	public const OPERATOR = 0x20 | self::BITFLAG_SECOND_SET;
	public const TELEPORT = 0x80 | self::BITFLAG_SECOND_SET;
	public const BUILD = 0x100 | self::BITFLAG_SECOND_SET;
	public const DEFAULT = 0x200 | self::BITFLAG_SECOND_SET;
	public $flags = 0;
	public $commandPermission = self::PERMISSION_NORMAL;
	public $flags2 = -1;
	public $playerPermission = PlayerPermissions::MEMBER;
	public $customFlags = 0; 
	public $entityUniqueId; 
	protected function decodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	        $this->flags = $this->getUnsignedVarInt();
	        $this->commandPermission = $this->getUnsignedVarInt();
	    }else{
	        $this->flags = $this->getInt();
	        $this->commandPermission = $this->getInt();
	        $this->playerPermission = $this->getInt();
	    }
	    
	}
	protected function encodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	        $this->putUnsignedVarInt($this->flags);
	    	$this->putUnsignedVarInt($this->commandPermission);
	    }else{
	        $this->putInt($this->flags);
	    	$this->putInt($this->commandPermission);
	    	$this->putInt($this->playerPermission);
	    }
	    
	}
	public function getFlag(int $flag) : bool{
		if($flag & self::BITFLAG_SECOND_SET){
			return ($this->flags2 & $flag) !== 0;
		}
		return ($this->flags & $flag) !== 0;
	}
	public function setFlag(int $flag, bool $value){
		if($flag & self::BITFLAG_SECOND_SET){
			$flagSet =& $this->flags2;
		}else{
			$flagSet =& $this->flags;
		}
		if($value){
			$flagSet |= $flag;
		}else{
			$flagSet &= ~$flag;
		}
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleAdventureSettings($this);
	}}