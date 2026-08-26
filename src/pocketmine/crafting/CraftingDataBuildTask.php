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
namespace pocketmine\crafting;
use pocketmine\thread\NonThreadSafeValue;use pocketmine\block\BlockFactory;use pocketmine\item\ItemFactory;use pocketmine\network\mcpe\multiversion\block\BlockPalette;use pocketmine\network\mcpe\multiversion\block\palettes\Palette;use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;use pocketmine\network\mcpe\protocol\BatchPacket;use pocketmine\network\mcpe\protocol\CraftingDataPacket;use pocketmine\network\mcpe\protocol\PacketPool;use pocketmine\network\mcpe\protocol\types\PotionTypeRecipe;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;use function assert;use function strlen;
class CraftingDataBuildTask extends AsyncTask{
    protected $shapelessRecipes;
    protected $shapedRecipes;
    protected $furnaceRecipes;
	protected $brewingRecipes;
    protected $protocol;
    protected $palette;
    protected $compressionLevel;
	public function __construct(array $shapelessRecipes, array $shapedRecipes, array $furnaceRecipes, array $brewingRecipes, int $protocol, ?Palette $palette, int $networkCompressionLevel){
	    $this->shapelessRecipes = new NonThreadSafeValue($shapelessRecipes);
	    $this->shapedRecipes = new NonThreadSafeValue($shapedRecipes);
	    $this->furnaceRecipes = new NonThreadSafeValue($furnaceRecipes);
		$this->brewingRecipes = new NonThreadSafeValue($brewingRecipes);
	    $this->protocol = $protocol;
	    $this->palette = new NonThreadSafeValue($palette);
		$this->compressionLevel = $networkCompressionLevel;
	}
	public function onRun() : void{
	    ItemPalette::init();
	    if(($palette = $this->palette->deserialize()) !== null){
	        BlockPalette::addPalette($palette, $this->protocol);
	    }
	    PacketPool::init();
	    ItemFactory::init();
		BlockFactory::init();
	    $pk = new CraftingDataPacket();
	    $pk->cleanRecipes = true;
	    foreach($this->shapelessRecipes->deserialize() as $list){
		    foreach($list as $recipe){
			    $pk->addShapelessRecipe($recipe);
		    }
	    }
	    foreach($this->shapedRecipes->deserialize() as $list){
		    foreach($list as $recipe){
			    $pk->addShapedRecipe($recipe);
		    }
	    }
	    foreach($this->furnaceRecipes->deserialize() as $recipe){
		    $pk->addFurnaceRecipe($recipe);
	    }
		foreach($this->brewingRecipes->deserialize() as $recipe){
			$input = $recipe->getPotion();
			$ingredient = $recipe->getInput();
			$output = $recipe->getResult();
			$pk->potionTypeRecipes[] = new PotionTypeRecipe(
				$input->getId(),
				$input->getDamage(),
				$ingredient->getId(),
				$ingredient->getDamage(),
				$output->getId(),
				$output->getDamage()
			);
		}
        $pk->setProtocol($this->protocol);
	    $pk->encode();
	    $batch = new BatchPacket();
	    $batch->setProtocol($this->protocol);
	    $batch->addPacket($pk);
	    $batch->setCompressionLevel($this->compressionLevel);
	    $batch->encode();
		$this->setResult($batch->getBuffer());
	}
	public function onCompletion(Server $server){
	    $result = $this->getResult();
		$batch = new BatchPacket($result);
		assert(strlen($batch->buffer) > 0);
		$batch->setProtocol($this->protocol);
		$batch->isEncoded = true;
		$server->getCraftingManager()->buildCraftingDataCache($batch, $this->protocol);
	}}