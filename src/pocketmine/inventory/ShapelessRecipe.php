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
namespace pocketmine\inventory;
use InvalidArgumentException;use pocketmine\item\Item;use function array_map;use function count;
class ShapelessRecipe implements CraftingRecipe{
	private $ingredients = [];
	private $results;
	private $priority;
	public function __construct(array $ingredients, array $results, int $priority = 50){
		foreach($ingredients as $item){
			$this->addIngredient($item);
		}
		$this->results = array_map(function(Item $item) : Item{ return clone $item; }, $results);
		$this->priority = $priority;
	}
	public function getResults() : array{
		return array_map(function(Item $item) : Item{ return clone $item; }, $this->results);
	}
	public function getResultsFor(CraftingGrid $grid) : array{
		return $this->getResults();
	}
	public function addIngredient(Item $item) : ShapelessRecipe{
		if(count($this->ingredients) + $item->getCount() > 9){
			throw new InvalidArgumentException("Shapeless recipes cannot have more than 9 ingredients");
		}
		while($item->getCount() > 0){
			$this->ingredients[] = $item->pop();
		}
		return $this;
	}
	public function removeIngredient(Item $item){
		foreach($this->ingredients as $index => $ingredient){
			if($item->getCount() <= 0){
				break;
			}
			if($ingredient->equals($item, !$item->hasAnyDamageValue(), $item->hasCompoundTag())){
				unset($this->ingredients[$index]);
				$item->pop();
			}
		}
		return $this;
	}
	public function getIngredientList() : array{
		return array_map(function(Item $item) : Item{ return clone $item; }, $this->ingredients);
	}
	public function getIngredientCount() : int{
		$count = 0;
		foreach($this->ingredients as $ingredient){
			$count += $ingredient->getCount();
		}
		return $count;
	}
	public function registerToCraftingManager(CraftingManager $manager, ?int $protocol = null) : void{
		$manager->registerShapelessRecipe($this, $protocol);
	}
	public function matchesCraftingGrid(CraftingGrid $grid) : bool{
		$input = $grid->getContents();
		foreach($this->ingredients as $needItem){
			foreach($input as $j => $haveItem){
				if($haveItem->equals($needItem, !$needItem->hasAnyDamageValue(), $needItem->hasCompoundTag()) and $haveItem->getCount() >= $needItem->getCount()){
					unset($input[$j]);
					continue 2;
				}
			}
			return false; 
		}
		return empty($input); 
	}
	public function setPriority(int $priority) : ShapelessRecipe{
		$this->priority = $priority;
		return $this;
	}
	public function getPriority() : int{
		return $this->priority;
	}}