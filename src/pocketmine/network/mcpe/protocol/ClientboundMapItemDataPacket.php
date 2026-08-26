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
use InvalidArgumentException;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\DimensionIds;use pocketmine\network\mcpe\protocol\types\MapDecoration;use pocketmine\network\mcpe\protocol\types\MapTrackedObject;use pocketmine\utils\Color;use UnexpectedValueException;use function chr;use function count;use function ord;use function pack;use function unpack;
class ClientboundMapItemDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CLIENTBOUND_MAP_ITEM_DATA_PACKET;
	public const BITFLAG_TEXTURE_UPDATE = 0x02;
	public const BITFLAG_DECORATION_UPDATE = 0x04;
	public $mapId;
	public $type;
	public $dimensionId = DimensionIds::OVERWORLD;
	public $isLocked = false;
    public $originX;
    public $originY;
    public $originZ;
	public $eids = [];
	public $scale;
	public $trackedEntities = [];
	public $decorations = [];
	public $width;
	public $height;
	public $xOffset = 0;
	public $yOffset = 0;
	public $colors = [];
	protected function decodePayload(){
		$this->mapId = $this->getEntityUniqueId();
		$this->type = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getInt();
		
		
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	if(($this->type & 0x08) !== 0){
		    	$count = $this->getUnsignedVarInt();
		    	for($i = 0; $i < $count; ++$i){
			    	$this->eids[] = $this->getEntityUniqueId();
		    	}
			}
		}
        
            $flags = (self::BITFLAG_DECORATION_UPDATE | self::BITFLAG_TEXTURE_UPDATE);
        
		if(($this->type & ($flags)) !== 0){ 
			$this->scale = $this->getByte();
		}
		if(($this->type & self::BITFLAG_DECORATION_UPDATE) !== 0){
		    
			for($i = 0, $count = ($this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getInt()); $i < $count; ++$i){
			    
			    	$weird = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getInt();
			    	$rotation = $weird & 0x0f;
			    	$icon = $weird >> 4;
			    
				$xOffset = $this->getByte();
				$yOffset = $this->getByte();
				$label = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->getShortString();
                
                    $color = Color::fromARGB($this->getLInt()); 
                
                $this->decorations[] = new MapDecoration($icon, $rotation, $xOffset, $yOffset, $label, $color);
			}
		}
		if(($this->type & self::BITFLAG_TEXTURE_UPDATE) !== 0){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$this->width = $this->getVarInt();
		    	$this->height = $this->getVarInt();
		    	$this->xOffset = $this->getVarInt();
		    	$this->yOffset = $this->getVarInt();
		    }else{
		    	$this->width = $this->getInt();
		    	$this->height = $this->getInt();
		    	$this->xOffset = $this->getInt();
		    	$this->yOffset = $this->getInt();
		    }
            
			for($y = 0; $y < $this->height; ++$y){
				for($x = 0; $x < $this->width; ++$x){
					$this->colors[$y][$x] = Color::fromABGR($this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getLInt());
				}
			}
		}
	}
	protected function encodePayload(){
		$this->putEntityUniqueId($this->mapId);
		$type = 0;
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	if(($eidsCount = count($this->eids)) > 0){
		    	$type |= 0x08;
	    	}
		}
		if(($decorationCount = count($this->decorations)) > 0){
			$type |= self::BITFLAG_DECORATION_UPDATE;
		}
		if(count($this->colors) > 0){
			$type |= self::BITFLAG_TEXTURE_UPDATE;
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putUnsignedVarInt($type);
		}else{
		    $this->putInt($type);
		}
		
		
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	if(($type & 0x08) !== 0){ 
		    	$this->putUnsignedVarInt($eidsCount);
		    	foreach($this->eids as $eid){
			    	$this->putEntityUniqueId($eid);
		    	}
			}
		}
        
            $flags = (self::BITFLAG_TEXTURE_UPDATE | self::BITFLAG_DECORATION_UPDATE);
        
		if(($type & ($flags)) !== 0){
			($this->buffer .= chr($this->scale));
		}
		if(($type & self::BITFLAG_DECORATION_UPDATE) !== 0){
		    
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$this->putUnsignedVarInt($decorationCount);
			}else{
			    $this->putInt($decorationCount);
			}
			foreach($this->decorations as $decoration){
			    
			        if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			            $this->putVarInt(($decoration->getRotation() & 0x0f) | ($decoration->getIcon() << 4));
			        }else{
			            $this->putInt(($decoration->getRotation() & 0x0f) | ($decoration->getIcon() << 4));
			        }
			    
                $this->putByte($decoration->getXOffset());
                $this->putByte($decoration->getYOffset());
                if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			    	$this->putString($decoration->getLabel());
                }else{
                    $this->putShortString($decoration->getLabel());
                }
			   	
				    $this->putLInt($decoration->getColor()->toARGB());
				
			}
		}
		if(($type & self::BITFLAG_TEXTURE_UPDATE) !== 0){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$this->putVarInt($this->width);
		    	$this->putVarInt($this->height);
		    	$this->putVarInt($this->xOffset);
		    	$this->putVarInt($this->yOffset);
		    }else{
		    	$this->putInt($this->width);
		    	$this->putInt($this->height);
		    	$this->putInt($this->xOffset);
		    	$this->putInt($this->yOffset);
		    }
            
			for($y = 0; $y < $this->height; ++$y){
				for($x = 0; $x < $this->width; ++$x){
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				    	$this->putUnsignedVarInt($this->colors[$y][$x]->toABGR());
				    }else{
				        $this->putLInt($this->colors[$y][$x]->toABGR());
				    }
				}
			}
		}
	}
	public function cropTexture(int $minX, int $minY, int $maxX, int $maxY) : void{
		$this->height = $maxY;
		$this->width = $maxX;
		$this->xOffset = $minX;
		$this->yOffset = $minY;
		$newColors = [];
		for($y = 0; $y < $maxY; $y++){
			for($x = 0; $x < $maxX; $x++){
				$newColors[$y][$x] = $this->colors[$minY + $y][$minX + $x];
			}
		}
		$this->colors = $newColors;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleClientboundMapItemData($this);
	}}