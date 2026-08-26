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
use InvalidArgumentException;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use function array_map;use function array_values;use function count;use function implode;use function strlen;use function strpos;
class ShapedRecipe implements CraftingRecipe{
	private $shape = [];
	private $ingredientList = [];
	private $results = [];
	private $height;
	private $width;
	private $priority;
	public function __construct(array $shape, array $ingredients, array $results, int $priority = 50){
		$this->height = count($shape);
		if($this->height > 3 or $this->height <= 0){
			throw new InvalidArgumentException("Shaped recipes may only have 1, 2 or 3 rows, not $this->height");
		}
		$shape = array_values($shape);
		$this->width = strlen($shape[0]);
		if($this->width > 3 or $this->width <= 0){
			throw new InvalidArgumentException("Shaped recipes may only have 1, 2 or 3 columns, not $this->width");
		}
		foreach($shape as $y => $row){
			if(strlen($row) !== $this->width){
				throw new InvalidArgumentException("Shaped recipe rows must all have the same length (expected $this->width, got " . strlen($row) . ")");
			}
			for($x = 0; $x < $this->width; ++$x){
				if($row[$x] !== ' ' and !isset($ingredients[$row[$x]])){
					throw new InvalidArgumentException("No item specified for symbol '" . $row[$x] . "'");
				}
			}
		}
		$this->shape = $shape;
		foreach($ingredients as $char => $i){
			$this->setIngredient($char, $i);
		}
		$this->results = array_map(function(Item $item) : Item{ return clone $item; }, $results);
		$this->priority = $priority;
	}
	public function getWidth() : int{
		return $this->width;
	}
	public function getHeight() : int{
		return $this->height;
	}
	public function getResults() : array{
		return array_map(function(Item $item) : Item{ return clone $item; }, $this->results);
	}
	public function getResultsFor(CraftingGrid $grid) : array{
		return $this->getResults();
	}
	public function setIngredient(string $key, Item $item){
		if(strpos(implode($this->shape), $key) === false){
			throw new InvalidArgumentException("Symbol '$key' does not appear in the recipe shape");
		}
		$this->ingredientList[$key] = clone $item;
		return $this;
	}
	public function getIngredientMap() : array{
		$ingredients = [];
		for($y = 0; $y < $this->height; ++$y){
			for($x = 0; $x < $this->width; ++$x){
				$ingredients[$y][$x] = $this->getIngredient($x, $y);
			}
		}
		return $ingredients;
	}
	public function getIngredientList() : array{
		$ingredients = [];
		for($y = 0; $y < $this->height; ++$y){
			for($x = 0; $x < $this->width; ++$x){
				$ingredient = $this->getIngredient($x, $y);
				if(!$ingredient->isNull()){
					$ingredients[] = $ingredient;
				}
			}
		}
		return $ingredients;
	}
	public function getIngredient(int $x, int $y) : Item{
		$exists = $this->ingredientList[$this->shape[$y][$x]] ?? null;
		return $exists !== null ? clone $exists : ItemFactory::get(Item::AIR, 0, 0);
	}
	public function getShape() : array{
		return $this->shape;
	}
	public function registerToCraftingManager(CraftingManager $manager, ?int $protocol = null) : void{
		$manager->registerShapedRecipe($this, $protocol);
	}
	private function matchInputMap(CraftingGrid $grid, bool $reverse) : bool{
		for($y = 0; $y < $this->height; ++$y){
			for($x = 0; $x < $this->width; ++$x){
				$given = $grid->getIngredient($reverse ? $this->width - $x - 1 : $x, $y);
				$required = $this->getIngredient($x, $y);
				if(!$required->equals($given, !$required->hasAnyDamageValue(), $required->hasCompoundTag()) or $required->getCount() > $given->getCount()){
					return false;
				}
			}
		}
		return true;
	}
	public function matchesCraftingGrid(CraftingGrid $grid) : bool{
		if($this->width !== $grid->getRecipeWidth() or $this->height !== $grid->getRecipeHeight()){
			return false;
		}
		return $this->matchInputMap($grid, false) or $this->matchInputMap($grid, true);
	}
	public function setPriority(int $priority) : ShapedRecipe{
		$this->priority = $priority;
		return $this;
	}
	public function getPriority() : int{
		return $this->priority;
	}}