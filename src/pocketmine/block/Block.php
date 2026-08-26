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
namespace pocketmine\block;
use InvalidArgumentException;use pocketmine\entity\Entity;use pocketmine\item\enchantment\Enchantment;use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\level\Position;use pocketmine\math\AxisAlignedBB;use pocketmine\math\RayTraceResult;use pocketmine\math\Vector3;use pocketmine\metadata\Metadatable;use pocketmine\metadata\MetadataValue;use pocketmine\Player;use pocketmine\plugin\Plugin;use function array_merge;use function get_class;use const PHP_INT_MAX;
class Block extends Position implements BlockIds, Metadatable{
	public const INTERNAL_METADATA_BITS = 4;
	public const INTERNAL_METADATA_MASK = ~(~0 << self::INTERNAL_METADATA_BITS);
	public static function get(int $id, int $meta = 0, Position $pos = null) : Block{
		return BlockFactory::get($id, $meta, $pos);
	}
	protected $id;
	protected $meta = 0;
	protected $fallbackName;
	protected $itemId;
	protected $boundingBox = null;
	protected $collisionBoxes = null;
	public function __construct(int $id, int $meta = 0, string $name = null, int $itemId = null){
		$this->id = $id;
		$this->meta = $meta;
		$this->fallbackName = $name;
		$this->itemId = $itemId;
	}
	public function getName() : string{
		return $this->fallbackName ?? "Unknown";
	}
	final public function getId() : int{
		return $this->id;
	}
	public function getItemId() : int{
		return $this->itemId ?? ($this->getId() > 255 ? 255 - $this->getId() : $this->getId());
	}
    public function asItem() : Item{
        return ItemFactory::get($this->getItemId(), $this->getVariant());
    }
	final public function getDamage() : int{
		return $this->meta;
	}
	final public function setDamage(int $meta) : void{
		if($meta < 0 or $meta > 0xf){
			throw new InvalidArgumentException("Block damage values must be 0-15, not $meta");
		}
		$this->meta = $meta;
	}
	public function getVariantBitmask() : int{
		return -1;
	}
	public function getVariant() : int{
		return $this->meta & $this->getVariantBitmask();
	}
	public function canBePlaced() : bool{
		return true;
	}
	public function canBeReplaced() : bool{
		return false;
	}
	public function canBePlacedAt(Block $blockReplace, Vector3 $clickVector, int $face, bool $isClickedBlock) : bool{
		return $blockReplace->canBeReplaced();
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		return $this->getLevelNonNull()->setBlock($this, $this, true, true);
	}
	public function isBreakable(Item $item) : bool{
		return true;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_NONE;
	}
	public function getToolHarvestLevel() : int{
		return 0;
	}
	public function isCompatibleWithTool(Item $tool) : bool{
		if($this->getHardness() < 0){
			return false;
		}
		$toolType = $this->getToolType();
		$harvestLevel = $this->getToolHarvestLevel();
		return $toolType === BlockToolType::TYPE_NONE or $harvestLevel === 0 or (
			($toolType & $tool->getBlockToolType()) !== 0 and $tool->getBlockToolHarvestLevel() >= $harvestLevel);
	}
	public function onBreak(Item $item, Player $player = null) : bool{
		return $this->getLevelNonNull()->setBlock($this, BlockFactory::get(Block::AIR), true, true);
	}
	public function getBreakTime(Item $item) : float{
		$base = $this->getHardness();
		if($this->isCompatibleWithTool($item)){
			$base *= 1.5;
		}else{
			$base *= 5;
		}
		$efficiency = $item->getMiningEfficiency($this);
		if($efficiency <= 0){
			throw new InvalidArgumentException(get_class($item) . " has invalid mining efficiency: expected >= 0, got $efficiency");
		}
		$base /= $efficiency;
		return $base;
	}
	public function onNearbyBlockChange() : void{
	}
	public function ticksRandomly() : bool{
		return false;
	}
	public function onRandomTick() : void{
	}
	public function onScheduledUpdate() : void{
	}
	public function onActivate(Item $item, Player $player = null) : bool{
		return false;
	}
	public function onAttack(Item $item, int $face, Player $player = null) : bool{
		return false;
	}
	public function getHardness() : float{
		return 10;
	}
	public function getBlastResistance() : float{
		return $this->getHardness() * 5;
	}
	public function getFrictionFactor() : float{
		return 0.6;
	}
	public function getLightLevel() : int{
		return 0;
	}
	public function getLightFilter() : int{
		return 15;
	}
	public function diffusesSkyLight() : bool{
		return false;
	}
	public function isTransparent() : bool{
		return false;
	}
	public function isSolid() : bool{
		return true;
	}
	public function canBeFlowedInto() : bool{
		return false;
	}
	public function activate(array $ignore = []){
		return false;
	}
	public function deactivate(array $ignore = []){
		return false;
	}
	public function isActivated(Block $from = null) : bool{
		return false;
	}
	public function turnAroundOff(array $ignore = []) : void{
	}
	public function turnOn() : bool{
		return false;
	}
	public function turnOff() : bool{
		return false;
	}
	public function hasEntityCollision() : bool{
		return false;
	}
	public function canPassThrough() : bool{
		return false;
	}
	public function canClimb() : bool{
		return false;
	}
	public function isPassable() : bool{
		return !$this->isSolid();
	}
	public function addVelocityToEntity(Entity $entity, Vector3 $vector) : void{
	}
	final public function position(Position $v) : void{
		$this->x = (int) $v->x;
		$this->y = (int) $v->y;
		$this->z = (int) $v->z;
		$this->level = $v->level;
		$this->boundingBox = null;
	}
	public function getDrops(Item $item) : array{
		if($this->isCompatibleWithTool($item)){
			if($this->isAffectedBySilkTouch() and $item->hasEnchantment(Enchantment::SILK_TOUCH)){
				return $this->getSilkTouchDrops($item);
			}
			return $this->getDropsForCompatibleTool($item);
		}
		return [];
	}
	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			ItemFactory::get($this->getItemId(), $this->getVariant())
		];
	}
	public function getSilkTouchDrops(Item $item) : array{
		return [
			ItemFactory::get($this->getItemId(), $this->getVariant())
		];
	}
	public function getXpDropForTool(Item $item) : int{
		if($item->hasEnchantment(Enchantment::SILK_TOUCH) or !$this->isCompatibleWithTool($item)){
			return 0;
		}
		return $this->getXpDropAmount();
	}
	protected function getXpDropAmount() : int{
		return 0;
	}
	public function isAffectedBySilkTouch() : bool{
		return true;
	}
	public function getPickedItem() : Item{
		return ItemFactory::get($this->getItemId(), $this->getVariant());
	}
	public function getFuelTime() : int{
		return 0;
	}
	public function getFlameEncouragement() : int{
		return 0;
	}
	public function getFlammability() : int{
		return 0;
	}
	public function burnsForever() : bool{
		return false;
	}
	public function isFlammable() : bool{
		return $this->getFlammability() > 0;
	}
	public function onIncinerate() : void{
	}
	public function getSide(int $side, int $step = 1){
		if($this->isValid()){
			return $this->getLevelNonNull()->getBlock(Vector3::getSide($side, $step));
		}
		return BlockFactory::get(Block::AIR, 0, Position::fromObject(Vector3::getSide($side, $step)));
	}
	public function getHorizontalSides() : array{
		return [
			$this->getSide(Vector3::SIDE_NORTH),
			$this->getSide(Vector3::SIDE_SOUTH),
			$this->getSide(Vector3::SIDE_WEST),
			$this->getSide(Vector3::SIDE_EAST)
		];
	}
	public function getAllSides() : array{
		return array_merge(
			[
				$this->getSide(Vector3::SIDE_DOWN),
				$this->getSide(Vector3::SIDE_UP)
			],
			$this->getHorizontalSides()
		);
	}
	public function getAffectedBlocks() : array{
		return [$this];
	}
	public function __toString(){
		return "Block[" . $this->getName() . "] (" . $this->getId() . ":" . $this->getDamage() . ")";
	}
	public function collidesWithBB(AxisAlignedBB $bb) : bool{
		foreach($this->getCollisionBoxes() as $bb2){
			if($bb->intersectsWith($bb2)){
				return true;
			}
		}
		return false;
	}
	public function onEntityCollide(Entity $entity) : void{
	}
	public function onEntityFallenUpon(Entity $entity, float $fallDistance) : void{
	}
	public function onEntityCollideUpon(Entity $entity) : void{
	}
	public function getCollisionBoxes() : array{
		if($this->collisionBoxes === null){
			$this->collisionBoxes = $this->recalculateCollisionBoxes();
		}
		return $this->collisionBoxes;
	}
	protected function recalculateCollisionBoxes() : array{
		if($bb = $this->recalculateBoundingBox()){
			return [$bb];
		}
		return [];
	}
	public function getBoundingBox() : ?AxisAlignedBB{
		if($this->boundingBox === null){
			$this->boundingBox = $this->recalculateBoundingBox();
		}
		return $this->boundingBox;
	}
	protected function recalculateBoundingBox() : ?AxisAlignedBB{
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 1,
			$this->z + 1
		);
	}
	public function clearCaches() : void{
		$this->boundingBox = null;
		$this->collisionBoxes = null;
	}
	public function calculateIntercept(Vector3 $pos1, Vector3 $pos2) : ?RayTraceResult{
		$bbs = $this->getCollisionBoxes();
		if(empty($bbs)){
			return null;
		}
		$currentHit = null;
		$currentDistance = PHP_INT_MAX;
		foreach($bbs as $bb){
			$nextHit = $bb->calculateIntercept($pos1, $pos2);
			if($nextHit === null){
				continue;
			}
			$nextDistance = $nextHit->hitVector->distanceSquared($pos1);
			if($nextDistance < $currentDistance){
				$currentHit = $nextHit;
				$currentDistance = $nextDistance;
			}
		}
		return $currentHit;
	}
	public function setMetadata(string $metadataKey, MetadataValue $newMetadataValue){
		if($this->isValid()){
			$this->level->getBlockMetadata()->setMetadata($this, $metadataKey, $newMetadataValue);
		}
	}
	public function getMetadata(string $metadataKey){
		if($this->isValid()){
			return $this->level->getBlockMetadata()->getMetadata($this, $metadataKey);
		}
		return [];
	}
	public function hasMetadata(string $metadataKey) : bool{
		if($this->isValid()){
			return $this->level->getBlockMetadata()->hasMetadata($this, $metadataKey);
		}
		return false;
	}
	public function removeMetadata(string $metadataKey, Plugin $owningPlugin){
		if($this->isValid()){
			$this->level->getBlockMetadata()->removeMetadata($this, $metadataKey, $owningPlugin);
		}
	}
	public function getBlockProtocol(int $protocol) : ?Block{
		return null;
	}}