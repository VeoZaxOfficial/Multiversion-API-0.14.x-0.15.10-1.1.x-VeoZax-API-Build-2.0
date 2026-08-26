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
use pocketmine\network\mcpe\multiversion\MultiversionEnums;use pocketmine\network\mcpe\NetworkSession;use UnexpectedValueException;use function count;
class TextPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TEXT_PACKET;
	public const TYPE_RAW = 0;
	public const TYPE_CHAT = 1;
	public const TYPE_TRANSLATION = 2;
	public const TYPE_POPUP = 3;
	public const TYPE_JUKEBOX_POPUP = 4;
	public const TYPE_TIP = 5;
	public const TYPE_SYSTEM = 6;
	public const TYPE_WHISPER = 7;
	public const TYPE_ANNOUNCEMENT = 8;
	public const TYPE_JSON_WHISPER = 9;
	public const TYPE_JSON = 10;
	public const TYPE_JSON_ANNOUNCEMENT = 11;
	public const PARAMETERS_LIMIT = 5;
	public $type;
	public $needsTranslation = false;
	public $sourceName;
	public $sourceThirdPartyName = "";
	public $sourcePlatform = 0;
	public $message;
	public $parameters = [];
	public $xboxUserId = "";
	public $platformChatId = "";
	public $filteredMessage = "";
	protected function decodePayload(){
		$this->type = MultiversionEnums::getMessageType($this->getProtocol(), $this->getByte());
		
		switch($this->type){
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			case self::TYPE_ANNOUNCEMENT:
				$this->sourceName = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
				
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
			case self::TYPE_JSON_WHISPER:
			case self::TYPE_JSON:
			case self::TYPE_JSON_ANNOUNCEMENT:
				$this->message = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
				break;
			case self::TYPE_POPUP:
			    
			        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			            $this->sourceName = $this->getString();
			            $this->message = $this->getString();
			        }else{
			            $this->sourceName = $this->getShortString();
			            $this->message = $this->getShortString();
			        }
			        break;
			    
			case self::TYPE_TRANSLATION:
			case self::TYPE_JUKEBOX_POPUP:
				$this->message = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
				$count = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getByte();
				if($count > self::PARAMETERS_LIMIT){
					throw new UnexpectedValueException("Too many translation parameters count: $count");
				}
				for($i = 0; $i < $count; ++$i){
					$this->parameters[] = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
				}
				break;
		}
        
	}
	protected function encodePayload(){
        $this->putByte(MultiversionEnums::getMessageTypeId($this->getProtocol(), $this->type));
		
		switch($this->type){
			case self::TYPE_CHAT:
			case self::TYPE_WHISPER:
			case self::TYPE_ANNOUNCEMENT:
			    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			    	$this->putString($this->sourceName);
			    }else{
			        $this->putShortString($this->sourceName);
			    }
				
			case self::TYPE_RAW:
			case self::TYPE_TIP:
			case self::TYPE_SYSTEM:
			case self::TYPE_JSON_WHISPER:
			case self::TYPE_JSON:
			case self::TYPE_JSON_ANNOUNCEMENT:
			    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			    	$this->putString($this->message);
			    }else{
			        $this->putShortString($this->message);
			    }
				break;
			case self::TYPE_POPUP:
			    
			        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			            $this->putString($this->sourceName);
			            $this->putString($this->message);
			        }else{
			            $this->putShortString($this->sourceName);
			            $this->putShortString($this->message);
			        }
			        break;
			    
			case self::TYPE_TRANSLATION:
			case self::TYPE_JUKEBOX_POPUP:
				if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				    $this->putString($this->message);
			    	$this->putUnsignedVarInt(count($this->parameters));
				}else{
				    $this->putShortString($this->message);
				    $this->putByte(count($this->parameters));
				}
				foreach($this->parameters as $p){
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				    	$this->putString($p);
				    }else{
				        $this->putShortString($p);
				    }
				}
				break;
		}
        
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleText($this);
	}}