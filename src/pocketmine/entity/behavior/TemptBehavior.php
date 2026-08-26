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
namespace pocketmine\entity\behavior;
use pocketmine\entity\Mob;use pocketmine\Player;use function in_array;
class TemptBehavior extends Behavior{
	protected $speedMultiplier;
	protected $temptItems;
	protected $delayTemptCounter = 0;
	protected $temptingPlayer;
	protected $scaredByPlayerMovement = false;
	public function __construct(Mob $mob, array $temptItemIds, float $speedMultiplier, bool $scaredByPlayerMovement = false){
		parent::__construct($mob);
		$this->temptItems = $temptItemIds;
		$this->speedMultiplier = $speedMultiplier;
		$this->scaredByPlayerMovement = $scaredByPlayerMovement;
		$this->mutexBits = 3;
	}
	public function canStart() : bool{
		if($this->delayTemptCounter > 0){
			$this->delayTemptCounter--;
			return false;
		}
		$player = $this->mob->level->getNearestEntity($this->mob, sqrt(10), Player::class);
		if($player instanceof Player){
			if(in_array($player->getInventory()->getItemInHand()->getId(), $this->temptItems)){
				$this->temptingPlayer = $player;
				return true;
			}
		}
		return false;
	}
	public function canContinue() : bool{
		if($this->scaredByPlayerMovement){
			if($this->temptingPlayer->hasMovementUpdate()){
				return false;
			}
		}
		return $this->canStart();
	}
	public function onTick() : void{
		$this->mob->getLookHelper()->setLookPositionWithEntity($this->temptingPlayer, 30, $this->mob->getVerticalFaceSpeed());
		if($this->temptingPlayer->distanceSquared($this->mob) < 6.25){
			$this->mob->getNavigator()->clearPath();
		}else{
			$this->mob->getNavigator()->tryMoveTo($this->temptingPlayer, $this->speedMultiplier);
		}
	}
	public function onEnd() : void{
		$this->delayTemptCounter = 100;
		$this->temptingPlayer = null;
		$this->mob->pitch = 0;
		$this->mob->getNavigator()->clearPath();
	}}