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
namespace pocketmine\entity\projectile;
use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\entity\EffectInstance;use pocketmine\entity\Living;use pocketmine\entity\Monster;use pocketmine\entity\hostile\Witch;use pocketmine\event\entity\ProjectileHitBlockEvent;use pocketmine\event\entity\ProjectileHitEntityEvent;use pocketmine\event\entity\ProjectileHitEvent;use pocketmine\item\Potion;use pocketmine\network\mcpe\protocol\LevelEventPacket;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\utils\Color;use function round;use function sqrt;
class SplashPotion extends Throwable{
	public const NETWORK_ID = self::SPLASH_POTION;
	protected $gravity = 0.05;
	protected $drag = 0.01;
	protected function initEntity() : void{
		parent::initEntity();
		$this->setPotionId($this->namedtag->getShort("PotionId", 0));
	}
	public function saveNBT() : void{
		parent::saveNBT();
		$this->namedtag->setShort("PotionId", $this->getPotionId());
	}
	public function getResultDamage() : int{
		return -1; 
	}
	protected function onHit(ProjectileHitEvent $event) : void{
		$effects = $this->getPotionEffects();
		$hasEffects = true;
		if(empty($effects)){
			$colors = [
				new Color(0x38, 0x5d, 0xc6) 
			];
			$hasEffects = false;
		}else{
			$colors = [];
			foreach($effects as $effect){
				$level = $effect->getEffectLevel();
				for($j = 0; $j < $level; ++$j){
					$colors[] = $effect->getColor();
				}
			}
		}
		$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_PARTICLE_SPLASH, Color::mix(...$colors)->toARGB());
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_GLASS);
		if($hasEffects){
			if(!$this->willLinger()){
				foreach($this->level->getNearbyEntities($this->boundingBox->expandedCopy(4.125, 2.125, 4.125), $this) as $entity){
					if($entity instanceof Living and $entity->isAlive() and !($this->getOwningEntity() instanceof Witch and $entity instanceof Monster)){
						$distanceSquared = $entity->add(0, $entity->getEyeHeight(), 0)->distanceSquared($this);
						if($distanceSquared > 16){ 
							continue;
						}
						$distanceMultiplier = 1 - (sqrt($distanceSquared) / 4);
						if($event instanceof ProjectileHitEntityEvent and $entity === $event->getEntityHit()){
							$distanceMultiplier = 1.0;
						}
						foreach($this->getPotionEffects() as $effect){
							if(!$effect->getType()->isInstantEffect()){
								$newDuration = (int) round($effect->getDuration() * 0.75 * $distanceMultiplier);
								if($newDuration < 20){
									continue;
								}
								$effect->setDuration($newDuration);
								$entity->addEffect($effect);
							}else{
								$effect->getType()->applyEffect($entity, $effect, $distanceMultiplier, $this, $this->getOwningEntity());
							}
						}
					}
				}
			}else{
			}
		}elseif($event instanceof ProjectileHitBlockEvent and $this->getPotionId() === Potion::WATER){
			$blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());
			if($blockIn->getId() === Block::FIRE){
				$this->level->setBlock($blockIn, BlockFactory::get(Block::AIR));
			}
			foreach($blockIn->getHorizontalSides() as $horizontalSide){
				if($horizontalSide->getId() === Block::FIRE){
					$this->level->setBlock($horizontalSide, BlockFactory::get(Block::AIR));
				}
			}
		}
	}
	public function getPotionId() : int{
		return $this->propertyManager->getShort(self::DATA_POTION_AUX_VALUE) ?? 0;
	}
	public function setPotionId(int $id) : void{
		$this->propertyManager->setShort(self::DATA_POTION_AUX_VALUE, $id);
	}
	public function willLinger() : bool{
		return $this->getDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER);
	}
	public function setLinger(bool $value = true) : void{
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER, $value);
	}
	public function getPotionEffects() : array{
		return Potion::getPotionEffectsById($this->getPotionId());
	}}