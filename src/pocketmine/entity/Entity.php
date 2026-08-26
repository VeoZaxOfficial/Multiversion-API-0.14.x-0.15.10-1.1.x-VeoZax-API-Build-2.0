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
use ErrorException;use InvalidArgumentException;use InvalidStateException;use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\block\Fire;use pocketmine\block\Lava;use pocketmine\block\Portal;use pocketmine\block\Water;use pocketmine\entity\hostile\Blaze;use pocketmine\entity\hostile\CaveSpider;use pocketmine\entity\hostile\Creeper;use pocketmine\entity\hostile\Enderman;use pocketmine\entity\hostile\Husk;use pocketmine\entity\hostile\MagmaCube;use pocketmine\entity\hostile\Skeleton;use pocketmine\entity\hostile\Slime;use pocketmine\entity\hostile\Spider;use pocketmine\entity\hostile\Stray;use pocketmine\entity\hostile\Witch;use pocketmine\entity\hostile\Zombie;use pocketmine\entity\object\EnderCrystal;use pocketmine\entity\object\ExperienceOrb;use pocketmine\entity\object\FallingBlock;use pocketmine\entity\object\FireworksRocket;use pocketmine\entity\object\ItemEntity;use pocketmine\entity\object\LeashKnot;use pocketmine\entity\object\Lightning;use pocketmine\entity\object\Painting;use pocketmine\entity\object\PaintingMotive;use pocketmine\entity\object\PrimedTNT;use pocketmine\entity\passive\Cat;use pocketmine\entity\passive\Chicken;use pocketmine\entity\passive\Cow;use pocketmine\entity\passive\Horse;use pocketmine\entity\passive\Mooshroom;use pocketmine\entity\passive\Ocelot;use pocketmine\entity\passive\Pig;use pocketmine\entity\passive\Sheep;use pocketmine\entity\passive\Squid;use pocketmine\entity\passive\Villager;use pocketmine\entity\passive\Wolf;use pocketmine\entity\projectile\Arrow;use pocketmine\entity\projectile\Egg;use pocketmine\entity\projectile\EnderPearl;use pocketmine\entity\projectile\ExperienceBottle;use pocketmine\entity\projectile\FishingHook;use pocketmine\entity\projectile\SmallFireball;use pocketmine\entity\projectile\Snowball;use pocketmine\entity\projectile\SplashPotion;use pocketmine\entity\vehicle\Boat;use pocketmine\entity\vehicle\Minecart;use pocketmine\entity\data\KnockBackData;use pocketmine\event\entity\EntityDamageEvent;use pocketmine\event\entity\EntityDespawnEvent;use pocketmine\event\entity\EntityLevelChangeEvent;use pocketmine\event\entity\EntityMotionEvent;use pocketmine\event\entity\EntityRegainHealthEvent;use pocketmine\event\entity\EntitySpawnEvent;use pocketmine\event\entity\EntityTeleportEvent;use pocketmine\item\Item;use pocketmine\level\biome\Biome;use pocketmine\level\format\Chunk;use pocketmine\level\Level;use pocketmine\level\Location;use pocketmine\level\Position;use pocketmine\level\weather\Weather;use pocketmine\math\AxisAlignedBB;use pocketmine\math\Vector2;use pocketmine\math\Vector3;use pocketmine\metadata\Metadatable;use pocketmine\metadata\MetadataValue;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\DoubleTag;use pocketmine\nbt\tag\FloatTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\NamedTag;use pocketmine\nbt\tag\StringTag;use pocketmine\network\mcpe\protocol\ActorEventPacket;use pocketmine\network\mcpe\protocol\AddActorPacket;use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;use pocketmine\network\mcpe\protocol\RemoveActorPacket;use pocketmine\network\mcpe\protocol\SetActorDataPacket;use pocketmine\network\mcpe\protocol\SetActorLinkPacket;use pocketmine\network\mcpe\protocol\SetActorMotionPacket;use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;use pocketmine\network\mcpe\protocol\types\EntityLink;use pocketmine\Player;use pocketmine\plugin\Plugin;use pocketmine\Server;use pocketmine\timings\Timings;use pocketmine\timings\TimingsHandler;use pocketmine\utils\Random;use ReflectionClass;use function abs;use function assert;use function cos;use function count;use function current;use function deg2rad;use function floor;use function get_class;use function in_array;use function is_a;use function is_array;use function is_infinite;use function is_nan;use function lcg_value;use function reset;use function sin;use const M_PI_2;
abstract class Entity extends Location implements Metadatable, EntityIds, EntityMetadataProperties, EntityMetadataFlags{
	public const MOTION_THRESHOLD = 0.00001;
	protected const STEP_CLIP_MULTIPLIER = 0.4;
	public const NETWORK_ID = -1;
	public const DATA_TYPE_BYTE = 0;
	public const DATA_TYPE_SHORT = 1;
	public const DATA_TYPE_INT = 2;
	public const DATA_TYPE_FLOAT = 3;
	public const DATA_TYPE_STRING = 4;
	public const DATA_TYPE_SLOT = 5;
	public const DATA_TYPE_POS = 6;
	public const DATA_TYPE_LONG = 7;
	public const DATA_TYPE_VECTOR3F = 8;
	public const DATA_PLAYER_FLAG_SLEEP = 1;
	public const DATA_PLAYER_FLAG_DEAD = 2; 
	public const SPAWN_PLACEMENT_TYPE = SpawnPlacementTypes::PLACEMENT_TYPE_ON_GROUND;
	public static $entityCount = 1;
	private static $knownEntities = [];
	private static $saveNames = [];
	public static function init() : void{
		Entity::registerEntity(Arrow::class, false, ['Arrow', 'minecraft:arrow']);
		Entity::registerEntity(Egg::class, false, ['Egg', 'minecraft:egg']);
		Entity::registerEntity(Lightning::class, false, ['Lightning', 'minecraft:lightning']);
		Entity::registerEntity(EnderCrystal::class, false, ['EnderCrystal', 'minecraft:ender_crystal']);
		Entity::registerEntity(EnderPearl::class, false, ['ThrownEnderpearl', 'minecraft:ender_pearl']);
		Entity::registerEntity(ExperienceBottle::class, false, ['ThrownExpBottle', 'minecraft:xp_bottle']);
		Entity::registerEntity(ExperienceOrb::class, false, ['XPOrb', 'minecraft:xp_orb']);
		Entity::registerEntity(FallingBlock::class, false, ['FallingSand', 'minecraft:falling_block']);
		Entity::registerEntity(ItemEntity::class, false, ['Item', 'minecraft:item']);
		Entity::registerEntity(Painting::class, false, ['Painting', 'minecraft:painting']);
		Entity::registerEntity(PrimedTNT::class, false, ['PrimedTnt', 'PrimedTNT', 'minecraft:tnt']);
		Entity::registerEntity(Snowball::class, false, ['Snowball', 'minecraft:snowball']);
		Entity::registerEntity(SplashPotion::class, false, ['ThrownPotion', 'minecraft:potion', 'thrownpotion']);
		Entity::registerEntity(Slime::class, false, ['Slime', 'minecraft:slime']);
		Entity::registerEntity(MagmaCube::class, false, ['MagmaCube', 'minecraft:magma_cube']);
		Entity::registerEntity(Boat::class, false, ['Boat', 'minecraft:boat']);
		Entity::registerEntity(Minecart::class, false, ['Minecart', 'minecraft:minecart']);
		Entity::registerEntity(Horse::class, false, ['Horse', 'minecraft:horse']);
		Entity::registerEntity(FireworksRocket::class, false, ['FireworksRocket', 'minecraft:fireworks_rocket']);
		Entity::registerEntity(Blaze::class, false, ['Blaze', 'minecraft:blaze']);
		Entity::registerEntity(SmallFireball::class, false, ['SmallFireball', 'minecraft:small_fireball']);
		Entity::registerEntity(Squid::class, false, ['Squid', 'minecraft:squid']);
		Entity::registerEntity(Villager::class, false, ['Villager', 'minecraft:villager']);
		Entity::registerEntity(Wolf::class, false, ['Wolf', 'minecraft:wolf']);
		Entity::registerEntity(Zombie::class, false, ['Zombie', 'minecraft:zombie']);
		Entity::registerEntity(Cow::class, false, ['Cow', 'minecraft:cow']);
		Entity::registerEntity(Sheep::class, false, ['Sheep', 'minecraft:sheep']);
		Entity::registerEntity(Mooshroom::class, false, ['Mooshroom', 'minecraft:mooshroom']);
		Entity::registerEntity(Ocelot::class, false, ['Ocelot', 'minecraft:ocelot']);
		Entity::registerEntity(Pig::class, false, ['Pig', 'minecraft:pig']);
		Entity::registerEntity(Cat::class, false, ['Cat', 'minecraft:cat']);
		Entity::registerEntity(Skeleton::class, false, ['Skeleton', 'minecraft:skeleton']);
		Entity::registerEntity(Stray::class, false, ['Stray', 'minecraft:stray']);
		Entity::registerEntity(Witch::class, false, ['Witch', 'minecraft:witch']);
		Entity::registerEntity(Husk::class, false, ['Husk', 'minecraft:husk']);
		Entity::registerEntity(Chicken::class, false, ['Chicken', 'minecraft:chicken']);
		Entity::registerEntity(Spider::class, false, ['Spider', 'minecraft:spider']);
		Entity::registerEntity(CaveSpider::class, false, ['CaveSpider', 'minecraft:cave_spider']);
		Entity::registerEntity(Creeper::class, false, ['Creeper', 'minecraft:creeper']);
		Entity::registerEntity(Enderman::class, false, ['Enderman', 'minecraft:enderman']);
		Entity::registerEntity(FishingHook::class, false, ['FishingHook', 'minecraft:fishing_hook']);
		Entity::registerEntity(LeashKnot::class, false, ['LeashKnot', 'minecraft:leash_knot']);
		Entity::registerEntity(Human::class, true);
		Attribute::init();
		Effect::init();
		PaintingMotive::init();
		self::initDefaultKnockBackData();
	}
	public static function createEntity($type, Level $level, CompoundTag $nbt, ...$args) : ?Entity{
		if(isset(self::$knownEntities[$type])){
			$class = self::$knownEntities[$type];
			return new $class($level, $nbt, ...$args);
		}
		return null;
	}
	public static function registerEntity(string $className, bool $force = false, array $saveNames = []) : bool{
		$class = new ReflectionClass($className);
		if(is_a($className, Entity::class, true) and !$class->isAbstract()){
			if($className::NETWORK_ID !== -1){
				self::$knownEntities[$className::NETWORK_ID] = $className;
			}elseif(!$force){
				return false;
			}
			$shortName = $class->getShortName();
			if(!in_array($shortName, $saveNames, true)){
				$saveNames[] = $shortName;
			}
			foreach($saveNames as $name){
				self::$knownEntities[$name] = $className;
			}
			self::$saveNames[$className] = $saveNames;
			return true;
		}
		return false;
	}
	public static function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0) : CompoundTag{
		return new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $pos->x),
				new DoubleTag("", $pos->y),
				new DoubleTag("", $pos->z)
			]),
			new ListTag("Motion", [
				new DoubleTag("", $motion ? $motion->x : 0.0),
				new DoubleTag("", $motion ? $motion->y : 0.0),
				new DoubleTag("", $motion ? $motion->z : 0.0)
			]),
			new ListTag("Rotation", [
				new FloatTag("", $yaw),
				new FloatTag("", $pitch)
			])
		]);
	}
	protected $hasSpawned = [];
	protected $id;
	protected $propertyManager;
	public $chunk;
	protected $lastDamageCause = null;
	protected $blocksAround = null;
	public $lastX = null;
	public $lastY = null;
	public $lastZ = null;
	protected $motion;
	protected $lastMotion;
	protected $forceMovementUpdate = false;
	public $temporalVector;
	public $lastYaw;
	public $lastPitch;
	public $boundingBox;
	public $onGround;
	public $eyeHeight = null;
	public $height;
	public $width;
	protected $baseOffset = 0.0;
	private $health = 20.0;
	private $maxHealth = 20;
	protected $ySize = 0.0;
	protected $stepHeight = 0.0;
	public $keepMovement = false;
	public $fallDistance = 0.0;
	public $ticksLived = 0;
	public $lastUpdate;
	protected $fireTicks = 0;
	public $namedtag;
	public $canCollide = true;
	protected $isStatic = false;
	private $savedWithChunk = true;
	public $isCollided = false;
	public $isCollidedHorizontally = false;
	public $isCollidedVertically = false;
	public $noDamageTicks;
	protected $justCreated = true;
	private $invulnerable;
	protected $attributeMap;
	protected $gravity;
	protected $drag;
	protected $server;
	protected $closed = false;
	private $needsDespawn = false;
	protected $timings;
	protected $constructed = false;
	protected $ridingEid = null;
	protected $riddenByEid = null;
	protected $entityRiderPitchDelta = 0;
	protected $entityRiderYawDelta = 0;
	public $passengers = [];
	public $headYaw;
	public $lastHeadYaw = 0;
	private $closeInFlight = false;
	protected $clientMoveTicks = 0;
	protected $clientPos;
	protected $clientYaw = 0;
	protected $clientPitch = 0;
	public $random;
    private static KnockBackData $defaultKnockBackData;
	public function __construct(Level $level, CompoundTag $nbt){
		$this->random = new Random(intval(microtime(true) * 1000));
		$this->constructed = true;
		$this->timings = Timings::getEntityTimings($this);
		$this->temporalVector = new Vector3();
		if($this->eyeHeight === null){
			$this->eyeHeight = $this->height * 0.85;
		}
		$this->id = Entity::$entityCount++;
		$this->namedtag = $nbt;
		$this->server = $level->getServer();
		$pos = $this->namedtag->getListTag("Pos")->getAllValues();
		$rotation = $this->namedtag->getListTag("Rotation")->getAllValues();
		parent::__construct($pos[0], $pos[1], $pos[2], $rotation[0], $rotation[1], $level);
		assert(!is_nan($this->x) and !is_infinite($this->x) and !is_nan($this->y) and !is_infinite($this->y) and !is_nan($this->z) and !is_infinite($this->z));
		$this->boundingBox = new AxisAlignedBB(0, 0, 0, 0, 0, 0);
		$this->recalculateBoundingBox();
		$this->chunk = $this->level->getChunkAtPosition($this, false);
		if($this->chunk === null){
			throw new InvalidStateException("Cannot create entities in unloaded chunks");
		}
		$this->motion = new Vector3(0, 0, 0);
		if($this->namedtag->hasTag("Motion", ListTag::class)){
			$motion = $this->namedtag->getListTag("Motion")->getAllValues();
			$this->setMotion($this->temporalVector->setComponents(...$motion));
		}
		$this->resetLastMovements();
		$this->fallDistance = $this->namedtag->getFloat("FallDistance", 0.0);
		$this->propertyManager = new DataPropertyManager();
		$this->propertyManager->setLong(self::DATA_FLAGS, 0);
		$this->propertyManager->setByte(self::DATA_COLOUR, 0);
		$this->propertyManager->setShort(self::DATA_MAX_AIR, 400);
		$this->propertyManager->setString(self::DATA_NAMETAG, "");
		$this->propertyManager->setLong(self::DATA_LEAD_HOLDER_EID, -1);
		$this->propertyManager->setFloat(self::DATA_SCALE, 1);
		$this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_WIDTH, $this->width);
		$this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, $this->height);
	    $this->fireTicks = $this->namedtag->getTagValue("Fire", NamedTag::class, 0);
		if($this->isOnFire()){
			$this->setGenericFlag(self::DATA_FLAG_ONFIRE);
		}
		$this->propertyManager->setShort(self::DATA_AIR, $this->namedtag->getShort("Air", 300));
		$this->onGround = $this->namedtag->getByte("OnGround", 0) !== 0;
		$this->invulnerable = $this->namedtag->getByte("Invulnerable", 0) !== 0;
		$this->setScale($this->namedtag->getFloat("Scale", 1));
		$this->attributeMap = new AttributeMap();
		$this->addAttributes();
		$this->setGenericFlag(self::DATA_FLAG_AFFECTED_BY_GRAVITY, true);
		$this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);
		$this->initEntity();
		$this->propertyManager->clearDirtyProperties(); 
		$this->chunk->addEntity($this);
		$this->level->addEntity($this);
		$this->lastUpdate = $this->server->getTick();
		(new EntitySpawnEvent($this))->call();
		$this->scheduleUpdate();
	}
	public function getNameTag() : string{
		return $this->propertyManager->getString(self::DATA_NAMETAG);
	}
	public function isNameTagVisible() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_CAN_SHOW_NAMETAG);
	}
	public function isNameTagAlwaysVisible() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_ALWAYS_SHOW_NAMETAG);
	}
	public function setNameTag(string $name) : void{
		$this->propertyManager->setString(self::DATA_NAMETAG, $name);
	}
	public function setNameTagVisible(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_CAN_SHOW_NAMETAG, $value);
	}
	public function setNameTagAlwaysVisible(bool $value = true) : void{
	    $this->setGenericFlag(self::DATA_FLAG_ALWAYS_SHOW_NAMETAG, $value);
	}
	public function getScoreTag() : ?string{
		return $this->propertyManager->getString(self::DATA_SCORE_TAG);
	}
	public function setScoreTag(string $score) : void{
		$this->propertyManager->setString(self::DATA_SCORE_TAG, $score);
	}
	public function getScale() : float{
		return $this->propertyManager->getFloat(self::DATA_SCALE);
	}
	public function setScale(float $value) : void{
		if($value <= 0){
			throw new InvalidArgumentException("Scale must be greater than 0");
		}
		$multiplier = $value / $this->getScale();
		$this->width *= $multiplier;
		$this->height *= $multiplier;
		$this->eyeHeight *= $multiplier;
		$this->recalculateBoundingBox();
		$this->propertyManager->setFloat(self::DATA_SCALE, $value);
	}
	public function isInLove() : bool{
		return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE);
	}
	public function setInLove(bool $value) : void{
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE, $value);
	}
	public function isRiding() : bool{
		return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_RIDING);
	}
	public function setRiding(bool $value) : void{
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_RIDING, $value);
	}
	public function getRidingEntity() : ?Entity{
		return $this->ridingEid !== null ? $this->server->findEntity($this->ridingEid) : null;
	}
	public function setRidingEntity(?Entity $ridingEntity = null) : void{
		if($ridingEntity instanceof Entity){
			$this->ridingEid = $ridingEntity->getId();
		}else{
			$this->ridingEid = null;
		}
	}
	public function getRiddenByEntity() : ?Entity{
		return $this->riddenByEid !== null ? $this->server->findEntity($this->riddenByEid) : null;
	}
	public function setRiddenByEntity(?Entity $riddenByEntity = null) : void{
		if($riddenByEntity instanceof Entity){
			$this->riddenByEid = $riddenByEntity->getId();
		}else{
			$this->riddenByEid = null;
		}
	}
	public function isBaby() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_BABY);
	}
	public function setBaby(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_BABY, $value);
		$this->setScale($value ? 0.5 : 1.0);
	}
	public function getBoundingBox() : AxisAlignedBB{
		return $this->boundingBox;
	}
	protected function recalculateBoundingBox() : void{
		$halfWidth = $this->width / 2;
		$this->boundingBox->setBounds(
			$this->x - $halfWidth,
			$this->y + $this->ySize,
			$this->z - $halfWidth,
			$this->x + $halfWidth,
			$this->y + $this->height + $this->ySize,
			$this->z + $halfWidth
		);
	}
	public function updateBoundingBox(float $height, float $width) : void{
		$this->height = $height;
		$this->width = $width;
		$this->recalculateBoundingBox();
		$this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_WIDTH, $width);
		$this->propertyManager->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, $height);
	}
	public function isSneaking() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_SNEAKING);
	}
	public function setSneaking(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_SNEAKING, $value);
	}
	public function isSprinting() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_SPRINTING);
	}
	public function setSprinting(bool $value = true) : void{
		if($value !== $this->isSprinting()){
			$this->setGenericFlag(self::DATA_FLAG_SPRINTING, $value);
			$attr = $this->attributeMap->getAttribute(Attribute::MOVEMENT_SPEED);
			$attr->setValue($value ? ($attr->getValue() * 1.3) : ($attr->getValue() / 1.3), false, true);
		}
	}
	public function isSwimming() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_SWIMMING);
	}
	public function setSwimming(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_SWIMMING, $value);
	}
	public function isSwimmer() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_SWIMMER);
	}
	public function setSwimmer(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_SWIMMER, $value);
	}
	public function isImmobile() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_IMMOBILE);
	}
	public function setImmobile(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_IMMOBILE, $value);
	}
	public function isInvisible() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_INVISIBLE);
	}
	public function setInvisible(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_INVISIBLE, $value);
	}
	public function isGliding() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_GLIDING);
	}
	public function setGliding(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_GLIDING, $value);
	}
	public function canClimb() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_CAN_CLIMB);
	}
	public function setCanClimb(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_CAN_CLIMB, $value);
	}
	public function canFly() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_CAN_FLY);
	}
	public function setCanFly(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_CAN_FLY, $value);
	}
	public function canClimbWalls() : bool{
		return $this->getGenericFlag(self::DATA_FLAG_WALLCLIMBING);
	}
	public function setCanClimbWalls(bool $value = true) : void{
		$this->setGenericFlag(self::DATA_FLAG_WALLCLIMBING, $value);
	}
	public function getOwningEntityId() : ?int{
		return $this->propertyManager->getLong(self::DATA_OWNER_EID);
	}
	public function getOwningEntity() : ?Entity{
		$eid = $this->getOwningEntityId();
		if($eid !== null){
			return $this->server->findEntity($eid);
		}
		return null;
	}
	public function setOwningEntity(?Entity $owner) : void{
		if($owner === null){
			$this->propertyManager->removeProperty(self::DATA_OWNER_EID);
		}elseif($owner->closed){
			throw new InvalidArgumentException("Supplied owning entity is garbage and cannot be used");
		}else{
			$this->propertyManager->setLong(self::DATA_OWNER_EID, $owner->getId());
		}
	}
	public function getTargetEntityId() : ?int{
		return $this->propertyManager->getLong(self::DATA_TARGET_EID);
	}
	public function getTargetEntity() : ?Entity{
		$eid = $this->getTargetEntityId();
		if($eid !== null){
			return $this->server->findEntity($eid);
		}
		return null;
	}
	public function setTargetEntity(?Entity $target) : void{
		if($target === null){
			$this->propertyManager->removeProperty(self::DATA_TARGET_EID);
		}elseif($target->closed){
			throw new InvalidArgumentException("Supplied target entity is garbage and cannot be used");
		}else{
			$this->propertyManager->setLong(self::DATA_TARGET_EID, $target->getId());
		}
	}
	public function canSaveWithChunk() : bool{
		return $this->savedWithChunk;
	}
	public function setCanSaveWithChunk(bool $value) : void{
		$this->savedWithChunk = $value;
	}
	public function getSaveId() : string{
		if(!isset(self::$saveNames[static::class])){
			throw new InvalidStateException("Entity is not registered");
		}
		reset(self::$saveNames[static::class]);
		return current(self::$saveNames[static::class]);
	}
	public function saveNBT() : void{
		if(!($this instanceof Player)){
			$this->namedtag->setString("id", $this->getSaveId(), true);
			if($this->getNameTag() !== ""){
				$this->namedtag->setString("CustomName", $this->getNameTag());
				$this->namedtag->setByte("CustomNameVisible", $this->isNameTagVisible() ? 1 : 0);
				$this->namedtag->setByte("CustomNameAlwaysVisible", $this->isNameTagAlwaysVisible() ? 1 : 0);
			}else{
				$this->namedtag->removeTag("CustomName", "CustomNameVisible", "CustomNameAlwaysVisible");
			}
		}
		$this->namedtag->setTag(new ListTag("Pos", [
			new DoubleTag("", $this->x),
			new DoubleTag("", $this->y),
			new DoubleTag("", $this->z)
		]));
		$this->namedtag->setTag(new ListTag("Motion", [
			new DoubleTag("", $this->motion->x),
			new DoubleTag("", $this->motion->y),
			new DoubleTag("", $this->motion->z)
		]));
		$this->namedtag->setTag(new ListTag("Rotation", [
			new FloatTag("", $this->yaw),
			new FloatTag("", $this->pitch)
		]));
		$this->namedtag->setFloat("FallDistance", $this->fallDistance);
		$this->namedtag->setShort("Fire", $this->fireTicks, true);
		$this->namedtag->setShort("Air", $this->propertyManager->getShort(self::DATA_AIR));
		$this->namedtag->setByte("OnGround", $this->onGround ? 1 : 0);
		$this->namedtag->setByte("Invulnerable", $this->invulnerable ? 1 : 0);
		$this->namedtag->setFloat("Scale", $this->getScale());
	}
	protected function initEntity() : void{
		assert($this->namedtag instanceof CompoundTag);
		if($this->namedtag->hasTag("CustomName", StringTag::class)){
			$this->setNameTag($this->namedtag->getString("CustomName"));
			if($this->namedtag->hasTag("CustomNameVisible", StringTag::class)){
				$this->setNameTagVisible($this->namedtag->getString("CustomNameVisible") !== "");
				$this->namedtag->removeTag("CustomNameVisible");
			}else{
				$this->setNameTagVisible($this->namedtag->getByte("CustomNameVisible", 1) !== 0);
			}
			if($this->namedtag->hasTag("CustomNameAlwaysVisible", StringTag::class)){
				$this->setNameTagAlwaysVisible($this->namedtag->getString("CustomNameAlwaysVisible") !== "");
				$this->namedtag->removeTag("CustomNameAlwaysVisible");
			}else{
				$this->setNameTagAlwaysVisible($this->namedtag->getByte("CustomNameAlwaysVisible", 1) !== 0);
			}
		}
	}
	protected function addAttributes() : void{
	}
	public function attack(EntityDamageEvent $source) : void{
		$source->call();
		if($source->isCancelled()){
			return;
		}
		$this->setLastDamageCause($source);
		$this->setHealth($this->getHealth() - $source->getFinalDamage());
	}
	public function heal(EntityRegainHealthEvent $source) : void{
		$source->call();
		if($source->isCancelled()){
			return;
		}
		$this->setHealth($this->getHealth() + $source->getAmount());
	}
	public function kill() : void{
		$this->health = 0;
		$this->dismountEntity(true);
		$this->scheduleUpdate();
	}
	protected function onDeathUpdate(int $tickDiff) : bool{
		return true;
	}
	public function isAlive() : bool{
		return $this->health > 0;
	}
	public function getHealth() : float{
		return $this->health;
	}
	public function setHealth(float $amount) : void{
		if($amount == $this->health){
			return;
		}
		if($amount <= 0){
			if($this->isAlive()){
				$this->health = 0;
				$this->kill();
			}
		}elseif($amount <= $this->getMaxHealth() or $amount < $this->health){
			$this->health = $amount;
		}else{
			$this->health = $this->getMaxHealth();
		}
	}
	public function getMaxHealth() : int{
		return $this->maxHealth;
	}
	public function setMaxHealth(int $amount) : void{
		$this->maxHealth = $amount;
	}
	public function setLastDamageCause(EntityDamageEvent $type) : void{
		$this->lastDamageCause = $type;
	}
	public function getLastDamageCause() : ?EntityDamageEvent{
		return $this->lastDamageCause;
	}
	public function getAttributeMap() : AttributeMap{
		return $this->attributeMap;
	}
	public function getDataPropertyManager() : DataPropertyManager{
		return $this->propertyManager;
	}
	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->getRidingEntity() === null and $this->ridingEid !== null){
			$this->ridingEid = null;
			$this->setRiding(false);
		}
		if($this->getRiddenByEntity() === null and $this->riddenByEid !== null){
			$this->riddenByEid = null;
			unset($this->passengers[array_search($this->riddenByEid, $this->passengers, true)]);
			$this->setGenericFlag(Entity::DATA_FLAG_WASD_CONTROLLED, false);
		}
		$this->justCreated = false;
		$changedProperties = $this->propertyManager->getDirty();
		if(!empty($changedProperties)){
			$this->sendData($this->hasSpawned, $changedProperties);
			$this->propertyManager->clearDirtyProperties();
		}
		$hasUpdate = false;
		$this->checkBlockCollision();
		if($this->y <= -16 and $this->isAlive()){
			$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_VOID, 10);
			$this->attack($ev);
			$hasUpdate = true;
		}
		if($this->isOnFire() and in_array($this->level->getWeather()->getWeather(), [Weather::RAIN, Weather::RAINY_THUNDER, Weather::THUNDER]) and $this->level->getBiomeId($this->getFloorX(), $this->getFloorZ()) !== Biome::DESERT){
			$this->extinguish();
		}
		if($this->isOnFire() and $this->doOnFireTick($tickDiff)){
			$hasUpdate = true;
		}
		if($this->noDamageTicks > 0){
			$this->noDamageTicks -= $tickDiff;
			if($this->noDamageTicks < 0){
				$this->noDamageTicks = 0;
			}
		}
		if($this->isGliding()){
			$this->resetFallDistance();
		}
		$this->ticksLived += $tickDiff;
		return $hasUpdate;
	}
	public function isOnFire() : bool{
		return $this->fireTicks > 0;
	}
	public function setOnFire(int $seconds) : void{
		$ticks = $seconds * 20;
		if($ticks > $this->getFireTicks()){
			$this->setFireTicks($ticks);
		}
		$this->setGenericFlag(self::DATA_FLAG_ONFIRE, $this->isOnFire());
	}
	public function getFireTicks() : int{
		return $this->fireTicks;
	}
	public function setFireTicks(int $fireTicks) : void{
		if($fireTicks < 0 or $fireTicks > 0x7fff){
			throw new InvalidArgumentException("Fire ticks must be in range 0 ... " . 0x7fff . ", got $fireTicks");
		}
		$this->fireTicks = $fireTicks;
	}
	public function extinguish() : void{
		$this->fireTicks = 0;
		$this->setGenericFlag(self::DATA_FLAG_ONFIRE, false);
	}
	public function isFireProof() : bool{
		return false;
	}
	protected function doOnFireTick(int $tickDiff = 1) : bool{
		if($this->isFireProof() and $this->fireTicks > 1){
			$this->fireTicks = 1;
		}else{
			$this->fireTicks -= $tickDiff;
		}
		if(($this->fireTicks % 20 === 0) or $tickDiff > 20){
			$this->dealFireDamage();
		}
		if(!$this->isOnFire()){
			$this->extinguish();
		}else{
			return true;
		}
		return false;
	}
	protected function dealFireDamage() : void{
		$ev = new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FIRE_TICK, 1);
		$this->attack($ev);
	}
	public function canCollideWith(Entity $entity) : bool{
		return !$this->justCreated and $entity !== $this;
	}
	public function canBeCollidedWith() : bool{
		return $this->isAlive();
	}
	protected function updateMovement(bool $teleport = false) : void{
		$diffPosition = ($this->x - $this->lastX) ** 2 + ($this->y - $this->lastY) ** 2 + ($this->z - $this->lastZ) ** 2;
		$diffRotation = ($this->yaw - $this->lastYaw) ** 2 + ($this->pitch - $this->lastPitch) ** 2;
		if($this->headYaw !== null){
			$diffRotation += (($this->headYaw ?? 0.0) - $this->lastHeadYaw) ** 2;
		}
		$diffMotion = $this->motion->subtract($this->lastMotion)->lengthSquared();
		$still = $this->motion->lengthSquared() == 0.0;
		$wasStill = $this->lastMotion->lengthSquared() == 0.0;
		if($wasStill !== $still and !($this instanceof Mob)){
			$this->setImmobile($still);
		}
		if($teleport or $diffPosition > 0.04 or $diffRotation > 2.25 or (!$wasStill and $still)){
			$this->lastX = $this->x;
			$this->lastY = $this->y;
			$this->lastZ = $this->z;
			$this->lastYaw = $this->yaw;
			$this->lastPitch = $this->pitch;
			$this->lastHeadYaw = $this->headYaw ?? 0.0;
			$this->broadcastMovement($teleport);
		}
		if($diffMotion > 0.0025 or $wasStill !== $still){ 
			$this->lastMotion = clone $this->motion;
			$this->broadcastMotion();
		}
	}
	public function getOffsetPosition(Vector3 $vector3) : Vector3{
		return new Vector3($vector3->x, $vector3->y + $this->baseOffset, $vector3->z);
	}
	protected function broadcastMovement(bool $teleport = false) : void{
		$pk = new MoveActorAbsolutePacket();
		$pk->entityRuntimeId = $this->id;
		$pk->position = $this->getOffsetPosition($this);
		$pk->pitch = $this->pitch;
		$pk->yaw = $this->yaw;
		$pk->headYaw = $this->headYaw ?? $this->yaw;
		if($teleport){
			$pk->flags |= MoveActorAbsolutePacket::FLAG_TELEPORT;
		}
		if($this->onGround){
			$pk->flags |= MoveActorAbsolutePacket::FLAG_GROUND;
		}
		$this->level->broadcastPacketToViewers($this, $pk);
	}
	protected function broadcastMotion() : void{
		$pk = new SetActorMotionPacket();
		$pk->entityRuntimeId = $this->id;
		$pk->motion = $this->getMotion();
		$pk->tick = 0;
		$this->level->broadcastPacketToViewers($this, $pk);
	}
	public function applyEntityCollision(Entity $entity) : void{
		if(!$this->isRiding() and !$entity->isRiding()){
			if(!($entity instanceof Player and $entity->isSpectator())){
				$d0 = $entity->x - $this->x;
				$d1 = $entity->z - $this->z;
				$d2 = abs(max($d0, $d1));
				if($d2 > 0){
					$d2 = sqrt($d2);
					$d0 /= $d2;
					$d1 /= $d2;
					$d3 = min(1, 1 / $d2);
					$entity->setMotion($entity->getMotion()->add($d0 * $d3 * 0.05, 0, $d1 * $d3 * 0.05));
				}
			}
		}
	}
	protected function applyDragBeforeGravity() : bool{
		return false;
	}
	protected function applyGravity() : void{
		$this->motion->y -= $this->gravity;
	}
	protected function tryChangeMovement() : void{
		$friction = 1 - $this->drag;
		if($this->applyDragBeforeGravity()){
			$this->motion->y *= $friction;
		}
		$this->applyGravity();
		if(!$this->applyDragBeforeGravity()){
			$this->motion->y *= $friction;
		}
		if($this->onGround){
			$friction *= $this->level->getBlockAt((int) floor($this->x), (int) floor($this->y - 1), (int) floor($this->z))->getFrictionFactor();
		}
		$this->motion->x *= $friction;
		$this->motion->z *= $friction;
	}
	protected function checkObstruction(float $x, float $y, float $z) : bool{
		if(count($this->level->getCollisionCubes($this, $this->getBoundingBox(), false)) === 0){
			return false;
		}
		$floorX = (int) floor($x);
		$floorY = (int) floor($y);
		$floorZ = (int) floor($z);
		$diffX = $x - $floorX;
		$diffY = $y - $floorY;
		$diffZ = $z - $floorZ;
		if(BlockFactory::$solid[$this->level->getBlockIdAt($floorX, $floorY, $floorZ)]){
			$westNonSolid  = !BlockFactory::$solid[$this->level->getBlockIdAt($floorX - 1, $floorY, $floorZ)];
			$eastNonSolid  = !BlockFactory::$solid[$this->level->getBlockIdAt($floorX + 1, $floorY, $floorZ)];
			$downNonSolid  = !BlockFactory::$solid[$this->level->getBlockIdAt($floorX, $floorY - 1, $floorZ)];
			$upNonSolid    = !BlockFactory::$solid[$this->level->getBlockIdAt($floorX, $floorY + 1, $floorZ)];
			$northNonSolid = !BlockFactory::$solid[$this->level->getBlockIdAt($floorX, $floorY, $floorZ - 1)];
			$southNonSolid = !BlockFactory::$solid[$this->level->getBlockIdAt($floorX, $floorY, $floorZ + 1)];
			$direction = -1;
			$limit = 9999;
			if($westNonSolid){
				$limit = $diffX;
				$direction = Vector3::SIDE_WEST;
			}
			if($eastNonSolid and 1 - $diffX < $limit){
				$limit = 1 - $diffX;
				$direction = Vector3::SIDE_EAST;
			}
			if($downNonSolid and $diffY < $limit){
				$limit = $diffY;
				$direction = Vector3::SIDE_DOWN;
			}
			if($upNonSolid and 1 - $diffY < $limit){
				$limit = 1 - $diffY;
				$direction = Vector3::SIDE_UP;
			}
			if($northNonSolid and $diffZ < $limit){
				$limit = $diffZ;
				$direction = Vector3::SIDE_NORTH;
			}
			if($southNonSolid and 1 - $diffZ < $limit){
				$direction = Vector3::SIDE_SOUTH;
			}
			$force = lcg_value() * 0.2 + 0.1;
			if($direction === Vector3::SIDE_WEST){
				$this->motion->x = -$force;
				return true;
			}
			if($direction === Vector3::SIDE_EAST){
				$this->motion->x = $force;
				return true;
			}
			if($direction === Vector3::SIDE_DOWN){
				$this->motion->y = -$force;
				return true;
			}
			if($direction === Vector3::SIDE_UP){
				$this->motion->y = $force;
				return true;
			}
			if($direction === Vector3::SIDE_NORTH){
				$this->motion->z = -$force;
				return true;
			}
			if($direction === Vector3::SIDE_SOUTH){
				$this->motion->z = $force;
				return true;
			}
		}
		return false;
	}
	public function getDirection() : ?int{
		$rotation = fmod($this->yaw - 90, 360);
		if($rotation < 0){
			$rotation += 360.0;
		}
		if((0 <= $rotation and $rotation < 45) or (315 <= $rotation and $rotation < 360)){
			return 2; 
		}elseif(45 <= $rotation and $rotation < 135){
			return 3; 
		}elseif(135 <= $rotation and $rotation < 225){
			return 0; 
		}elseif(225 <= $rotation and $rotation < 315){
			return 1; 
		}else{
			return null;
		}
	}
	public function getDirectionVector() : Vector3{
		$y = -sin(deg2rad($this->pitch));
		$xz = cos(deg2rad($this->pitch));
		$x = -$xz * sin(deg2rad($this->yaw));
		$z = $xz * cos(deg2rad($this->yaw));
		return $this->temporalVector->setComponents($x, $y, $z)->normalize();
	}
	public function getDirectionPlane() : Vector2{
		return (new Vector2(-cos(deg2rad($this->yaw) - M_PI_2), -sin(deg2rad($this->yaw) - M_PI_2)))->normalize();
	}
	public function onUpdate(int $currentTick) : bool{
		if($this->closed){
			return false;
		}
		$tickDiff = $currentTick - $this->lastUpdate;
		if($tickDiff <= 0){
			if(!$this->justCreated){
				$this->server->getLogger()->debug("Expected tick difference of at least 1, got $tickDiff for " . get_class($this));
			}
			return true;
		}
		$this->lastUpdate = $currentTick;
		if(!$this->isAlive()){
			if($this->onDeathUpdate($tickDiff)){
				$this->flagForDespawn();
			}
			return true;
		}
		$this->timings->startTiming();
		if($this->hasMovementUpdate()){
			$this->onMovementUpdate();
			$this->forceMovementUpdate = false;
			$this->updateMovement();
		}
		Timings::$timerEntityBaseTick->startTiming();
		$hasUpdate = $this->entityBaseTick($tickDiff);
		Timings::$timerEntityBaseTick->stopTiming();
		$this->timings->stopTiming();
		return ($hasUpdate or $this->hasMovementUpdate());
	}
	protected function onMovementUpdate() : void{
		$this->tryChangeMovement();
		$this->checkMotion();
		if($this->motion->x != 0 or $this->motion->y != 0 or $this->motion->z != 0){
			$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		}
	}
	protected function checkMotion() : void{
		if(abs($this->motion->x) <= self::MOTION_THRESHOLD){
			$this->motion->x = 0;
		}
		if(abs($this->motion->y) <= self::MOTION_THRESHOLD){
			$this->motion->y = 0;
		}
		if(abs($this->motion->z) <= self::MOTION_THRESHOLD){
			$this->motion->z = 0;
		}
	}
	final public function scheduleUpdate() : void{
		if($this->closed){
			throw new InvalidStateException("Cannot schedule update on garbage entity " . get_class($this));
		}
		$this->level->updateEntities[$this->id] = $this;
	}
	public function onNearbyBlockChange() : void{
		$this->setForceMovementUpdate();
		$this->scheduleUpdate();
	}
	final public function setForceMovementUpdate(bool $value = true) : void{
		$this->forceMovementUpdate = $value;
		$this->blocksAround = null;
	}
	public function hasMovementUpdate() : bool{
		return (
			$this->forceMovementUpdate or
			$this->motion->x != 0 or
			$this->motion->y != 0 or
			$this->motion->z != 0 or
			!$this->onGround
		);
	}
	public function canTriggerWalking() : bool{
		return true;
	}
	public function canBePushed() : bool{
		return false;
	}
	public function resetFallDistance() : void{
		$this->fallDistance = 0.0;
	}
	protected function updateFallState(float $distanceThisTick, bool $onGround) : void{
		if($onGround){
			if($this->fallDistance > 0){
				$block = $this->level->getBlockAt($this->getFloorX(), (int) floor($this->y - 0.2), $this->getFloorZ());
				if($block->isSolid()){
					$block->onEntityFallenUpon($this, $this->fallDistance);
				}
				$this->fall($this->fallDistance);
				$this->resetFallDistance();
			}
		}elseif($distanceThisTick < $this->fallDistance){
			$this->fallDistance -= $distanceThisTick;
		}else{
			$this->fallDistance = 0;
		}
	}
	public function mountEntity(Entity $entity, int $seatNumber = 0, bool $causedByRider = true) : bool{
		if($this->getRidingEntity() === null and $entity !== $this and count($entity->passengers) < $entity->getSeatCount()){
			if(!isset($entity->passengers[$seatNumber])){
				if($seatNumber === 0){
					$entity->setRiddenByEntity($this);
					$this->setRiding(true);
					$entity->setGenericFlag(self::DATA_FLAG_WASD_CONTROLLED, true);
				}
				$this->setRotation($entity->yaw, $entity->pitch);
				$this->setRidingEntity($entity);
				$entity->passengers[$seatNumber] = $this->getId();
				$this->propertyManager->setVector3(self::DATA_RIDER_SEAT_POSITION, $entity->getRiderSeatPosition($seatNumber)->add(0, $this->getMountedYOffset(), 0));
				$this->propertyManager->setByte(self::DATA_CONTROLLING_RIDER_SEAT_NUMBER, $seatNumber);
				$entity->sendLink($entity->getViewers(), $this->getId(), EntityLink::TYPE_RIDER, $causedByRider);
				$entity->onRiderMount($this);
				return true;
			}
		}
		return false;
	}
	public function onRiderMount(Entity $entity) : void{
	}
	public function onRiderLeave(Entity $entity) : void{
	}
	public function sendLink(array $targets, int $entityId, int $type = EntityLink::TYPE_RIDER, bool $immediate = false, bool $causedByRider = true, float $vehicleAngularVelocity = 0.0) : void{
		$pk = new SetActorLinkPacket();
		$pk->link = new EntityLink($this->id, $entityId, $type, $immediate, $causedByRider, $vehicleAngularVelocity);
		$this->server->broadcastPacket($targets, $pk);
	}
	public function getMountedYOffset() : float{
		return $this->height * 0.65;
	}
	public function dismountEntity(bool $immediate = false) : bool{
		if($this->getRidingEntity() !== null){
			$entity = $this->getRidingEntity();
			unset($entity->passengers[$this->propertyManager->getByte(self::DATA_CONTROLLING_RIDER_SEAT_NUMBER)]);
			if($entity->getRiddenByEntity() === $this){
				$entity->setRiddenByEntity(null);
				$this->entityRiderYawDelta = 0;
				$this->entityRiderPitchDelta = 0;
				$this->setRiding(false);
				$entity->setGenericFlag(Entity::DATA_FLAG_WASD_CONTROLLED, false);
			}
			$this->propertyManager->removeProperty(self::DATA_RIDER_SEAT_POSITION);
			$this->propertyManager->removeProperty(self::DATA_CONTROLLING_RIDER_SEAT_NUMBER);
			$this->setRidingEntity(null);
			$entity->sendLink($entity->getViewers(), $this->getId(), EntityLink::TYPE_REMOVE, $immediate);
			$entity->onRiderLeave($this);
			return true;
		}
		return false;
	}
	public function getRiderSeatPosition(int $seatNumber = 0) : Vector3{
		return new Vector3(0, $this->getEyeHeight(), 0);
	}
	public function getSeatCount() : int{
		return 1;
	}
	public function updateRiderPosition() : void{
		if($this->getRiddenByEntity() !== null){
			$this->getRiddenByEntity()->setPosition($this->add($this->getRiderSeatPosition()));
		}
	}
	public function updateRidden() : void{
		if($this->getRidingEntity() === null) return;
		if($this->getRidingEntity()->isClosed()){
			$this->ridingEid = null;
		}else{
			$this->resetMotion();
			if(!($this instanceof Player)){
				$this->getRidingEntity()->updateRiderPosition();
			}
			$this->entityRiderYawDelta += $this->yaw - $this->lastYaw;
			for($this->entityRiderPitchDelta += $this->pitch - $this->lastPitch; $this->entityRiderYawDelta >= 180; $this->entityRiderYawDelta -= 360){
			}
			while($this->entityRiderYawDelta < -180){
				$this->entityRiderYawDelta += 360;
			}
			while($this->entityRiderPitchDelta >= 180){
				$this->entityRiderPitchDelta -= 360;
			}
			while($this->entityRiderPitchDelta < -180){
				$this->entityRiderPitchDelta += 360;
			}
			$d0 = $this->entityRiderYawDelta * 0.5;
			$d1 = $this->entityRiderPitchDelta * 0.5;
			$f = 10;
			$d0 = ($d0 > $f) ? $f : (($d0 < -$f) ? -$f : $d0);
			$d1 = ($d1 > $f) ? $f : (($d1 < -$f) ? -$f : $d1);
			$this->entityRiderYawDelta -= $d0;
			$this->entityRiderPitchDelta -= $d1;
		}
	}
	public function fall(float $fallDistance) : void{
		if($this->getRidingEntity() instanceof Entity){
			$this->getRidingEntity()->fall($fallDistance);
		}
	}
	public function getEyeHeight() : float{
		return $this->eyeHeight;
	}
	public function moveFlying(float $strafe, float $forward, float $friction) : bool{
		$f = $strafe * $strafe + $forward * $forward;
		if($f >= self::MOTION_THRESHOLD){
			$f = sqrt($f);
			if($f < 1) $f = 1;
			$f = $friction / $f;
			$strafe *= $f;
			$forward *= $f;
			$f1 = sin($this->yaw * pi() / 180);
			$f2 = cos($this->yaw * pi() / 180);
			$this->motion->x += $strafe * $f2 - $forward * $f1;
			$this->motion->z += $forward * $f2 + $strafe * $f1;
			return true;
		}
		return false;
	}
	public function onCollideWithPlayer(Player $player) : void{
	}
	public function onCollideWithEntity(Entity $entity) : void{
		if($this->canBePushed()){
			$entity->applyEntityCollision($this);
		}
	}
	public function isInsideOfPortal() : bool{
		$blocks = $this->getBlocksAround();
		foreach($blocks as $block){
			if($block instanceof Portal){
				return true;
			}
		}
		return false;
	}
	public function isInsideOfFire() : bool{
		foreach($this->getBlocksAround() as $block){
			if($block instanceof Fire){
				return true;
			}
		}
		return false;
	}
	public function isUnderwater() : bool{
		$block = $this->level->getBlockAt((int) floor($this->x), (int) floor($y = ($this->y + $this->getEyeHeight())), (int) floor($this->z));
		if($block instanceof Water){
			$f = ($block->y + 1) - ($block->getFluidHeightPercent() - 0.1111111);
			return $y < $f;
		}
		return false;
	}
	public function isWet() : bool{
		return $this->isInsideOfWater();
	}
	public function isInsideOfSolid() : bool{
		$block = $this->level->getBlockAt((int) floor($this->x), (int) floor($y = ($this->y + $this->getEyeHeight())), (int) floor($this->z));
		return $block->isSolid() and !$block->isTransparent() and $block->collidesWithBB($this->getBoundingBox());
	}
	public function isInsideOfLava() : bool{
		$block = $this->level->getBlockAt((int) floor($this->x), (int) floor($this->y), (int) floor($this->z));
		return $block instanceof Lava;
	}
	public function isInsideOfWater() : bool{
		$block = $this->level->getBlockAt((int) floor($this->x), (int) floor($this->y), (int) floor($this->z));
		return $block instanceof Water;
	}
	public function fastMove(float $dx, float $dy, float $dz) : bool{
		$this->blocksAround = null;
		if($dx == 0 and $dz == 0 and $dy == 0){
			return true;
		}
		Timings::$entityMoveTimer->startTiming();
		$newBB = $this->boundingBox->offsetCopy($dx, $dy, $dz);
		$list = $this->level->getCollisionCubes($this, $newBB, false);
		if(count($list) === 0){
			$this->boundingBox = $newBB;
		}
		$this->x = ($this->boundingBox->minX + $this->boundingBox->maxX) / 2;
		$this->y = $this->boundingBox->minY - $this->ySize;
		$this->z = ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2;
		$this->checkChunks();
		if(!$this->onGround or $dy != 0){
			$bb = clone $this->boundingBox;
			$bb->minY -= 0.75;
			$this->onGround = false;
			if(count($this->level->getCollisionBlocks($bb)) > 0){
				$this->onGround = true;
			}
		}
		$this->isCollided = $this->onGround;
		$this->updateFallState($dy, $this->onGround);
		Timings::$entityMoveTimer->stopTiming();
		return true;
	}
	public function move(float $dx, float $dy, float $dz) : void{
		$this->blocksAround = null;
		Timings::$entityMoveTimer->startTiming();
		$movX = $dx;
		$movY = $dy;
		$movZ = $dz;
		if($this->keepMovement){
			$this->boundingBox->offset($dx, $dy, $dz);
		}else{
			$this->ySize *= self::STEP_CLIP_MULTIPLIER;
			$axisalignedbb = clone $this->boundingBox;
			assert(abs($dx) <= 20 and abs($dy) <= 20 and abs($dz) <= 20, "Movement distance is excessive: dx=$dx, dy=$dy, dz=$dz");
			$list = $this->level->getCollisionCubes($this, $this->level->getTickRateTime() > 50 ? $this->boundingBox->offsetCopy($dx, $dy, $dz) : $this->boundingBox->addCoord($dx, $dy, $dz), false);
			foreach($list as $bb){
				$dy = $bb->calculateYOffset($this->boundingBox, $dy);
			}
			$this->boundingBox->offset(0, $dy, 0);
			$fallingFlag = ($this->onGround or ($dy != $movY and $movY < 0));
			foreach($list as $bb){
				$dx = $bb->calculateXOffset($this->boundingBox, $dx);
			}
			$this->boundingBox->offset($dx, 0, 0);
			foreach($list as $bb){
				$dz = $bb->calculateZOffset($this->boundingBox, $dz);
			}
			$this->boundingBox->offset(0, 0, $dz);
			if($this->stepHeight > 0 and $fallingFlag and ($movX != $dx or $movZ != $dz)){
				$cx = $dx;
				$cy = $dy;
				$cz = $dz;
				$dx = $movX;
				$dy = $this->stepHeight;
				$dz = $movZ;
				$axisalignedbb1 = clone $this->boundingBox;
				$this->boundingBox->setBB($axisalignedbb);
				$list = $this->level->getCollisionCubes($this, $this->boundingBox->addCoord($dx, $dy, $dz), false);
				foreach($list as $bb){
					$dy = $bb->calculateYOffset($this->boundingBox, $dy);
				}
				$this->boundingBox->offset(0, $dy, 0);
				foreach($list as $bb){
					$dx = $bb->calculateXOffset($this->boundingBox, $dx);
				}
				$this->boundingBox->offset($dx, 0, 0);
				foreach($list as $bb){
					$dz = $bb->calculateZOffset($this->boundingBox, $dz);
				}
				$this->boundingBox->offset(0, 0, $dz);
				$reverseDY = -$dy;
				foreach($list as $bb){
					$reverseDY = $bb->calculateYOffset($this->boundingBox, $reverseDY);
				}
				$dy += $reverseDY;
				$this->boundingBox->offset(0, $reverseDY, 0);
				if(($cx ** 2 + $cz ** 2) >= ($dx ** 2 + $dz ** 2)){
					$dx = $cx;
					$dy = $cy;
					$dz = $cz;
					$this->boundingBox->setBB($axisalignedbb1);
				}else{
					$this->ySize += $dy;
				}
			}
		}
		$this->x = ($this->boundingBox->minX + $this->boundingBox->maxX) / 2;
		$this->y = $this->boundingBox->minY - $this->ySize;
		$this->z = ($this->boundingBox->minZ + $this->boundingBox->maxZ) / 2;
		$this->checkChunks();
		$this->checkBlockCollision();
		$this->checkEntityCollision();
		$this->checkGroundState($movX, $movY, $movZ, $dx, $dy, $dz);
		$this->updateFallState($dy, $this->onGround);
		if($movX != $dx){
			$this->motion->x = 0;
		}
		if($movY != $dy){
			$this->motion->y = 0;
		}
		if($movZ != $dz){
			$this->motion->z = 0;
		}
		Timings::$entityMoveTimer->stopTiming();
	}
	protected function checkGroundState(float $movX, float $movY, float $movZ, float $dx, float $dy, float $dz) : void{
		$this->isCollidedVertically = $movY != $dy;
		$this->isCollidedHorizontally = ($movX != $dx or $movZ != $dz);
		$this->isCollided = ($this->isCollidedHorizontally or $this->isCollidedVertically);
		$this->onGround = ($movY != $dy and $movY < 0);
	}
	public function getBlocksAround() : array{
		if($this->blocksAround === null){
			$inset = 0.001; 
			$minX = (int) floor($this->boundingBox->minX + $inset);
			$minY = (int) floor($this->boundingBox->minY + $inset);
			$minZ = (int) floor($this->boundingBox->minZ + $inset);
			$maxX = (int) floor($this->boundingBox->maxX - $inset);
			$maxY = (int) floor($this->boundingBox->maxY - $inset);
			$maxZ = (int) floor($this->boundingBox->maxZ - $inset);
			$this->blocksAround = [];
			for($z = $minZ; $z <= $maxZ; ++$z){
				for($x = $minX; $x <= $maxX; ++$x){
					for($y = $minY; $y <= $maxY; ++$y){
						$block = $this->level->getBlockAt($x, $y, $z);
						if($block->hasEntityCollision()){
							$this->blocksAround[] = $block;
						}
					}
				}
			}
		}
		return $this->blocksAround;
	}
	public function canBeMovedByCurrents() : bool{
		return true;
	}
	protected function checkBlockCollision() : void{
		$vector = $this->temporalVector->setComponents(0, 0, 0);
		foreach($this->getBlocksAround() as $block){
			$block->onEntityCollide($this);
			$block->addVelocityToEntity($this, $vector);
		}
		if($this instanceof Living){
			$down = $this->level->getBlockAt($this->getFloorX(), $this->getFloorY() - 1, $this->getFloorZ());
			if($down->hasEntityCollision()){
				$down->onEntityCollideUpon($this);
			}
		}
		if($vector->lengthSquared() > 0){
			$vector = $vector->normalize();
			$d = 0.014;
			$this->motion->x += $vector->x * $d;
			$this->motion->y += $vector->y * $d;
			$this->motion->z += $vector->z * $d;
		}
	}
	protected function checkEntityCollision() : void{
		if($this->canBePushed()){
			foreach($this->level->getCollidingEntities($this->getBoundingBox()->expandedCopy(0.2, 0, 0.2), $this) as $e){
				$this->onCollideWithEntity($e);
			}
		}
	}
	public function getPosition() : Position{
		return $this->asPosition();
	}
	public function getLocation() : Location{
		return $this->asLocation();
	}
	public function setPosition(Vector3 $pos) : bool{
		if($this->closed){
			return false;
		}
		if($pos instanceof Position and $pos->level !== null and $pos->level !== $this->level){
			if(!$this->switchLevel($pos->getLevelNonNull())){
				return false;
			}
		}
		$this->x = $pos->x;
		$this->y = $pos->y;
		$this->z = $pos->z;
		$this->recalculateBoundingBox();
		$this->blocksAround = null;
		$this->checkChunks();
		return true;
	}
	public function setRotation(float $yaw, float $pitch) : void{
		$this->yaw = $yaw;
		$this->pitch = $pitch;
		$this->scheduleUpdate();
	}
	public function setPositionAndRotation(Vector3 $pos, float $yaw, float $pitch) : bool{
		if($this->setPosition($pos)){
			$this->setRotation($yaw, $pitch);
			return true;
		}
		return false;
	}
	protected function checkChunks() : void{
		$chunkX = $this->getFloorX() >> 4;
		$chunkZ = $this->getFloorZ() >> 4;
		if($this->chunk === null or ($this->chunk->getX() !== $chunkX or $this->chunk->getZ() !== $chunkZ)){
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			$this->chunk = $this->level->getChunk($chunkX, $chunkZ, true);
			if(!$this->justCreated){
				$newChunk = $this->level->getViewersForPosition($this);
				foreach($this->hasSpawned as $player){
					if(!isset($newChunk[$player->getLoaderId()])){
						$this->despawnFrom($player);
					}else{
						unset($newChunk[$player->getLoaderId()]);
					}
				}
				foreach($newChunk as $player){
					$this->spawnTo($player);
				}
			}
			if($this->chunk === null){
				return;
			}
			$this->chunk->addEntity($this);
		}
	}
	protected function resetLastMovements() : void{
		list($this->lastX, $this->lastY, $this->lastZ) = [$this->x, $this->y, $this->z];
		list($this->lastYaw, $this->lastPitch) = [$this->yaw, $this->pitch];
		$this->lastMotion = clone $this->motion;
	}
	public function getMotion() : Vector3{
		return clone $this->motion;
	}
	public function setMotion(Vector3 $motion) : bool{
		if(!$this->justCreated){
			$ev = new EntityMotionEvent($this, $motion);
			$ev->call();
			if($ev->isCancelled()){
				return false;
			}
		}
		$this->motion = clone $motion;
		if(!$this->justCreated){
			$this->updateMovement();
		}
		return true;
	}
	public function resetMotion() : void{
		$this->motion->setComponents(0, 0, 0);
	}
	public function addMotion(float $x, float $y, float $z) : void{
		$this->motion->x += $x;
		$this->motion->y += $y;
		$this->motion->z += $z;
	}
	public function isOnGround() : bool{
		return $this->onGround;
	}
	public function teleport(Vector3 $pos, ?float $yaw = null, ?float $pitch = null) : bool{
		if($pos instanceof Location){
			$yaw = $yaw ?? $pos->yaw;
			$pitch = $pitch ?? $pos->pitch;
		}
		$from = Position::fromObject($this, $this->level);
		$to = Position::fromObject($pos, $pos instanceof Position ? $pos->getLevelNonNull() : $this->level);
		$ev = new EntityTeleportEvent($this, $from, $to);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}
		$this->ySize = 0;
		$pos = $ev->getTo();
		$this->setMotion($this->temporalVector->setComponents(0, 0, 0));
		$this->dismountEntity(true);
		if($this->setPositionAndRotation($pos, $yaw ?? $this->yaw, $pitch ?? $this->pitch)){
			$this->resetFallDistance();
			$this->setForceMovementUpdate();
			$this->updateMovement(true);
			return true;
		}
		return false;
	}
	protected function switchLevel(Level $targetLevel) : bool{
		if($this->closed){
			return false;
		}
		if($this->isValid()){
			$ev = new EntityLevelChangeEvent($this, $this->level, $targetLevel);
			$ev->call();
			if($ev->isCancelled()){
				return false;
			}
			$this->dismountEntity(true);
			$this->level->removeEntity($this);
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
			}
			$this->despawnFromAll();
		}
		$this->setLevel($targetLevel);
		$this->level->addEntity($this);
		$this->chunk = null;
		return true;
	}
	public function getId() : int{
		return $this->id;
	}
	public function getViewers() : array{
		return $this->hasSpawned;
	}
	protected function sendSpawnPacket(Player $player) : void{
		$pk = new AddActorPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = static::NETWORK_ID;
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->headYaw = $this->headYaw ?? $this->yaw;
		$pk->bodyYaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->attributes = $this->attributeMap->getAll();
		$pk->metadata = $this->propertyManager->getAll();
		$pk->syncedProperties = new PropertySyncData([], []);
		if(!empty($this->passengers)){
			foreach($this->getPassengers() as $passenger){
				$passenger->spawnTo($player);
			}
			$pk->links = array_map(function(int $entityId){
				return new EntityLink($this->getId(), $entityId, EntityLink::TYPE_RIDER, true, false);
			}, $this->passengers);
		}
		$player->dataPacket($pk);
	}
	public function spawnTo(Player $player) : void{
		if(
            !isset($this->hasSpawned[$player->getLoaderId()]) and
            $this->chunk !== null and
            isset($player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())]) and
            $player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())] === true
        ){
			$this->hasSpawned[$player->getLoaderId()] = $player;
			$this->sendSpawnPacket($player);
		}
	}
	public function spawnToAll() : void{
		if($this->chunk === null or $this->closed){
			return;
		}
		foreach($this->level->getViewersForPosition($this) as $player){
			if($player->isOnline()){
				$this->spawnTo($player);
			}
		}
	}
	public function respawnToAll() : void{
		foreach($this->hasSpawned as $key => $player){
			unset($this->hasSpawned[$key]);
			$this->spawnTo($player);
		}
	}
	public function despawnFrom(Player $player, bool $send = true) : void{
		if(isset($this->hasSpawned[$player->getLoaderId()])){
			if($send){
				$pk = new RemoveActorPacket();
				$pk->entityUniqueId = $this->id;
				$player->dataPacket($pk);
			}
			unset($this->hasSpawned[$player->getLoaderId()]);
		}
	}
	public function despawnFromAll() : void{
		foreach($this->hasSpawned as $player){
			$this->despawnFrom($player);
		}
	}
	public function flagForDespawn() : void{
		$this->needsDespawn = true;
		$this->scheduleUpdate();
	}
	public function isFlaggedForDespawn() : bool{
		return $this->needsDespawn;
	}
	public function isClosed() : bool{
		return $this->closed;
	}
	public function close() : void{
		if($this->closeInFlight){
			return;
		}
		if(!$this->closed){
			$this->closeInFlight = true;
			(new EntityDespawnEvent($this))->call();
			$this->closed = true;
			$this->despawnFromAll();
			$this->hasSpawned = [];
			if($this->chunk !== null){
				$this->chunk->removeEntity($this);
				$this->chunk = null;
			}
			if($this->isValid()){
				$this->level->removeEntity($this);
				$this->setLevel(null);
			}
			$this->namedtag = null;
			$this->lastDamageCause = null;
			$this->closeInFlight = false;
		}
	}
	public function setDataFlag(int $propertyId, int $flagId, bool $value = true, int $propertyType = self::DATA_TYPE_LONG) : void{
		if($this->getDataFlag($propertyId, $flagId) !== $value){
			$flags = (int) $this->propertyManager->getPropertyValue($propertyId, $propertyType);
			$flags ^= 1 << $flagId;
			$this->propertyManager->setPropertyValue($propertyId, $propertyType, $flags);
		}
	}
	public function getDataFlag(int $propertyId, int $flagId) : bool{
		return (((int) $this->propertyManager->getPropertyValue($propertyId, -1)) & (1 << $flagId)) > 0;
	}
	public function getGenericFlag(int $flagId) : bool{
		return $this->getDataFlag($flagId >= 64 ? self::DATA_FLAGS2 : self::DATA_FLAGS, $flagId % 64);
	}
	public function setGenericFlag(int $flagId, bool $value = true) : void{
		$this->setDataFlag($flagId >= 64 ? self::DATA_FLAGS2 : self::DATA_FLAGS, $flagId % 64, $value, self::DATA_TYPE_LONG);
	}
	public function sendData($player, ?array $data = null) : void{
		if(!is_array($player)){
			$player = [$player];
		}
		$pk = new SetActorDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = $data ?? $this->propertyManager->getAll();
		$pk->syncedProperties = new PropertySyncData([], []);
		foreach($player as $p){
			if($p === $this){
				continue;
			}
			$p->dataPacket(clone $pk);
		}
		if($this instanceof Player){
			$this->dataPacket($pk);
		}
	}
	public function broadcastEntityEvent(int $eventId, ?int $eventData = null, ?array $players = null) : void{
		$pk = new ActorEventPacket();
		$pk->entityRuntimeId = $this->id;
		$pk->event = $eventId;
		$pk->data = $eventData ?? 0;
		$this->server->broadcastPacket($players ?? $this->getViewers(), $pk);
	}
	public function getPassengers() : array{
		$passengers = [];
		foreach($this->passengers as $id){
			$entity = $this->server->findEntity($id);
			if($entity !== null){
				$passengers[] = $entity;
			}
		}
		return $passengers;
	}
	public function __destruct(){
		$this->close();
	}
	public function onFirstInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
		return false;
	}
	public function setClientPositionAndRotation(Vector3 $pos, float $yaw, float $pitch, int $clientMoveTicks, bool $immediate) : void{
		$this->clientPos = $pos;
		$this->clientYaw = $yaw;
		$this->clientPitch = $pitch;
		$this->clientMoveTicks = $clientMoveTicks;
	}
	public function setClientMotion(Vector3 $motion) : void{
		$this->motion = $motion;
	}
	public function setMetadata(string $metadataKey, MetadataValue $newMetadataValue){
		$this->server->getEntityMetadata()->setMetadata($this, $metadataKey, $newMetadataValue);
	}
	public function getMetadata(string $metadataKey){
		return $this->server->getEntityMetadata()->getMetadata($this, $metadataKey);
	}
	public function hasMetadata(string $metadataKey) : bool{
		return $this->server->getEntityMetadata()->hasMetadata($this, $metadataKey);
	}
	public function removeMetadata(string $metadataKey, Plugin $owningPlugin){
		$this->server->getEntityMetadata()->removeMetadata($this, $metadataKey, $owningPlugin);
	}
	public static function getDefaultKnockBackData() : KnockBackData{
		return self::$defaultKnockBackData;
	}
	private static function initDefaultKnockBackData() : void{
		$kbData = Server::getInstance()->getAdvancedProperty("entity.knockBack", [
			"vanilla" => true,
		]);
		self::$defaultKnockBackData = $kbData["vanilla"] ?
		    new KnockBackData() :
		    new KnockBackData(
		        false,
		        $kbData["modX"],
		        $kbData["modY"],
		        $kbData["delay"],
		        $kbData["verticalLimitEnabled"],
		        $kbData["verticalLimit"],
		        $kbData["verticalLimitMultiplier"]
		    );
	}
	public function __toString(){
		return (new ReflectionClass($this))->getShortName() . "(" . $this->getId() . ")";
	}
	public function __get($name){
		if($name === "fireTicks"){
			return $this->fireTicks;
		}
		throw new ErrorException("Undefined property: " . get_class($this) . "::\$" . $name);
	}
	public function __set($name, $value){
		if($name === "fireTicks"){
			$this->setFireTicks($value);
		}else{
			throw new ErrorException("Undefined property: " . get_class($this) . "::\$" . $name);
		}
	}
	public function __isset($name){
		return $name === "fireTicks";
	}}