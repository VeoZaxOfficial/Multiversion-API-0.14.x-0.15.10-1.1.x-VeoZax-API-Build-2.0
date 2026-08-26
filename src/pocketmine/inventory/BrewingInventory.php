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
use pocketmine\item\Item;use pocketmine\network\mcpe\protocol\types\WindowTypes;use pocketmine\tile\BrewingStand;
class BrewingInventory extends ContainerInventory{
	public const SLOT_INGREDIENT = 0;
	public const SLOT_LEFT = 1;
	public const SLOT_MIDDLE = 2;
	public const SLOT_RIGHT = 3;
	public const SLOT_FUEL = 4;
	protected $holder;
	public function __construct(BrewingStand $holder){
		parent::__construct($holder);
	}
	public function getNetworkType() : int{
		return WindowTypes::BREWING_STAND;
	}
	public function getName() : string{
		return "Brewing";
	}
	public function getDefaultSize() : int{
		return 5; 
	}
	public function getHolder(){
		return $this->holder;
	}
	public function getIngredient() : Item{
		return $this->getItem(self::SLOT_INGREDIENT);
	}
	public function setIngredient(Item $item) : void{
		$this->setItem(self::SLOT_INGREDIENT, $item, true);
	}
	public function getPotions() : array{
		$return = [];
		for($i = 1; $i <= 3; $i++){
			$return[] = $this->getItem($i);
		}
		return $return;
	}
	public function getFuel() : Item{
		return $this->getItem(self::SLOT_FUEL);
	}
	public function setFuel(Item $fuel) : void{
		$this->setItem(self::SLOT_FUEL, $fuel);
	}}