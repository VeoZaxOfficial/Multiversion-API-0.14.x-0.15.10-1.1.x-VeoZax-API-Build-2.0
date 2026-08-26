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

namespace pocketmine\network\mcpe\protocol\legacy\p84;
use pocketmine\utils\p70\Binary;
use pocketmine\inventory\FurnaceRecipe;use pocketmine\inventory\ShapedRecipe;use pocketmine\inventory\ShapelessRecipe;use pocketmine\item\enchantment\Enchantment;use pocketmine\item\enchantment\EnchantmentList;use pocketmine\utils\p70\BinaryStream;
class CraftingDataPacket extends DataPacket{
	const NETWORK_ID = Info::CRAFTING_DATA_PACKET;
	const ENTRY_SHAPELESS = 0;
	const ENTRY_SHAPED = 1;
	const ENTRY_FURNACE = 2;
	const ENTRY_FURNACE_DATA = 3;
	const ENTRY_ENCHANT_LIST = 4;
	public $entries = [];
	public $cleanRecipes = \false;
	private static function writeEntry($entry, BinaryStream $stream){
		if($entry instanceof ShapelessRecipe){
			return self::writeShapelessRecipe($entry, $stream);
		}elseif($entry instanceof ShapedRecipe){
			return self::writeShapedRecipe($entry, $stream);
		}elseif($entry instanceof FurnaceRecipe){
			return self::writeFurnaceRecipe($entry, $stream);
		}elseif($entry instanceof EnchantmentList){
			return self::writeEnchantList($entry, $stream);
		}
		return -1;
	}
	private static function writeShapelessRecipe(ShapelessRecipe $recipe, BinaryStream $stream){
		$stream->putInt($recipe->getIngredientCount());
		foreach($recipe->getIngredientList() as $item){
			$stream->putSlot($item);
		}
		$stream->putInt(1);
		$stream->putSlot($recipe->getResults()[0]);
		$stream->put(\str_repeat("\x00", 16));
		return CraftingDataPacket::ENTRY_SHAPELESS;
	}
	private static function writeShapedRecipe(ShapedRecipe $recipe, BinaryStream $stream){
		$stream->putInt($recipe->getWidth());
		$stream->putInt($recipe->getHeight());
		for($y = 0; $y < $recipe->getHeight(); ++$y){
			for($x = 0; $x < $recipe->getWidth(); ++$x){
				$stream->putSlot($recipe->getIngredient($x, $y));
			}
		}
		$stream->putInt(1);
		$stream->putSlot($recipe->getResults()[0]);
		$stream->put(\str_repeat("\x00", 16));
		return CraftingDataPacket::ENTRY_SHAPED;
	}
	private static function writeFurnaceRecipe(FurnaceRecipe $recipe, BinaryStream $stream){
		if($recipe->getInput()->getDamage() !== 0){ 
			$stream->putInt(($recipe->getInput()->getId() << 16) | ($recipe->getInput()->getDamage()));
			$stream->putSlot($recipe->getResult());
			return CraftingDataPacket::ENTRY_FURNACE_DATA;
		}else{
			$stream->putInt($recipe->getInput()->getId());
			$stream->putSlot($recipe->getResult());
			return CraftingDataPacket::ENTRY_FURNACE;
		}
	}
	private static function writeEnchantList(EnchantmentList $list, BinaryStream $stream){
		$stream->putByte($list->getSize());
		for($i = 0; $i < $list->getSize(); ++$i){
			$entry = $list->getSlot($i);
			$stream->putInt($entry->getCost());
			$stream->putByte(\count($entry->getEnchantments()));
			foreach($entry->getEnchantments() as $enchantment){
				$stream->putInt($enchantment->getId());
				$stream->putInt($enchantment->getLevel());
			}
			$stream->putString($entry->getRandomName());
		}
		return CraftingDataPacket::ENTRY_ENCHANT_LIST;
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
	public function addEnchantList(EnchantmentList $list){
		$this->entries[] = $list;
	}
	public function clean(){
		$this->entries = [];
		return parent::clean();
	}
	public function decode(){
	}
	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= pack("N", \count($this->entries));
		$writer = new BinaryStream();
		foreach($this->entries as $d){
			$entryType = self::writeEntry($d, $writer);
			if($entryType >= 0){
				$this->buffer .= pack("N", $entryType);
				$this->buffer .= pack("N", \strlen($writer->getBuffer()));
				$this->buffer .= $writer->getBuffer();
			}else{
				$this->buffer .= pack("N", -1);
				$this->buffer .= pack("N", 0);
			}
			$writer->reset();
		}
		$this->buffer .= chr($this->cleanRecipes ? 1 : 0);
	}
}