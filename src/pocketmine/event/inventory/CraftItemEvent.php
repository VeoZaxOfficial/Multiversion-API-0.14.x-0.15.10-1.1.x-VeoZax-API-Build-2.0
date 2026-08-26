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
namespace pocketmine\event\inventory;
use pocketmine\event\Cancellable;use pocketmine\event\Event;use pocketmine\inventory\CraftingRecipe;use pocketmine\inventory\transaction\CraftingTransaction;use pocketmine\item\Item;use pocketmine\Player;
class CraftItemEvent extends Event implements Cancellable{
	private $transaction;
	private $recipe;
	private $repetitions;
	private $inputs;
	private $outputs;
	public function __construct(CraftingTransaction $transaction, CraftingRecipe $recipe, int $repetitions, array $inputs, array $outputs){
		$this->transaction = $transaction;
		$this->recipe = $recipe;
		$this->repetitions = $repetitions;
		$this->inputs = $inputs;
		$this->outputs = $outputs;
	}
	public function getTransaction() : CraftingTransaction{
		return $this->transaction;
	}
	public function getRecipe() : CraftingRecipe{
		return $this->recipe;
	}
	public function getRepetitions() : int{
		return $this->repetitions;
	}
	public function getInputs() : array{
		return $this->inputs;
	}
	public function getOutputs() : array{
		return $this->outputs;
	}
	public function getPlayer() : Player{
		return $this->transaction->getSource();
	}}