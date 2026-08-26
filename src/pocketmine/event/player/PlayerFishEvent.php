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
namespace pocketmine\event\player;
use pocketmine\entity\Entity;use pocketmine\entity\projectile\FishingHook;use pocketmine\event\Cancellable;use pocketmine\Player;
class PlayerFishEvent extends PlayerEvent implements Cancellable{
	public const STATE_FISHING = 0;
	public const STATE_CAUGHT_FISH = 1;
	public const STATE_CAUGHT_ENTITY = 2;
	protected $hook;
	protected $xpDropAmount = 0;
	protected $state = 0;
	protected $result;
	protected $name = null;
	protected $lore = null;
	public function __construct(Player $fisher, FishingHook $hook, int $state, $result, $name, $lore, int $xpDropAmount = 0){
		$this->player = $fisher;
		$this->hook = $hook;
		$this->state = $state;
		$this->xpDropAmount = $xpDropAmount;
		$this->result = $result;
		$this->name = $name;
		$this->lore = $lore;
	}
	public function getResult(){
		return $this->result;
	}
	public function setResult($result){
		$this->result = $result;
	}
	public function getName(){
		return $this->name;
	}
	public function setName($name){
		$this->name = $name;
	}
	public function getLore(){
		return $this->lore;
	}
	public function setLore($lore){
		$this->lore = $lore;
	}
	public function getCaughtEntity() : ?Entity{
		return $this->hook->getRidingEntity();
	}
	public function getHook() : FishingHook{
		return $this->hook;
	}
	public function getXpDropAmount() : int{
		return $this->xpDropAmount;
	}
	public function setXpDropAmount(int $xpDropAmount) : void{
		$this->xpDropAmount = $xpDropAmount;
	}
	public function getState() : int{
		return $this->state;
	}}