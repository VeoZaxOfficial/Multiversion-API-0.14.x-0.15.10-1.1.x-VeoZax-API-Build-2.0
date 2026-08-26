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
namespace pocketmine\network\mcpe;
use pocketmine\block\BlockFactory;use pocketmine\block\BlockIds;use pocketmine\entity\Attribute;use pocketmine\entity\Entity;use pocketmine\entity\Skin;use pocketmine\item\Durable;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\item\ItemIds;use pocketmine\math\Vector3;use pocketmine\math\Vector2;use pocketmine\nbt\LittleEndianNBTStream;use pocketmine\nbt\NetworkLittleEndianNBTStream;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\LongTag;use pocketmine\nbt\tag\NamedTag;use pocketmine\nbt\tag\StringTag;use pocketmine\network\mcpe\multiversion\block\BlockPalette;use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;use pocketmine\network\mcpe\multiversion\MetadataConvertor;use pocketmine\network\mcpe\multiversion\MultiversionEnums;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\types\CommandOriginData;use pocketmine\network\mcpe\protocol\types\entity\AttributeModifier;use pocketmine\network\mcpe\protocol\types\EntityLink;use pocketmine\network\mcpe\protocol\types\GameRuleType;use pocketmine\network\mcpe\protocol\types\PersonaPieceTintColor;use pocketmine\network\mcpe\protocol\types\PersonaSkinPiece;use pocketmine\network\mcpe\protocol\types\SerializedSkin;use pocketmine\network\mcpe\protocol\types\SkinAnimation;use pocketmine\network\mcpe\protocol\types\SkinImage;use pocketmine\network\mcpe\protocol\types\StructureEditorData;use pocketmine\network\mcpe\protocol\types\StructureSettings;use pocketmine\utils\BinaryStream;use pocketmine\utils\Color;use pocketmine\utils\UUID;use SplFixedArray;use UnexpectedValueException;use function chr;use function count;use function ord;use function strlen;use Closure;
class NetworkBinaryStream extends BinaryStream{
	private const DAMAGE_TAG = "Damage"; 
	private const DAMAGE_TAG_CONFLICT_RESOLUTION = "___Damage_ProtocolCollisionResolution___";
	private const PM_ID_TAG = "___Id___";
	private const PM_META_TAG = "___Meta___";
	protected $protocol;
	public function setProtocol(int $protocol) : void{
	    $this->protocol = $protocol;
	}
	public function getProtocol() : int{
	    return $this->protocol;
	}
	public function getString() : string{
		return $this->get($this->getUnsignedVarInt());
	}
	public function putString(string $v) : void{
	    $this->putUnsignedVarInt(strlen($v));
		($this->buffer .= $v);
	}
	public function getShortString() : string{
		return $this->get($this->getShort());
	}
	public function putShortString(string $v) : void{
	    $this->putShort(strlen($v));
		($this->buffer .= $v);
	}
	public function getUUID() : UUID{
		$part1 = $this->getLInt();
		$part0 = $this->getLInt();
		$part3 = $this->getLInt();
		$part2 = $this->getLInt();
		return new UUID($part0, $part1, $part2, $part3);
	}
	public function putUUID(UUID $uuid) : void{
        $this->putLInt($uuid->getPart(1));
        $this->putLInt($uuid->getPart(0));
        $this->putLInt($uuid->getPart(3));
        $this->putLInt($uuid->getPart(2));
	}
	public function getSkin() : Skin{
		$skinId = $this->getString();
		
		$skinResourcePatch = $this->getString();
		$skinData = $this->getSkinImage();
		$animationCount = $this->getLInt();
		if($animationCount > 128){
			throw new UnexpectedValueException("Too many skin animations: $animationCount");
		}
		$animations = [];
		for($i = 0; $i < $animationCount; ++$i){
			$animations[] = new SkinAnimation(
				$skinImage = $this->getSkinImage(),
				$animationType = $this->getLInt(),
				$animationFrames = $this->getLFloat(),
				$expressionType = ( (0) )
			);
		}
		$capeData = $this->getSkinImage();
		$geometryData = $this->getString();
        
		$animationData = $this->getString();
		
	    	$premium = $this->getBool();
	    	$persona = $this->getBool();
	    	$capeOnClassic = $this->getBool();
		
		$capeId = $this->getString();
		$fullSkinId = $this->getString();
		
            $armSize = "wide";
            $skinColor = new Color(0, 0, 0);
            $personaPieces = SplFixedArray::fromArray([]);
            $pieceTintColors = SplFixedArray::fromArray([]);
		
        return (new SerializedSkin($skinId, $skinPlayFabId ?? "", $skinData, $capeId, $capeData, $skinResourcePatch, $geometryData, $geometryDataVersion ?? "", $animationData, $animations, $premium, $persona, $capeOnClassic, $fullSkinId, $armSize, $skinColor, $personaPieces, $pieceTintColors, $isPrimaryUser ?? true, $override ?? true))->toSkin();
	}
	public function putSkin(Skin $skin){
	    $skin = $skin->getSerializedSkin();
		$this->putString($skin->getSkinId());
		
		$this->putString($skin->getResourcePatch());
		$this->putSkinImage($skin->getSkinImage());
        $this->putLInt(count($skin->getAnimationFrames()));
		foreach($skin->getAnimationFrames() as $animation){
			$this->putSkinImage($animation->getImage());
            $this->putLInt($animation->getType());
            $this->putLFloat($animation->getFrames());
			
		}
		$this->putSkinImage($skin->getCapeImage());
		$this->putString($skin->getGeometryData());
        
		$this->putString($skin->getAnimationData());
		
            $this->putBool($skin->isPremiumSkin());
            $this->putBool($skin->isPersonaSkin());
            $this->putBool($skin->isCapeOnClassicSkin());
		
		$this->putString($skin->getCapeId());
		$this->putString($skin->getFullSkinId());
		
	}
	private function getSkinImage() : SkinImage{
		$width = $this->getLInt();
		$height = $this->getLInt();
		$data = $this->getString();
		return new SkinImage($height, $width, $data);
	}
	private function putSkinImage(SkinImage $image) : void{
        $this->putLInt($image->getWidth());
        $this->putLInt($image->getHeight());
		$this->putString($image->getData());
	}
	public function getSlot(bool $withStackId = true) : Item{
	    
		$id = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getShort();
		if($id === 0){
			return ItemFactory::get(0, 0, 0);
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$auxValue = $this->getVarInt();
	    	$data = $auxValue >> 8;
	    	$cnt = $auxValue & 0xff;
	        if( $this->getProtocol() >= ProtocolInfo::PROTOCOL_110 && $data === 0x7fff ){
		        $data = -1;
	        }
		}else{
		    $cnt = $this->getByte();
		    $data = $this->getShort();
		    if($data >= 32768){
		        $data -= 65536;
		    }
		}
        
		$nbtLen = $this->getLShort();
		$nbt = null;
		if($nbtLen > 0){
		    $decodedNBT = (new LittleEndianNBTStream())->read($this->get($nbtLen));
		    if(!($decodedNBT instanceof CompoundTag)){
			    throw new UnexpectedValueException("Unexpected root tag type for itemstack");
		    }
            if( $id === ItemIds::FILLED_MAP && $decodedNBT->hasTag("map_uuid", StringTag::class) ){
                $decodedNBT->setLong("map_uuid", (int) $decodedNBT->getString("map_uuid"), true);
            }
		    $nbt = $decodedNBT;
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	    	$canPlaceOn = $this->getVarInt();
	     	if($canPlaceOn > 128){
	    		throw new UnexpectedValueException("Too many canPlaceOn: $canPlaceOn");
	    	}
	    	for($i = 0; $i < $canPlaceOn; ++$i){
		    	$this->getString();
	    	}
		    $canDestroy = $this->getVarInt();
	    	if($canDestroy > 128){
		    	throw new UnexpectedValueException("Too many canDestroy: $canDestroy");
	    	}
		    for($i = 0; $i < $canDestroy; ++$i){
		    	$this->getString();
	    	}
	    	if($id === ItemIds::SHIELD){
		    	$this->getVarLong(); 
	    	}
		}
		if($nbt !== null){
			if($nbt->hasTag(self::PM_ID_TAG, IntTag::class)){
				$id = $nbt->getInt(self::PM_ID_TAG);
				$nbt->removeTag(self::PM_ID_TAG);
				if($nbt->count() === 0){
					$nbt = null;
				}
			}
			if($nbt->hasTag(self::DAMAGE_TAG, IntTag::class)){
				$data = $nbt->getInt(self::DAMAGE_TAG);
				$nbt->removeTag(self::DAMAGE_TAG);
				if($nbt->count() === 0){
					$nbt = null;
					goto end;
				}
			}
			if(($conflicted = $nbt->getTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION)) !== null){
				$nbt->removeTag(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
				$conflicted->setName(self::DAMAGE_TAG);
				$nbt->setTag($conflicted);
			}
		    if(($metaTag = $nbt->getTag(self::PM_META_TAG)) instanceof IntTag){
		    	$data = $metaTag->getValue();
		    	$nbt->removeTag(self::PM_META_TAG);
		    	if($nbt->count() === 0){
			     	$nbt = null;
		    	}
	    	}
		}
		end:
		return ItemFactory::get($id, $data, $cnt, $nbt);
	}
	public function putSlot(Item $item, bool $withStackId = true) : void{
	    
		if($item->getId() === 0){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$this->putVarInt(0);
		    }else{
		        $this->putShort(0);
		    }
			return;
		}
        $id = $item->getId();
        $damage = $item->getDamage();
		$nbt = null;
		if($item->hasCompoundTag()){
			$nbt = clone $item->getNamedTag();
		}
		$protocolItem = $item->getItemProtocol($this->getProtocol());
		if($protocolItem !== null){
			if($nbt === null){
		    	$nbt = new CompoundTag();
	    	}
	    	$nbt->setInt(self::PM_ID_TAG, $item->getId());
	    	$nbt->setInt(self::PM_META_TAG, $item->getDamage());
            [$id, $damage] = [$protocolItem->getId(), $protocolItem->getDamage()];
		}
        
			if($item instanceof Durable and $item->getDamage() > 0){
				if($nbt !== null){
					if(($existing = $nbt->getTag(self::DAMAGE_TAG)) !== null){
						$nbt->removeTag(self::DAMAGE_TAG);
						$existing->setName(self::DAMAGE_TAG_CONFLICT_RESOLUTION);
						$nbt->setTag($existing);
					}
				}else{
					$nbt = new CompoundTag();
				}
				$nbt->setInt(self::DAMAGE_TAG, $item->getDamage());
			}
		
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($id);
	    	if($this->getProtocol() < ProtocolInfo::PROTOCOL_110){
		    	$auxValue = ($damage << 8) | $item->getCount();
	    	}else{
	    		$auxValue = (($damage & 0x7fff) << 8) | $item->getCount();
	    	}
	    	$this->putVarInt($auxValue);
		}else{
		    $this->putShort($id);
		    $this->putByte($item->getCount());
		    $this->putShort($damage);
		}
		if($nbt !== null){
		    
                if( $item->getId() === ItemIds::FILLED_MAP && $item->getNamedTag()->hasTag("map_uuid", LongTag::class) ){
                    $tag = $item->getNamedTag();
                    $uuid = $tag->getLong("map_uuid");
                    $nbt->setString("map_uuid", (string) $uuid, true);
                }
                $nbt = (new LittleEndianNBTStream())->write($nbt);
                $this->putLShort(strlen($nbt));
                $this->put($nbt);
		    
		}else{
            $this->putLShort(0);
		}
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_110){
	    	$this->putVarInt(0); 
	    	$this->putVarInt(0); 
	    	if($id === ItemIds::SHIELD){
		    	$this->putVarLong(0); 
			}
		}
	}
	public function getRecipeIngredient() : Item{
		$id = $this->getVarInt();
		if($id === 0){
			return ItemFactory::get(ItemIds::AIR, 0, 0);
		}
		$meta = $this->getVarInt();
		if($meta === 0x7fff){
		    $meta = -1;
		}
		$count = $this->getVarInt();
		return ItemFactory::get($id, $meta, $count);
	}
	public function putRecipeIngredient(Item $item) : void{
	    
	    	if($item->isNull()){
		    	$this->putVarInt(0);
	    	}else{
		        $id = $item->getId();
		        $damage = $item->getDamage();
		        
		            $damage = $damage & 0x7fff;
		        
		    	$this->putVarInt($id);
		    	$this->putVarInt($damage);
		    	$this->putVarInt($item->getCount());
	    	}
		
	}
	public function getEntityMetadata(bool $types = true) : array{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$count = $this->getUnsignedVarInt();
	    	if($count > 128){
		    	throw new UnexpectedValueException("Too many actor metadata: $count");
	    	}
	    	$data = [];
	    	for($i = 0; $i < $count; ++$i){
		    	$key = $this->getUnsignedVarInt();
		    	$type = $this->getUnsignedVarInt();
			    $value = $this->getMetadataValue($type);
		    	if($types){
			    	$data[$key] = [$type, $value];
		    	}else{
			    	$data[$key] = $value;
		    	}
	    	}
		}else{
		    $data = [];
		    $count = 0;
	    	$b = $this->getByte();
		    while($b !== 127 and !$this->feof()){
		        if($count++ > 128){
		            throw new UnexpectedValueException("Too many actor metadata: $count");
		        }
		    	$key = $b & 0x1F;
		    	$type = $b >> 5;
		    	$value = $this->getMetadataValue($type);
		    	if($types === true){
			    	$data[$key] = [$type, $value];
		    	}else{
			    	$data[$key] = $value;
		    	}
		    	$b = $this->getByte();
		    }
		}
		return MetadataConvertor::rollbackMeta($data, $this->getProtocol());
	}
    public function getMetadataValue(int $type) : mixed{
		switch($type){
			case Entity::DATA_TYPE_BYTE:
				$value = (ord($this->get(1)));
				break;
			case Entity::DATA_TYPE_SHORT:
				$value = $this->getSignedLShort();
				break;
			case Entity::DATA_TYPE_INT:
				$value = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getLInt();
				break;
			case Entity::DATA_TYPE_FLOAT:
				$value = $this->getLFloat();
				break;
			case Entity::DATA_TYPE_STRING:
				$value = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getString() : $this->get($this->getLShort());
				break;
			case Entity::DATA_TYPE_SLOT:
				
				    $value = $this->getSlot();
				
				break;
			case Entity::DATA_TYPE_POS:
				$value = new Vector3();
				$this->getSignedBlockPosition($value->x, $value->y, $value->z);
				break;
			case Entity::DATA_TYPE_LONG:
				$value = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarLong() : $this->getLLong();
				break;
			case Entity::DATA_TYPE_VECTOR3F:
				$value = $this->getVector3();
				break;
			default:
				throw new UnexpectedValueException("Invalid data type " . $type);
		}
		return $value;
    }
	public function putEntityMetadata(array $metadata) : void{
	    $metadata = MetadataConvertor::updateMeta($metadata, $this->getProtocol());
		if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putUnsignedVarInt(count($metadata));
		}
		foreach($metadata as $key => $d){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$this->putUnsignedVarInt($key); 
		    	$this->putUnsignedVarInt($d[0]); 
		    }else{
		        $this->putByte(($d[0] << 5) | ($key & 0x1F));
		    }
			switch($d[0]){
				case Entity::DATA_TYPE_BYTE:
                    $this->putByte($d[1]);
					break;
				case Entity::DATA_TYPE_SHORT:
                    $this->putLShort($d[1]);
					break;
				case Entity::DATA_TYPE_INT:
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				    	$this->putVarInt($d[1]);
				    }else{
				        $this->putLInt($d[1]);
				    }
					break;
				case Entity::DATA_TYPE_FLOAT:
                    $this->putLFloat($d[1]);
					break;
				case Entity::DATA_TYPE_STRING:
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				    	$this->putString($d[1]);
				    }else{
				        $this->putLShort(strlen($d[1]));
				        $this->put($d[1]);
				    }
					break;
				case Entity::DATA_TYPE_SLOT:
				    
				        $this->putSlot($d[1]);
				    
					break;
				case Entity::DATA_TYPE_POS:
					$v = $d[1];
					if($v !== null){
						$this->putSignedBlockPosition($v->x, $v->y, $v->z);
					}else{
						$this->putSignedBlockPosition(0, 0, 0);
					}
					break;
				case Entity::DATA_TYPE_LONG:
				    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
				    	$this->putVarLong($d[1]);
				    }else{
				        $this->putLLong($d[1]);
				    }
					break;
				case Entity::DATA_TYPE_VECTOR3F:
					$this->putVector3Nullable($d[1]);
					break;
				default:
					throw new UnexpectedValueException("Invalid data type " . $d[0]);
			}
		}
		if($this->getProtocol() < ProtocolInfo::PROTOCOL_90){
		    $this->put("\x7f"); 
		}
	}
	public function getAttributeList() : array{
		$list = [];
		$count = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getShort();
		for($i = 0; $i < $count; ++$i){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$min = $this->getLFloat();
		    	$max = $this->getLFloat();
		    	$current = $this->getLFloat();
		    }else{
		    	$min = $this->getFloat();
		    	$max = $this->getFloat();
		    	$current = $this->getFloat();
		    }
			if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	
		    	$default = $this->getLFloat();
			}
			$name = $this->getString();
			$modifiers = [];
			
			$attr = Attribute::getAttributeByName($name);
			if($attr !== null){
				$attr->setMinValue($min);
				$attr->setMaxValue($max);
				$attr->setValue($current);
				$attr->setDefaultValue($default ?? $current);
				$attr->setModifiers($modifiers);
				$list[] = $attr;
			}else{
				throw new UnexpectedValueException("Unknown attribute type \"$name\"");
			}
		}
		return $list;
	}
	public function putAttributeList(Attribute ...$attributes) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putUnsignedVarInt(count($attributes));
	    }else{
	        $this->putShort(count($attributes));
	    }
		foreach($attributes as $attribute){
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
                $this->putLFloat($attribute->getMinValue());
                $this->putLFloat($attribute->getMaxValue());
                $this->putLFloat($attribute->getValue());
		    }else{
                $this->putFloat($attribute->getMinValue());
                $this->putFloat($attribute->getMaxValue());
                $this->putFloat($attribute->getValue());
		    }
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	
                $this->putLFloat($attribute->getDefaultValue());
            }
            $name = MultiversionEnums::getAttributeName($this->getProtocol(), $attribute->getId()) ?? $attribute->getName();
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
		    	$this->putString($name);
            }else{
                $this->putShortString($name);
            }
			
		}
	}
	final public function getEntityUniqueId() : int{
		return $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarLong() : $this->getLong();
	}
	public function putEntityUniqueId(int $eid) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarLong($eid);
	    }else{
	        $this->putLong($eid);
	    }
	}
	final public function getEntityRuntimeId() : int{
		return $this->getProtocol() < ProtocolInfo::PROTOCOL_90 ? $this->getLong() : $this->getUnsignedVarLong();
	}
	public function putEntityRuntimeId(int $eid) : void{
	    if($this->getProtocol() < ProtocolInfo::PROTOCOL_90){
	        $this->putLong($eid);
	    }else{
	    	$this->putUnsignedVarLong($eid);
	    }
	}
	public function getBlockPosition(&$x, &$y, &$z) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$x = $this->getVarInt();
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_92){
		        $y = $this->getUnsignedVarInt();
	    	}else{
		        $y = $this->getByte();
		    }
	    	$z = $this->getVarInt();
	    }else{
	        $x = $this->getInt();
	        $y = $this->getInt();
	        $z = $this->getInt();
	    }
	}
	public function putBlockPosition(int $x, int $y, int $z) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($x);
	    	if($this->getProtocol() >= ProtocolInfo::PROTOCOL_92){
	        	$this->putUnsignedVarInt($y);
	    	}else{
		        $this->putByte($y);
	    	}
	    	$this->putVarInt($z);
	    }else{
	        $this->putInt($x);
	        $this->putInt($y);
	        $this->putInt($z);
	    }
	}
	public function getSignedBlockPosition(&$x, &$y, &$z) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$x = $this->getVarInt();
	    	$y = $this->getVarInt();
	    	$z = $this->getVarInt();
	    }else{
	    	$x = $this->getLInt();
	    	$y = $this->getLInt();
	    	$z = $this->getLInt();
	    }
	}
	public function putSignedBlockPosition(int $x, int $y, int $z) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putVarInt($x);
	    	$this->putVarInt($y);
	    	$this->putVarInt($z);
	    }else{
	    	$this->putLInt($x);
	    	$this->putLInt($y);
	    	$this->putLInt($z);
	    }
	}
	public function getVector3() : Vector3{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	return new Vector3(
                $this->getLFloat(),
                $this->getLFloat(),
                $this->getLFloat()
		    );
	    }else{
	    	return new Vector3(
                $this->getFloat(),
                $this->getFloat(),
                $this->getFloat()
		    );
	    }
	}
	public function putVector3Nullable(?Vector3 $vector) : void{
		if($vector){
			$this->putVector3($vector);
		}else{
		    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
                $this->putLFloat(0.0);
                $this->putLFloat(0.0);
                $this->putLFloat(0.0);
		    }else{
                $this->putFloat(0.0);
                $this->putFloat(0.0);
                $this->putFloat(0.0);
		    }
		}
	}
	public function putVector3(Vector3 $vector) : void{
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
            $this->putLFloat($vector->x);
            $this->putLFloat($vector->y);
            $this->putLFloat($vector->z);
	    }else{
            $this->putFloat($vector->x);
            $this->putFloat($vector->y);
            $this->putFloat($vector->z);
	    }
	}
    public function getVector2() : Vector2{
        $x = $this->getLFloat();
        $y = $this->getLFloat();
        return new Vector2($x, $y);
    }
    public function putVector2(Vector2 $vector2) : void{
        $this->putLFloat($vector2->x);
        $this->putLFloat($vector2->y);
    }
	public function getByteRotation() : float{
		return (float) ((ord($this->get(1))) * (360 / 256));
	}
	public function putByteRotation(float $rotation) : void{
		($this->buffer .= chr((int) ($rotation / (360 / 256))));
	}
	public function getGameRules() : array{
		$count = $this->getUnsignedVarInt();
		$rules = [];
		for($i = 0; $i < $count; ++$i){
			$name = $this->getString();
			
			$type = $this->getUnsignedVarInt();
			$value = null;
			switch($type){
				case GameRuleType::BOOL:
					$value = (($this->get(1) !== "\x00"));
					break;
				case GameRuleType::INT:
					$value = $this->getUnsignedVarInt();
					break;
				case GameRuleType::FLOAT:
					$value = $this->getLFloat();
					break;
			}
			$rules[$name] = [$type, $value, $isPlayerModifiable ?? false];
		}
		return $rules;
	}
	public function putGameRules(array $rules) : void{
		$this->putUnsignedVarInt(count($rules));
		foreach($rules as $name => $rule){
			$this->putString($name);
			
	    	$this->putUnsignedVarInt($rule[0]);
			switch($rule[0]){
				case GameRuleType::BOOL:
                    $this->putBool($rule[1]);
					break;
				case GameRuleType::INT:
					$this->putUnsignedVarInt($rule[1]);
					break;
				case GameRuleType::FLOAT:
                    $this->putLFloat($rule[1]);
					break;
			}
		}
	}
	protected function getEntityLink() : EntityLink{
		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getEntityUniqueId();
		$link->toEntityUniqueId = $this->getEntityUniqueId();
		$link->type = $this->getByte();
		
		return $link;
	}
	protected function putEntityLink(EntityLink $link) : void{
		$this->putEntityUniqueId($link->fromEntityUniqueId);
		$this->putEntityUniqueId($link->toEntityUniqueId);
        $this->putByte($link->type);
		
	}
	protected function getCommandOriginData() : CommandOriginData{
		$result = new CommandOriginData();
		$result->type = $this->getUnsignedVarInt();
		$result->uuid = $this->getUUID();
		$result->requestId = $this->getString();
		if($result->type === CommandOriginData::ORIGIN_DEV_CONSOLE or $result->type === CommandOriginData::ORIGIN_TEST){
			$result->varlong1 = $this->getVarLong();
		}
		return $result;
	}
	protected function putCommandOriginData(CommandOriginData $data) : void{
		$this->putUnsignedVarInt($data->type);
		$this->putUUID($data->uuid);
		$this->putString($data->requestId);
		if($data->type === CommandOriginData::ORIGIN_DEV_CONSOLE or $data->type === CommandOriginData::ORIGIN_TEST){
			$this->putVarLong($data->varlong1);
		}
	}
	protected function getStructureSettings() : StructureSettings{
		$result = new StructureSettings();
		$result->paletteName = $this->getString();
		$result->ignoreEntities = $this->getBool();
		$result->ignoreBlocks = $this->getBool();
		$this->getBlockPosition($result->structureSizeX, $result->structureSizeY, $result->structureSizeZ);
		$this->getBlockPosition($result->structureOffsetX, $result->structureOffsetY, $result->structureOffsetZ);
		$result->lastTouchedByPlayerID = $this->getEntityUniqueId();
		$result->rotation = $this->getByte();
		$result->mirror = $this->getByte();
		$result->integrityValue = $this->getFloat();
		$result->integritySeed = $this->getInt();
		return $result;
	}
	protected function putStructureSettings(StructureSettings $structureSettings) : void{
		$this->putString($structureSettings->paletteName);
        $this->putBool($structureSettings->ignoreEntities);
        $this->putBool($structureSettings->ignoreBlocks);
		$this->putBlockPosition($structureSettings->structureSizeX, $structureSettings->structureSizeY, $structureSettings->structureSizeZ);
		$this->putBlockPosition($structureSettings->structureOffsetX, $structureSettings->structureOffsetY, $structureSettings->structureOffsetZ);
		$this->putEntityUniqueId($structureSettings->lastTouchedByPlayerID);
        $this->putByte($structureSettings->rotation);
        $this->putByte($structureSettings->mirror);
        $this->putFloat($structureSettings->integrityValue);
        $this->putInt($structureSettings->integritySeed);
	}
	protected function getStructureEditorData() : StructureEditorData{
		$result = new StructureEditorData();
		$result->structureName = $this->getString();
		
		$result->structureDataField = $this->getString();
		$result->includePlayers = $this->getBool();
		$result->showBoundingBox = $this->getBool();
		$result->structureBlockType = $this->getVarInt();
		$result->structureSettings = $this->getStructureSettings();
		$result->structureRedstoneSaveMove = $this->getVarInt();
		return $result;
	}
	protected function putStructureEditorData(StructureEditorData $structureEditorData) : void{
		$this->putString($structureEditorData->structureName);
		
		$this->putString($structureEditorData->structureDataField);
        $this->putBool($structureEditorData->includePlayers);
        $this->putBool($structureEditorData->showBoundingBox);
		$this->putVarInt($structureEditorData->structureBlockType);
		$this->putStructureSettings($structureEditorData->structureSettings);
		$this->putVarInt($structureEditorData->structureRedstoneSaveMove);
	}
	public function getNbtRoot() : NamedTag{
		$offset = $this->getOffset();
		try{
			$result = (new NetworkLittleEndianNBTStream())->read($this->getBuffer(), false, $offset, 512);
			assert($result instanceof NamedTag, "doMultiple is false so we should definitely have a NamedTag here");
			return $result;
		}finally{
			$this->setOffset($offset);
		}
	}
	public function getNbtCompoundRoot() : CompoundTag{
		$root = $this->getNbtRoot();
		if(!($root instanceof CompoundTag)){
			throw new UnexpectedValueException("Expected TAG_Compound root");
		}
		return $root;
	}
	public function readGenericTypeNetworkId() : int{
		return $this->getVarInt();
	}
	public function writeGenericTypeNetworkId(int $id) : void{
		$this->putVarInt($id);
	}
	public function readRecipeNetId() : int{
		return $this->getUnsignedVarInt();
	}
	public function writeRecipeNetId(int $id) : void{
		$this->putUnsignedVarInt($id);
	}
	public function readCreativeItemNetId() : int{
		return $this->getUnsignedVarInt();
	}
	public function writeCreativeItemNetId(int $id) : void{
		$this->putUnsignedVarInt($id);
	}
	public function readItemStackNetIdVariant() : int{
		return $this->getVarInt();
	}
	public function writeItemStackNetIdVariant(int $id) : void{
		$this->putVarInt($id);
	}
	public function readItemStackRequestId() : int{
		return $this->getVarInt();
	}
	public function writeItemStackRequestId(int $id) : void{
		$this->putVarInt($id);
	}
	public function readLegacyItemStackRequestId() : int{
		return $this->getVarInt();
	}
	public function writeLegacyItemStackRequestId(int $id) : void{
		$this->putVarInt($id);
	}
	public function readServerItemStackId() : int{
		return $this->getVarInt();
	}
	public function writeServerItemStackId(int $id) : void{
		$this->putVarInt($id);
	}
	protected function prepareGeometryDataForOld(?string $skinGeometryData) : ?string{
		if(!empty($skinGeometryData)){
			if(($tempData = @json_decode($skinGeometryData, true))){
				unset($tempData["format_version"]);
				return json_encode($tempData);
			}
		}
		return $skinGeometryData;
	}
    public function readOptional(Closure $reader) : mixed{
        if($this->getBool()){
            return $reader();
        }
        return null;
    }
    public function writeOptional(mixed $value, Closure $writer) : void{
        if($value !== null){
            $this->putBool(true);
            $writer($value);
        }else{
            $this->putBool(false);
        }
    }}