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
use pocketmine\inventory\FurnaceRecipe;use pocketmine\inventory\ShapedRecipe;use pocketmine\inventory\ShapelessRecipe;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\PotionContainerChangeRecipe;use pocketmine\network\mcpe\protocol\types\PotionTypeRecipe;use pocketmine\network\mcpe\protocol\types\recipe\MaterialReducerRecipe;use pocketmine\network\mcpe\protocol\types\recipe\MaterialReducerRecipeOutput;use pocketmine\utils\Binary;use UnexpectedValueException;use function count;use function pack;use function str_repeat;use function strlen;
class CraftingDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CRAFTING_DATA_PACKET;
	public const ENTRY_SHAPELESS = 0;
	public const ENTRY_SHAPED = 1;
	public const ENTRY_FURNACE = 2;
	public const ENTRY_FURNACE_DATA = 3;
	public const ENTRY_MULTI = 4; 
	public const ENTRY_SHULKER_BOX = 5; 
	public const ENTRY_SHAPELESS_CHEMISTRY = 6; 
	public const ENTRY_SHAPED_CHEMISTRY = 7; 
	public $entries = [];
	public $potionTypeRecipes = [];
	public $potionContainerRecipes = [];
	public $materialReducerRecipes = [];
	public $cleanRecipes = false;
	public $decodedEntries = [];
	public function clean(){
		$this->entries = [];
		$this->decodedEntries = [];
		return parent::clean();
	}
    protected function decodePayload() : void{
        $this->decodedEntries = [];
        $recipeCount = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getInt();
        for($i = 0; $i < $recipeCount; ++$i){
            $entry = [];
            $entry["type"] = $recipeType = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getVarInt() : $this->getInt();
            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
                $this->getInt(); 
            }
            switch($recipeType){
                case self::ENTRY_SHAPELESS:
                case self::ENTRY_SHULKER_BOX:
                case self::ENTRY_SHAPELESS_CHEMISTRY:
                    
                    $ingredientCount = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getInt();
                    $entry["input"] = [];
                    for($j = 0; $j < $ingredientCount; ++$j){
                        
                            $entry["input"][] = $this->getSlot(false);
                        
                    }
                    $resultCount = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getInt();
                    $entry["output"] = [];
                    for($k = 0; $k < $resultCount; ++$k){
                        $entry["output"][] = $this->getSlot(false);
                    }
                    $entry["uuid"] = $this->getUUID()->toString();
                    
                    break;
                case self::ENTRY_SHAPED:
                case self::ENTRY_SHAPED_CHEMISTRY:
                    
                    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
                        $entry["width"] = $this->getVarInt();
                        $entry["height"] = $this->getVarInt();
                    }else{
                        $entry["width"] = $this->getInt();
                        $entry["height"] = $this->getInt();
                    }
                    $count = $entry["width"] * $entry["height"];
                    $entry["input"] = [];
                    for($j = 0; $j < $count; ++$j){
                        
                            $entry["input"][] = $this->getSlot(false);
                        
                    }
                    $resultCount = $this->getProtocol() >= ProtocolInfo::PROTOCOL_90 ? $this->getUnsignedVarInt() : $this->getInt();
                    $entry["output"] = [];
                    for($k = 0; $k < $resultCount; ++$k){
                        $entry["output"][] = $this->getSlot(false);
                    }
                    $entry["uuid"] = $this->getUUID()->toString();
                    
                    break;
                case self::ENTRY_FURNACE:
                case self::ENTRY_FURNACE_DATA:
                    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
                        $inputId = $this->getVarInt();
                    }
                    
                        $inputData = -1;
                        if($recipeType === self::ENTRY_FURNACE_DATA){
                            if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
                                $inputData = $this->getVarInt();
                            }else{
                                $value = $this->getInt();
                                $inputId = $value >> 16;
                                $damage = $value & 0xFFFF;
                            }
                            if($inputData === 0x7fff){
                                $inputData = -1;
                            }
                        }else{
                            $inputId = $this->getInt();
                        }
                    
                    $entry["input"] = ItemFactory::get($inputId, $inputData);
                    
                        $entry["output"] = $this->getSlot(false);
                    
                    
                    break;
                case self::ENTRY_MULTI:
                    $entry["uuid"] = $this->getUUID()->toString();
                    
                    break;
                default:
                    throw new UnexpectedValueException("Unhandled recipe type $recipeType!"); 
            }
            $this->decodedEntries[] = $entry;
        }
        
        $this->cleanRecipes = $this->getBool();
    }
    private static function writeEntry($entry, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
		if($entry instanceof ShapelessRecipe){
			return self::writeShapelessRecipe($entry, $stream, $pos, $playerProtocol);
		}elseif($entry instanceof ShapedRecipe){
			return self::writeShapedRecipe($entry, $stream, $pos, $playerProtocol);
		}elseif($entry instanceof FurnaceRecipe){
			return self::writeFurnaceRecipe($entry, $stream, $playerProtocol);
		}
		return -1;
	}
	private static function writeShapelessRecipe(ShapelessRecipe $recipe, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
	    
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_90){
	    	$stream->putUnsignedVarInt($recipe->getIngredientCount());
	    }else{
	        $stream->putInt($recipe->getIngredientCount());
	    }
		foreach($recipe->getIngredientList() as $item){
		    
		        $stream->putSlot($item, false);
		    
		}
		$results = $recipe->getResults();
		if($playerProtocol >= ProtocolInfo::PROTOCOL_90){
	    	$stream->putUnsignedVarInt(count($results));
		}else{
		    $stream->putInt(count($results));
		}
		foreach($results as $item){
			$stream->putSlot($item, false);
		}
		$stream->put(str_repeat("\x00", 16)); 
		
		return CraftingDataPacket::ENTRY_SHAPELESS;
	}
	private static function writeShapedRecipe(ShapedRecipe $recipe, NetworkBinaryStream $stream, int $pos, int $playerProtocol){
	    
	    if($playerProtocol >= ProtocolInfo::PROTOCOL_90){
	    	$stream->putVarInt($recipe->getWidth());
	    	$stream->putVarInt($recipe->getHeight());
	    }else{
	    	$stream->putInt($recipe->getWidth());
	    	$stream->putInt($recipe->getHeight());
	    }
		for($z = 0; $z < $recipe->getHeight(); ++$z){
			for($x = 0; $x < $recipe->getWidth(); ++$x){
			    $ingredient = $recipe->getIngredient($x, $z);
			    
			        $stream->putSlot($ingredient, false);
			    
			}
		}
		$results = $recipe->getResults();
		if($playerProtocol >= ProtocolInfo::PROTOCOL_90){
	    	$stream->putUnsignedVarInt(count($results));
		}else{
		    $stream->putInt(count($results));
		}
		foreach($results as $item){
			$stream->putSlot($item, false);
		}
		$stream->put(str_repeat("\x00", 16)); 
		
		return CraftingDataPacket::ENTRY_SHAPED;
	}
	private static function writeFurnaceRecipe(FurnaceRecipe $recipe, NetworkBinaryStream $stream, int $playerProtocol){
	    $id = $recipe->getInput()->getId();
	    $damage = $recipe->getInput()->getDamage();
	    
		if($playerProtocol >= ProtocolInfo::PROTOCOL_90){
	    	$stream->putVarInt($id);
		}
		
	    	$result = CraftingDataPacket::ENTRY_FURNACE;
	    	if(!$recipe->getInput()->hasAnyDamageValue()){ 
	    	    if($playerProtocol >= ProtocolInfo::PROTOCOL_90){
		        	$stream->putVarInt($damage);
	    	    }else{
	    	        $stream->putInt(($recipe->getInput()->getId() << 16) | ($recipe->getInput()->getDamage()));
	    	    }
		    	$result = CraftingDataPacket::ENTRY_FURNACE_DATA;
	    	}elseif($playerProtocol < ProtocolInfo::PROTOCOL_90){
	    	    $stream->putInt($recipe->getInput()->getId());
	    	}
		
		$stream->putSlot($recipe->getResult(), false);
		
		return $result;
	}
	public function addShapelessRecipe(ShapelessRecipe $recipe){
		$this->entries[] = $recipe;
	}
	public function addShapedRecipe(ShapedRecipe $recipe){
		$this->entries[] = $recipe;
	}
	public function addFurnaceRecipe(FurnaceRecipe $recipe){
		$this->entries[] = $recipe;
	}
	protected function encodePayload(){
	    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
	    	$this->putUnsignedVarInt(count($this->entries));
	    }else{
	        $this->putInt(count($this->entries));
	    }
		$writer = new NetworkBinaryStream();
		$writer->setProtocol($this->getProtocol());
		$counter = 0;
		foreach($this->entries as $d){
			$entryType = self::writeEntry($d, $writer, $counter++, $this->getProtocol());
			if($entryType >= 0){
			    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			    	$this->putVarInt($entryType);
			    }else{
			        $this->putInt($entryType);
			        $this->putInt(strlen($writer->getBuffer()));
			    }
                $this->put($writer->getBuffer());
			}else{
			    if($this->getProtocol() >= ProtocolInfo::PROTOCOL_90){
			    	$this->putVarInt(-1);
			    }else{
			        $this->putInt(-1);
			        $this->putInt(0);
			    }
			}
			$writer->reset();
		}
		
        $this->putBool($this->cleanRecipes);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCraftingData($this);
	}}