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
namespace pocketmine\inventory\transaction;
use pocketmine\event\inventory\CraftItemEvent;use pocketmine\inventory\CraftingRecipe;use pocketmine\item\Item;use pocketmine\network\mcpe\protocol\ContainerClosePacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\types\ContainerIds;use pocketmine\Player;use function array_pop;use function count;use function intdiv;
class CraftingTransaction extends InventoryTransaction{
	protected $recipe;
	protected $repetitions;
	protected $inputs = [];
	protected $outputs = [];
	protected function matchRecipeItems(array $txItems, array $recipeItems, bool $wildcards, int $iterations = 0) : int{
		if(empty($recipeItems)){
			throw new TransactionValidationException("No recipe items given");
		}
		if(empty($txItems)){
			throw new TransactionValidationException("No transaction items given");
		}
		while(!empty($recipeItems)){
			$recipeItem = array_pop($recipeItems);
			$needCount = $recipeItem->getCount();
			foreach($recipeItems as $i => $otherRecipeItem){
				if($otherRecipeItem->equals($recipeItem)){ 
					$needCount += $otherRecipeItem->getCount();
					unset($recipeItems[$i]);
				}
			}
			$haveCount = 0;
			foreach($txItems as $j => $txItem){
				if($txItem->equals($recipeItem, !$wildcards or !$recipeItem->hasAnyDamageValue(), !$wildcards or $recipeItem->hasCompoundTag())){
					$haveCount += $txItem->getCount();
					unset($txItems[$j]);
				}
			}
			if($haveCount % $needCount !== 0){
				throw new TransactionValidationException("Expected an exact multiple of required $recipeItem (given: $haveCount, needed: $needCount)");
			}
			$multiplier = intdiv($haveCount, $needCount);
			if($multiplier < 1){
				throw new TransactionValidationException("Expected more than zero items matching $recipeItem (given: $haveCount, needed: $needCount)");
			}
			if($iterations === 0){
				$iterations = $multiplier;
			}elseif($multiplier !== $iterations){
				throw new TransactionValidationException("Expected $recipeItem x$iterations, but found x$multiplier");
			}
		}
		if($iterations < 1){
			throw new TransactionValidationException("Tried to craft zero times");
		}
		if(!empty($txItems)){
			throw new TransactionValidationException("Expected 0 ingredients left over, have " . count($txItems));
		}
		return $iterations;
	}
	public function validate() : void{
		$this->squashDuplicateSlotChanges();
		if(count($this->actions) < 1){
			throw new TransactionValidationException("Transaction must have at least one action to be executable");
		}
		$this->matchItems($this->outputs, $this->inputs);
		$failed = 0;
		foreach($this->source->getServer()->getCraftingManager()->matchRecipeByOutputs($this->outputs, $this->source->getCraftingProtocol()) as $recipe){
			try{
				$this->repetitions = $this->matchRecipeItems($this->outputs, $recipe->getResultsFor($this->source->getCraftingGrid()), false);
				$this->matchRecipeItems($this->inputs, $recipe->getIngredientList(), true, $this->repetitions);
				$this->recipe = $recipe;
				break;
			}catch(TransactionValidationException $e){
				++$failed;
			}
		}
		if($this->recipe === null){
			throw new TransactionValidationException("Unable to match a recipe to transaction (tried to match against $failed recipes)");
		}
	}
	protected function callExecuteEvent() : bool{
		$ev = new CraftItemEvent($this, $this->recipe, $this->repetitions, $this->inputs, $this->outputs);
		$ev->call();
		return !$ev->isCancelled();
	}
	protected function sendInventories() : void{
		
			return;
		
		parent::sendInventories();
		$pk = new ContainerClosePacket();
		
	    	$pk->windowId = ContainerIds::NONE;
		
		$pk->windowType = $this->source->getCurrentWindowType();
		$pk->server = true;
		$this->source->dataPacket($pk);
	}
	public function execute() : bool{
		if(parent::execute()){
			foreach($this->outputs as $item){
				switch($item->getId()){
					case Item::CRAFTING_TABLE:
						$this->source->awardAchievement("buildWorkBench");
						break;
					case Item::WOODEN_PICKAXE:
						$this->source->awardAchievement("buildPickaxe");
						break;
					case Item::FURNACE:
						$this->source->awardAchievement("buildFurnace");
						break;
					case Item::WOODEN_HOE:
						$this->source->awardAchievement("buildHoe");
						break;
					case Item::BREAD:
						$this->source->awardAchievement("makeBread");
						break;
					case Item::CAKE:
						$this->source->awardAchievement("bakeCake");
						break;
					case Item::STONE_PICKAXE:
					case Item::GOLDEN_PICKAXE:
					case Item::IRON_PICKAXE:
					case Item::DIAMOND_PICKAXE:
						$this->source->awardAchievement("buildBetterPickaxe");
						break;
					case Item::WOODEN_SWORD:
						$this->source->awardAchievement("buildSword");
						break;
					case Item::DIAMOND:
						$this->source->awardAchievement("diamond");
						break;
				}
			}
			return true;
		}
		return false;
	}}