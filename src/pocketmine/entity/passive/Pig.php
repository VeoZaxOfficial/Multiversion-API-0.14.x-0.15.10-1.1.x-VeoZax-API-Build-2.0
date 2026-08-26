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
namespace pocketmine\entity\passive;
use pocketmine\entity\Animal;use pocketmine\entity\behavior\FloatBehavior;use pocketmine\entity\behavior\FollowParentBehavior;use pocketmine\entity\behavior\LookAtPlayerBehavior;use pocketmine\entity\behavior\MateBehavior;use pocketmine\entity\behavior\PanicBehavior;use pocketmine\entity\behavior\RandomLookAroundBehavior;use pocketmine\entity\behavior\RandomStrollBehavior;use pocketmine\entity\behavior\TemptBehavior;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\math\Vector3;use pocketmine\Player;use function boolval;use function intval;use function rand;
class Pig extends Animal{
	public const NETWORK_ID = self::PIG;
	public $width = 0.9;
	public $height = 0.9;
	protected function addBehaviors() : void{
		$this->behaviorPool->setBehavior(0, new FloatBehavior($this));
		$this->behaviorPool->setBehavior(1, new PanicBehavior($this, 1.25));
		$this->behaviorPool->setBehavior(2, new MateBehavior($this, 1.0));
		$this->behaviorPool->setBehavior(3, new TemptBehavior($this, [Item::CARROT], 1.2));
		$this->behaviorPool->setBehavior(4, new FollowParentBehavior($this, 1.1));
		$this->behaviorPool->setBehavior(5, new RandomStrollBehavior($this, 1.0));
		$this->behaviorPool->setBehavior(6, new LookAtPlayerBehavior($this, 6.0));
		$this->behaviorPool->setBehavior(7, new RandomLookAroundBehavior($this));
	}
	protected function initEntity() : void{
		$this->setMaxHealth(10);
		$this->setMovementSpeed(0.25);
		$this->setFollowRange(10);
		$this->setSaddled(boolval($this->namedtag->getByte("Saddle", 0)));
		parent::initEntity();
	}
	public function getName() : string{
		return "Pig";
	}
	public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
		if(parent::onInteract($player, $item, $clickPos)){
			return true;
		}
		if(!$this->isImmobile()){
			if($this->isSaddled() and $this->getRiddenByEntity() === null){
				$player->mountEntity($this);
				return true;
			}
		}
		return false;
	}
	public function getXpDropAmount() : int{
		return rand(1, ($this->isInLove() ? 7 : 3));
	}
	public function getDrops() : array{
		$drops = [
			($this->isOnFire() ? ItemFactory::get(Item::COOKED_PORKCHOP, 0, rand(1, 3)) : ItemFactory::get(Item::RAW_PORKCHOP, 0, rand(1, 3)))
		];
		if($this->isSaddled()){
			$drops[] = ItemFactory::get(Item::SADDLE, 0, 1);
		}
		return $drops;
	}
	public function isSaddled() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_SADDLED);
	}
	public function setSaddled(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_SADDLED, $value);
	}
	public function saveNBT() : void{
		parent::saveNBT();
		$this->namedtag->setByte("Saddle", intval($this->isSaddled()));
	}
	public function getRiderSeatPosition(int $seatNumber = 0) : Vector3{
		return new Vector3(0, 0.63, 0);
	}
	public function isBreedingItem(Item $item) : bool{
		return $item->getId() === Item::CARROT;
	}}