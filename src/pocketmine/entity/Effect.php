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
namespace pocketmine\entity;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;use pocketmine\event\entity\EntityDamageByEntityEvent;use pocketmine\event\entity\EntityDamageEvent;use pocketmine\event\entity\EntityRegainHealthEvent;use pocketmine\event\player\PlayerExhaustEvent;use pocketmine\utils\Color;use function constant;use function defined;use function strtoupper;
class Effect{
	public const SPEED = 1;
	public const SLOWNESS = 2;
	public const HASTE = 3;
	public const FATIGUE = 4, MINING_FATIGUE = 4;
	public const STRENGTH = 5;
	public const INSTANT_HEALTH = 6, HEALING = 6;
	public const INSTANT_DAMAGE = 7, HARMING = 7;
	public const JUMP_BOOST = 8, JUMP = 8;
	public const NAUSEA = 9, CONFUSION = 9;
	public const REGENERATION = 10;
	public const RESISTANCE = 11, DAMAGE_RESISTANCE = 11;
	public const FIRE_RESISTANCE = 12;
	public const WATER_BREATHING = 13;
	public const INVISIBILITY = 14;
	public const BLINDNESS = 15;
	public const NIGHT_VISION = 16;
	public const HUNGER = 17;
	public const WEAKNESS = 18;
	public const POISON = 19;
	public const WITHER = 20;
	public const HEALTH_BOOST = 21;
	public const ABSORPTION = 22;
	public const SATURATION = 23;
	public const LEVITATION = 24; 
	public const FATAL_POISON = 25;
	public const CONDUIT_POWER = 26;
	protected static $effects = [];
	public static function init() : void{
		self::registerEffect(new Effect(Effect::SPEED, "%potion.moveSpeed", new Color(0x7c, 0xaf, 0xc6)));
		self::registerEffect(new Effect(Effect::SLOWNESS, "%potion.moveSlowdown", new Color(0x5a, 0x6c, 0x81), true));
		self::registerEffect(new Effect(Effect::HASTE, "%potion.digSpeed", new Color(0xd9, 0xc0, 0x43)));
		self::registerEffect(new Effect(Effect::MINING_FATIGUE, "%potion.digSlowDown", new Color(0x4a, 0x42, 0x17), true));
		self::registerEffect(new Effect(Effect::STRENGTH, "%potion.damageBoost", new Color(0x93, 0x24, 0x23)));
		self::registerEffect(new Effect(Effect::INSTANT_HEALTH, "%potion.heal", new Color(0xf8, 0x24, 0x23), false, 1, false));
		self::registerEffect(new Effect(Effect::INSTANT_DAMAGE, "%potion.harm", new Color(0x43, 0x0a, 0x09), true, 1, false));
		self::registerEffect(new Effect(Effect::JUMP_BOOST, "%potion.jump", new Color(0x22, 0xff, 0x4c)));
		self::registerEffect(new Effect(Effect::NAUSEA, "%potion.confusion", new Color(0x55, 0x1d, 0x4a), true));
		self::registerEffect(new Effect(Effect::REGENERATION, "%potion.regeneration", new Color(0xcd, 0x5c, 0xab)));
		self::registerEffect(new Effect(Effect::RESISTANCE, "%potion.resistance", new Color(0x99, 0x45, 0x3a)));
		self::registerEffect(new Effect(Effect::FIRE_RESISTANCE, "%potion.fireResistance", new Color(0xe4, 0x9a, 0x3a)));
		self::registerEffect(new Effect(Effect::WATER_BREATHING, "%potion.waterBreathing", new Color(0x2e, 0x52, 0x99)));
		self::registerEffect(new Effect(Effect::INVISIBILITY, "%potion.invisibility", new Color(0x7f, 0x83, 0x92)));
		self::registerEffect(new Effect(Effect::BLINDNESS, "%potion.blindness", new Color(0x1f, 0x1f, 0x23), true));
		self::registerEffect(new Effect(Effect::NIGHT_VISION, "%potion.nightVision", new Color(0x1f, 0x1f, 0xa1)));
		self::registerEffect(new Effect(Effect::HUNGER, "%potion.hunger", new Color(0x58, 0x76, 0x53), true));
		self::registerEffect(new Effect(Effect::WEAKNESS, "%potion.weakness", new Color(0x48, 0x4d, 0x48), true));
		self::registerEffect(new Effect(Effect::POISON, "%potion.poison", new Color(0x4e, 0x93, 0x31), true));
		self::registerEffect(new Effect(Effect::WITHER, "%potion.wither", new Color(0x35, 0x2a, 0x27), true));
		self::registerEffect(new Effect(Effect::HEALTH_BOOST, "%potion.healthBoost", new Color(0xf8, 0x7d, 0x23)));
		self::registerEffect(new Effect(Effect::ABSORPTION, "%potion.absorption", new Color(0x25, 0x52, 0xa5)));
		self::registerEffect(new Effect(Effect::SATURATION, "%potion.saturation", new Color(0xf8, 0x24, 0x23), false, 1));
		self::registerEffect(new Effect(Effect::LEVITATION, "%potion.levitation", new Color(0xce, 0xff, 0xff)));
		self::registerEffect(new Effect(Effect::FATAL_POISON, "%potion.poison", new Color(0x4e, 0x93, 0x31), true));
		self::registerEffect(new Effect(Effect::CONDUIT_POWER, "%potion.conduitPower", new Color(0x1d, 0xc2, 0xd1)));
	}
	public static function registerEffect(Effect $effect) : void{
		self::$effects[$effect->getId()] = $effect;
	}
	public static function getEffect(int $id) : ?Effect{
		return self::$effects[$id] ?? null;
	}
	public static function getEffectByName(string $name) : ?Effect{
		$const = self::class . "::" . strtoupper($name);
		if(defined($const)){
			return self::getEffect(constant($const));
		}
		return null;
	}
	protected $id;
	protected $name;
	protected $color;
	protected $bad;
	protected $defaultDuration;
	protected $hasBubbles;
	public function __construct(int $id, string $name, Color $color, bool $isBad = false, int $defaultDuration = 300 * 20, bool $hasBubbles = true){
		$this->id = $id;
		$this->name = $name;
		$this->color = $color;
		$this->bad = $isBad;
		$this->defaultDuration = $defaultDuration;
		$this->hasBubbles = $hasBubbles;
	}
	public function getId() : int{
		return $this->id;
	}
	public function getName() : string{
		return $this->name;
	}
	public function getColor() : Color{
		return clone $this->color;
	}
	public function isBad() : bool{
		return $this->bad;
	}
	public function isInstantEffect() : bool{
		return $this->defaultDuration <= 1;
	}
	public function getDefaultDuration() : int{
		return $this->defaultDuration;
	}
	public function hasBubbles() : bool{
		return $this->hasBubbles;
	}
	public function canTick(EffectInstance $instance) : bool{
		switch($this->id){
			case Effect::POISON:
			case Effect::FATAL_POISON:
				if(($interval = (25 >> $instance->getAmplifier())) > 0){
					return ($instance->getDuration() % $interval) === 0;
				}
				return true;
			case Effect::WITHER:
				if(($interval = (50 >> $instance->getAmplifier())) > 0){
					return ($instance->getDuration() % $interval) === 0;
				}
				return true;
			case Effect::REGENERATION:
				if(($interval = (40 >> $instance->getAmplifier())) > 0){
					return ($instance->getDuration() % $interval) === 0;
				}
				return true;
			case Effect::HUNGER:
				return true;
			case Effect::INSTANT_DAMAGE:
			case Effect::INSTANT_HEALTH:
			case Effect::SATURATION:
				return true;
		}
		return false;
	}
	public function applyEffect(Living $entity, EffectInstance $instance, float $potency = 1.0, ?Entity $source = null, ?Entity $sourceOwner = null) : void{
		switch($this->id){
			case Effect::POISON:
				if($entity->getHealth() <= 1){
					break;
				}
			case Effect::FATAL_POISON:
				$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 1);
				$entity->attack($ev);
				break;
			case Effect::WITHER:
				$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, 1);
				$entity->attack($ev);
				break;
			case Effect::REGENERATION:
				if($entity->getHealth() < $entity->getMaxHealth()){
					$ev = new EntityRegainHealthEvent($entity, 1, EntityRegainHealthEvent::CAUSE_MAGIC);
					$entity->heal($ev);
				}
				break;
			case Effect::HUNGER:
				if($entity instanceof Human){
					$entity->exhaust(0.025 * $instance->getEffectLevel(), PlayerExhaustEvent::CAUSE_POTION);
				}
				break;
			case Effect::INSTANT_HEALTH:
				if($entity->getHealth() < $entity->getMaxHealth()){
					$entity->heal(new EntityRegainHealthEvent($entity, (4 << $instance->getAmplifier()) * $potency, EntityRegainHealthEvent::CAUSE_MAGIC));
				}
				break;
			case Effect::INSTANT_DAMAGE:
				$damage = (4 << $instance->getAmplifier()) * $potency;
				if($source !== null and $sourceOwner !== null){
					$ev = new EntityDamageByChildEntityEvent($sourceOwner, $source, $entity, EntityDamageEvent::CAUSE_MAGIC, $damage);
				}elseif($source !== null){
					$ev = new EntityDamageByEntityEvent($source, $entity, EntityDamageEvent::CAUSE_MAGIC, $damage);
				}else{
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_MAGIC, $damage);
				}
				$entity->attack($ev);
				break;
			case Effect::SATURATION:
				if($entity instanceof Human){
				    $entity->addFood($instance->getEffectLevel());
				    $entity->addSaturation($instance->getEffectLevel() * 2);
				}
				break;
		}
	}
	public function add(Living $entity, EffectInstance $instance) : void{
		switch($this->id){
			case Effect::INVISIBILITY:
				$entity->setInvisible();
				$entity->setNameTagVisible(false);
				break;
			case Effect::SPEED:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() * (1 + 0.2 * $instance->getEffectLevel()));
				break;
			case Effect::SLOWNESS:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() * (1 - 0.15 * $instance->getEffectLevel()), true);
				break;
			case Effect::HEALTH_BOOST:
				$entity->setMaxHealth($entity->getMaxHealth() + 4 * $instance->getEffectLevel());
				break;
			case Effect::ABSORPTION:
				$new = (4 * $instance->getEffectLevel());
				if($new > $entity->getAbsorption()){
					$entity->setAbsorption($new);
				}
				break;
		}
	}
	public function remove(Living $entity, EffectInstance $instance) : void{
		switch($this->id){
			case Effect::INVISIBILITY:
				$entity->setInvisible(false);
				$entity->setNameTagVisible(true);
				break;
			case Effect::SPEED:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() / (1 + 0.2 * $instance->getEffectLevel()));
				break;
			case Effect::SLOWNESS:
				$attr = $entity->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED);
				$attr->setValue($attr->getValue() / (1 - 0.15 * $instance->getEffectLevel()));
				break;
			case Effect::HEALTH_BOOST:
				$entity->setMaxHealth($entity->getMaxHealth() - 4 * $instance->getEffectLevel());
				break;
			case Effect::ABSORPTION:
				$entity->setAbsorption(0);
				break;
		}
	}
	public function __clone(){
		$this->color = clone $this->color;
	}}