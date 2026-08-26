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
use BadMethodCallException;use InvalidStateException;use pocketmine\item\Item;use pocketmine\Player;use function max;use function min;use const PHP_INT_MAX;
class CraftingGrid extends BaseInventory{
	public const SIZE_SMALL = 2;
	public const SIZE_BIG = 3;
	protected $holder;
	private $gridWidth;
	private $startX;
	private $xLen;
	private $startY;
	private $yLen;
	public function __construct(Player $holder, int $gridWidth){
		$this->holder = $holder;
		$this->gridWidth = $gridWidth;
		parent::__construct();
	}
	public function getGridWidth() : int{
		return $this->gridWidth;
	}
	public function getDefaultSize() : int{
		return $this->getGridWidth() ** 2;
	}
	public function setSize(int $size){
		throw new BadMethodCallException("Cannot change the size of a crafting grid");
	}
	public function getName() : string{
		return "Crafting";
	}
	public function setItem(int $index, Item $item, bool $send = true) : bool{
		if(parent::setItem($index, $item, $send)){
			$this->seekRecipeBounds();
			return true;
		}
		return false;
	}
	public function sendSlot(int $index, $target) : void{
	}
	public function sendContents($target) : void{
	}
	public function getHolder(){
		return $this->holder;
	}
	private function seekRecipeBounds() : void{
		$minX = PHP_INT_MAX;
		$maxX = 0;
		$minY = PHP_INT_MAX;
		$maxY = 0;
		$empty = true;
		for($y = 0; $y < $this->gridWidth; ++$y){
			for($x = 0; $x < $this->gridWidth; ++$x){
				if(!$this->isSlotEmpty($y * $this->gridWidth + $x)){
					$minX = min($minX, $x);
					$maxX = max($maxX, $x);
					$minY = min($minY, $y);
					$maxY = max($maxY, $y);
					$empty = false;
				}
			}
		}
		if(!$empty){
			$this->startX = $minX;
			$this->xLen = $maxX - $minX + 1;
			$this->startY = $minY;
			$this->yLen = $maxY - $minY + 1;
		}else{
			$this->startX = $this->xLen = $this->startY = $this->yLen = null;
		}
	}
	public function getIngredient(int $x, int $y) : Item{
		if($this->startX !== null and $this->startY !== null){
			return $this->getItem(($y + $this->startY) * $this->gridWidth + ($x + $this->startX));
		}
		throw new InvalidStateException("No ingredients found in grid");
	}
	public function getRecipeWidth() : int{
		return $this->xLen ?? 0;
	}
	public function getRecipeHeight() : int{
		return $this->yLen ?? 0;
	}}