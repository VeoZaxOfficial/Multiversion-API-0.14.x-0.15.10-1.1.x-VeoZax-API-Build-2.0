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
use pocketmine\block\Block;use pocketmine\entity\Living;use pocketmine\event\entity\EntityDamageByBlockEvent;use pocketmine\event\entity\EntityDamageByEntityEvent;use pocketmine\event\entity\EntityDamageEvent;use pocketmine\event\entity\EntityDeathEvent;use pocketmine\item\Item;use pocketmine\lang\TextContainer;use pocketmine\lang\TranslationContainer;use pocketmine\Player;
class PlayerDeathEvent extends EntityDeathEvent{
	protected $entity;
	private $deathMessage;
	private $keepInventory = false;
	public function __construct(Player $entity, array $drops, $deathMessage = null){
		parent::__construct($entity, $drops);
		$this->deathMessage = $deathMessage ?? self::deriveMessage($entity->getDisplayName(), $entity->getLastDamageCause());
	}
	public function getEntity(){
		return $this->entity;
	}
	public function getPlayer() : Player{
		return $this->entity;
	}
	public function getDeathMessage(){
		return $this->deathMessage;
	}
	public function setDeathMessage($deathMessage) : void{
		$this->deathMessage = $deathMessage;
	}
	public function getKeepInventory() : bool{
		return $this->keepInventory;
	}
	public function setKeepInventory(bool $keepInventory) : void{
		$this->keepInventory = $keepInventory;
	}
	public static function deriveMessage(string $name, ?EntityDamageEvent $deathCause) : TranslationContainer{
		$message = "death.attack.generic";
		$params = [$name];
		switch($deathCause === null ? EntityDamageEvent::CAUSE_CUSTOM : $deathCause->getCause()){
			case EntityDamageEvent::CAUSE_ENTITY_ATTACK:
				if($deathCause instanceof EntityDamageByEntityEvent){
					$e = $deathCause->getDamager();
					if($e instanceof Player){
						$message = "death.attack.player";
						$params[] = $e->getDisplayName();
						break;
					}elseif($e instanceof Living){
						$message = "death.attack.mob";
						$params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
						break;
					}else{
						$params[] = "Unknown";
					}
				}
				break;
			case EntityDamageEvent::CAUSE_PROJECTILE:
				if($deathCause instanceof EntityDamageByEntityEvent){
					$e = $deathCause->getDamager();
					if($e instanceof Player){
						$message = "death.attack.arrow";
						$params[] = $e->getDisplayName();
					}elseif($e instanceof Living){
						$message = "death.attack.arrow";
						$params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
						break;
					}else{
						$params[] = "Unknown";
					}
				}
				break;
			case EntityDamageEvent::CAUSE_SUICIDE:
				$message = "death.attack.generic";
				break;
			case EntityDamageEvent::CAUSE_VOID:
				$message = "death.attack.outOfWorld";
				break;
			case EntityDamageEvent::CAUSE_FALL:
				if($deathCause instanceof EntityDamageEvent){
					if($deathCause->getFinalDamage() > 2){
						$message = "death.fell.accident.generic";
						break;
					}
				}
				$message = "death.attack.fall";
				break;
			case EntityDamageEvent::CAUSE_SUFFOCATION:
				$message = "death.attack.inWall";
				break;
			case EntityDamageEvent::CAUSE_LAVA:
				$message = "death.attack.lava";
				break;
			case EntityDamageEvent::CAUSE_FIRE:
				$message = "death.attack.onFire";
				break;
			case EntityDamageEvent::CAUSE_FIRE_TICK:
				$message = "death.attack.inFire";
				break;
			case EntityDamageEvent::CAUSE_DROWNING:
				$message = "death.attack.drown";
				break;
			case EntityDamageEvent::CAUSE_CONTACT:
				if($deathCause instanceof EntityDamageByBlockEvent){
					if($deathCause->getDamager()->getId() === Block::CACTUS){
						$message = "death.attack.cactus";
					}
				}
				break;
			case EntityDamageEvent::CAUSE_BLOCK_EXPLOSION:
			case EntityDamageEvent::CAUSE_ENTITY_EXPLOSION:
				if($deathCause instanceof EntityDamageByEntityEvent){
					$e = $deathCause->getDamager();
					if($e instanceof Player){
						$message = "death.attack.explosion.player";
						$params[] = $e->getDisplayName();
					}elseif($e instanceof Living){
						$message = "death.attack.explosion.player";
						$params[] = $e->getNameTag() !== "" ? $e->getNameTag() : $e->getName();
						break;
					}
				}else{
					$message = "death.attack.explosion";
				}
				break;
			case EntityDamageEvent::CAUSE_MAGIC:
				$message = "death.attack.magic";
				break;
			case EntityDamageEvent::CAUSE_CUSTOM:
				break;
			default:
				break;
		}
		return new TranslationContainer($message, $params);
	}}