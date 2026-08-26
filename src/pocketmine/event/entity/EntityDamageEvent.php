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
namespace pocketmine\event\entity;
use pocketmine\entity\Entity;use pocketmine\event\Cancellable;use function array_sum;
class EntityDamageEvent extends EntityEvent implements Cancellable{
	public const MODIFIER_ARMOR = 1;
	public const MODIFIER_STRENGTH = 2;
	public const MODIFIER_WEAKNESS = 3;
	public const MODIFIER_RESISTANCE = 4;
	public const MODIFIER_ABSORPTION = 5;
	public const MODIFIER_ARMOR_ENCHANTMENTS = 6;
	public const MODIFIER_CRITICAL = 7;
	public const MODIFIER_TOTEM = 8;
	public const MODIFIER_WEAPON_ENCHANTMENTS = 9;
	public const CAUSE_CONTACT = 0;
	public const CAUSE_ENTITY_ATTACK = 1;
	public const CAUSE_PROJECTILE = 2;
	public const CAUSE_SUFFOCATION = 3;
	public const CAUSE_FALL = 4;
	public const CAUSE_FIRE = 5;
	public const CAUSE_FIRE_TICK = 6;
	public const CAUSE_LAVA = 7;
	public const CAUSE_DROWNING = 8;
	public const CAUSE_BLOCK_EXPLOSION = 9;
	public const CAUSE_ENTITY_EXPLOSION = 10;
	public const CAUSE_VOID = 11;
	public const CAUSE_SUICIDE = 12;
	public const CAUSE_MAGIC = 13;
	public const CAUSE_CUSTOM = 14;
	public const CAUSE_STARVATION = 15;
	private $cause;
	private $baseDamage;
	private $originalBase;
	private $modifiers;
	private $originals;
	private $attackCooldown = 10;
	public function __construct(Entity $entity, int $cause, float $damage, array $modifiers = []){
		$this->entity = $entity;
		$this->cause = $cause;
		$this->baseDamage = $this->originalBase = $damage;
		$this->modifiers = $modifiers;
		$this->originals = $this->modifiers;
	}
	public function getCause() : int{
		return $this->cause;
	}
	public function getBaseDamage() : float{
		return $this->baseDamage;
	}
	public function setBaseDamage(float $damage) : void{
		$this->baseDamage = $damage;
	}
	public function getOriginalBaseDamage() : float{
		return $this->originalBase;
	}
	public function getOriginalModifiers() : array{
		return $this->originals;
	}
	public function getOriginalModifier(int $type) : float{
		return $this->originals[$type] ?? 0.0;
	}
	public function getModifiers() : array{
		return $this->modifiers;
	}
	public function getModifier(int $type) : float{
		return $this->modifiers[$type] ?? 0.0;
	}
	public function setModifier(float $damage, int $type) : void{
		$this->modifiers[$type] = $damage;
	}
	public function isApplicable(int $type) : bool{
		return isset($this->modifiers[$type]);
	}
	public function getFinalDamage() : float{
		return $this->baseDamage + array_sum($this->modifiers);
	}
	public function canBeReducedByArmor() : bool{
		switch($this->cause){
			case self::CAUSE_FIRE_TICK:
			case self::CAUSE_SUFFOCATION:
			case self::CAUSE_DROWNING:
			case self::CAUSE_STARVATION:
			case self::CAUSE_FALL:
			case self::CAUSE_VOID:
			case self::CAUSE_MAGIC:
			case self::CAUSE_SUICIDE:
				return false;
		}
		return true;
	}
	public function getAttackCooldown() : int{
		return $this->attackCooldown;
	}
	public function setAttackCooldown(int $attackCooldown) : void{
		$this->attackCooldown = $attackCooldown;
	}}