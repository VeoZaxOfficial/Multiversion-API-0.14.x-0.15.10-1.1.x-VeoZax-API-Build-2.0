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
namespace pocketmine\entity\object;
use pocketmine\block\Fence;use pocketmine\entity\Entity;use pocketmine\entity\Living;use pocketmine\event\entity\EntityDamageEvent;use pocketmine\item\Item;use pocketmine\level\Level;use pocketmine\level\Position;use pocketmine\math\AxisAlignedBB;use pocketmine\math\Vector3;use pocketmine\nbt\tag\CompoundTag;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\Player;
class LeashKnot extends Entity{
	public const NETWORK_ID = self::LEASH_KNOT;
	protected $gravity = 0.0;
	protected $drag = 0.0;
	public $height = 0.33;
	public $width = 0.1875;
	public $dropCounter = 0;
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setPosition($this->getHangingPosition()->add(0.5, 0.25, 0.5));
		$this->boundingBox = new AxisAlignedBB($this->x - 0.1875, $this->y - 0.25 + 0.125, $this->z - 0.1875, $this->x + 0.1875, $this->y + 0.25 + 0.125, $this->z + 0.1875);
	}
	public function initEntity() : void{
		$this->setMaxHealth(1);
		parent::initEntity();
	}
	public function isSurfaceValid() : bool{
		return $this->level->getBlock($this) instanceof Fence;
	}
	public function hasMovementUpdate() : bool{
		return false;
	}
	protected function updateMovement(bool $teleport = false) : void{
	}
	public function canBeCollidedWith() : bool{
		return false;
	}
	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->dropCounter++ >= 100 and $this->isValid()){
			$this->dropCounter = 0;
			if(!$this->isSurfaceValid() and !$this->isFlaggedForDespawn()){
				$this->kill();
			}
		}
		return parent::entityBaseTick($tickDiff);
	}
	public function onFirstInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
		$flag = false;
		if($item->getId() === Item::LEAD){
			$f = 7.0;
			foreach($player->level->getCollidingEntities(new AxisAlignedBB($this->x - $f, $this->y - $f, $this->z - $f, $this->x + $f, $this->y + $f, $this->z + $f)) as $entity){
				if($entity instanceof Living){
					if($entity->isLeashed() and $entity->getLeashedToEntity() === $player){
						$entity->setLeashedToEntity($this, true);
						$flag = true;
					}
				}
			}
		}
		if(!$flag){
			$this->kill();
			if($player->isCreative()){
				$f = 7.0;
				foreach($player->level->getCollidingEntities(new AxisAlignedBB($this->x - $f, $this->y - $f, $this->z - $f, $this->x + $f, $this->y + $f, $this->z + $f)) as $entity){
					if($entity instanceof Living){
						if($entity->isLeashed() and $entity->getLeashedToEntity() === $this){
							$entity->clearLeashed(true, false);
						}
					}
				}
			}
		}
		return true;
	}
	public function attack(EntityDamageEvent $source) : void{
		parent::attack($source);
		if(!$source->isCancelled()){
			$this->kill();
		}
	}
	public function kill() : void{
		parent::kill();
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LEASHKNOT_BREAK);
	}
	public function getHangingPosition() : Position{
		return new Position($this->getFloorX(), $this->getFloorY(), $this->getFloorZ(), $this->level);
	}
	public static function getKnotFromPosition(Level $level, Vector3 $pos) : ?LeashKnot{
		foreach($level->getCollidingEntities(new AxisAlignedBB($pos->x - 1, $pos->y - 1, $pos->z - 1, $pos->x + 1, $pos->y + 1, $pos->z + 1)) as $entity){
			if($entity instanceof LeashKnot){
				if($entity->getHangingPosition()->equals($pos)){
					return $entity;
				}
			}
		}
		return null;
	}}