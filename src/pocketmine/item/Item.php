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
namespace pocketmine\item;
use InvalidArgumentException;use JsonSerializable;use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\block\BlockToolType;use pocketmine\entity\Entity;use pocketmine\item\enchantment\Enchantment;use pocketmine\item\enchantment\EnchantmentInstance;use pocketmine\math\Vector3;use pocketmine\nbt\LittleEndianNBTStream;use pocketmine\nbt\NBT;use pocketmine\nbt\tag\ByteTag;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\NamedTag;use pocketmine\nbt\tag\ShortTag;use pocketmine\nbt\tag\StringTag;use pocketmine\network\mcpe\multiversion\inventory\ItemPalette;use pocketmine\network\mcpe\cache\CreativePacketCache;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use pocketmine\utils\Binary;use function array_diff;use function array_keys;use function array_map;use function base64_decode;use function base64_encode;use function file_get_contents;use function get_class;use function hex2bin;use function json_decode;use function krsort;use function scandir;use const DIRECTORY_SEPARATOR;use const pocketmine\RESOURCE_PATH;
class Item implements ItemIds, JsonSerializable{
	public const TAG_ENCH = "ench";
	public const TAG_DISPLAY = "display";
	public const TAG_BLOCK_ENTITY_TAG = "BlockEntityTag";
	public const TAG_DISPLAY_NAME = "Name";
	public const TAG_DISPLAY_LORE = "Lore";
	private static $cachedParser = null;
	private static function parseCompoundTag(string $tag) : CompoundTag{
		if($tag === ""){
			throw new InvalidArgumentException("No NBT data found in supplied string");
		}
		if(self::$cachedParser === null){
			self::$cachedParser = new LittleEndianNBTStream();
		}
		$data = self::$cachedParser->read($tag);
		if(!($data instanceof CompoundTag)){
			throw new InvalidArgumentException("Invalid item NBT string given, it could not be deserialized");
		}
		return $data;
	}
	private static function writeCompoundTag(CompoundTag $tag) : string{
		if(self::$cachedParser === null){
			self::$cachedParser = new LittleEndianNBTStream();
		}
		return self::$cachedParser->write($tag);
	}
	public static function get(int $id, int $meta = 0, int $count = 1, $tags = "") : Item{
		return ItemFactory::get($id, $meta, $count, $tags);
	}
	public static function fromString(string $str, bool $multiple = false){
		return ItemFactory::fromString($str, $multiple);
	}
	public static function clearCreativeItems(?int $protocol = null){
		CreativePacketCache::getInstance()->clearItems($protocol);
	}
    public static function getCreativeItems(int $protocol = ProtocolInfo::CURRENT_PROTOCOL) : array{
        $creativeItemEntries = CreativePacketCache::getInstance()->getItems($protocol);
        $items = [];
        foreach ($creativeItemEntries as $creativeItemEntry){
            $items[] = $creativeItemEntry->getItem();
        }
        return $items;
	}
    public static function addCreativeItem(Item $item, ?int $groupId = null, ?int $protocol = null){
        CreativePacketCache::getInstance()->addItem($item, $groupId, $protocol);
	}
	public static function removeCreativeItem(Item $item, ?int $protocol = null){
		CreativePacketCache::getInstance()->removeItem($item, $protocol);
	}
	public static function isCreativeItem(Item $item, int $protocol) : bool{
		return CreativePacketCache::getInstance()->getItemIndex($item, $protocol) !== -1;
	}
    public static function getCreativeItem(int $index, int $protocol = ProtocolInfo::CURRENT_PROTOCOL){
        $items = self::getCreativeItems($protocol);
        return $items[$index] ?? null;
	}
	public static function getCreativeItemIndex(Item $item, int $protocol) : int{
		return CreativePacketCache::getInstance()->getItemIndex($item, $protocol);
	}
    public static function getCreativeItemProtocols() : array{
        return CreativePacketCache::getInstance()->getCreativeItemProtocols();
    }
	protected $id;
	protected $meta;
	private $tags = "";
	private $cachedNBT = null;
	public $count = 1;
	protected $name;
	protected $onItemFrame = false;
	public function __construct(int $id, int $meta = 0, string $name = "Unknown"){
		if($id < -0x8000 or $id > 0x7fff){ 
			throw new InvalidArgumentException("ID must be in range " . -0x8000 . " - " . 0x7fff);
		}
		$this->id = $id;
		$this->setDamage($meta);
		$this->name = $name;
	}
	public function setCompoundTag($tags) : Item{
		if($tags instanceof CompoundTag){
			$this->setNamedTag($tags);
		}else{
			$this->tags = $tags === null ? "" : (string) $tags;
			$this->cachedNBT = null;
		}
		return $this;
	}
	public function getCompoundTag() : string{
		return $this->tags;
	}
	public function hasCompoundTag() : bool{
		return $this->tags !== "";
	}
	public function hasCustomBlockData() : bool{
		return $this->getNamedTagEntry(self::TAG_BLOCK_ENTITY_TAG) instanceof CompoundTag;
	}
	public function clearCustomBlockData(){
		$this->removeNamedTagEntry(self::TAG_BLOCK_ENTITY_TAG);
		return $this;
	}
	public function setCustomBlockData(CompoundTag $compound) : Item{
		$tags = clone $compound;
		$tags->setName(self::TAG_BLOCK_ENTITY_TAG);
		$this->setNamedTagEntry($tags);
		return $this;
	}
	public function getCustomBlockData() : ?CompoundTag{
		$tag = $this->getNamedTagEntry(self::TAG_BLOCK_ENTITY_TAG);
		return $tag instanceof CompoundTag ? $tag : null;
	}
	public function hasEnchantments() : bool{
		return $this->getNamedTagEntry(self::TAG_ENCH) instanceof ListTag;
	}
	public function hasEnchantment(int $id, int $level = -1) : bool{
		$ench = $this->getNamedTagEntry(self::TAG_ENCH);
		if(!($ench instanceof ListTag)){
			return false;
		}
		foreach($ench as $entry){
			if($entry->getShort("id") === $id and ($level === -1 or $entry->getShort("lvl") === $level)){
				return true;
			}
		}
		return false;
	}
	public function getEnchantment(int $id) : ?EnchantmentInstance{
		$ench = $this->getNamedTagEntry(self::TAG_ENCH);
		if(!($ench instanceof ListTag)){
			return null;
		}
		foreach($ench as $entry){
			if($entry->getShort("id") === $id){
				$e = Enchantment::getEnchantment($entry->getShort("id"));
				if($e !== null){
					return new EnchantmentInstance($e, $entry->getShort("lvl"));
				}
			}
		}
		return null;
	}
	public function removeEnchantment(int $id, int $level = -1) : void{
		$ench = $this->getNamedTagEntry(self::TAG_ENCH);
		if(!($ench instanceof ListTag)){
			return;
		}
		foreach($ench as $k => $entry){
			if($entry->getShort("id") === $id and ($level === -1 or $entry->getShort("lvl") === $level)){
				$ench->remove($k);
				break;
			}
		}
		$this->setNamedTagEntry($ench);
	}
	public function removeEnchantments() : void{
		$this->removeNamedTagEntry(self::TAG_ENCH);
	}
	public function addEnchantment(EnchantmentInstance $enchantment) : void{
		$found = false;
		$ench = $this->getNamedTagEntry(self::TAG_ENCH);
		if(!($ench instanceof ListTag)){
			$ench = new ListTag(self::TAG_ENCH, [], NBT::TAG_Compound);
		}else{
			foreach($ench as $k => $entry){
				if($entry->getShort("id") === $enchantment->getId()){
					$ench->set($k, new CompoundTag("", [
						new ShortTag("id", $enchantment->getId()),
						new ShortTag("lvl", $enchantment->getLevel())
					]));
					$found = true;
					break;
				}
			}
		}
		if(!$found){
			$ench->push(new CompoundTag("", [
				new ShortTag("id", $enchantment->getId()),
				new ShortTag("lvl", $enchantment->getLevel())
			]));
		}
		$this->setNamedTagEntry($ench);
	}
	public function getEnchantments() : array{
		$enchantments = [];
		$ench = $this->getNamedTagEntry(self::TAG_ENCH);
		if($ench instanceof ListTag){
			foreach($ench as $entry){
				$e = Enchantment::getEnchantment($entry->getShort("id"));
				if($e !== null){
					$enchantments[] = new EnchantmentInstance($e, $entry->getShort("lvl"));
				}
			}
		}
		return $enchantments;
	}
	public function getEnchantmentLevel(int $enchantmentId) : int{
		$ench = $this->getNamedTag()->getListTag(self::TAG_ENCH);
		if($ench !== null){
			foreach($ench as $entry){
				if($entry->getShort("id") === $enchantmentId){
					return $entry->getShort("lvl");
				}
			}
		}
		return 0;
	}
	public function hasCustomName() : bool{
		$display = $this->getNamedTagEntry(self::TAG_DISPLAY);
		if($display instanceof CompoundTag){
			return $display->hasTag(self::TAG_DISPLAY_NAME);
		}
		return false;
	}
	public function getCustomName() : string{
		$display = $this->getNamedTagEntry(self::TAG_DISPLAY);
		if($display instanceof CompoundTag){
			return $display->getString(self::TAG_DISPLAY_NAME, "");
		}
		return "";
	}
	public function setCustomName(string $name) : Item{
		if($name === ""){
			return $this->clearCustomName();
		}
		$display = $this->getNamedTagEntry(self::TAG_DISPLAY);
		if(!($display instanceof CompoundTag)){
			$display = new CompoundTag(self::TAG_DISPLAY);
		}
		$display->setString(self::TAG_DISPLAY_NAME, $name);
		$this->setNamedTagEntry($display);
		return $this;
	}
	public function clearCustomName() : Item{
		$display = $this->getNamedTagEntry(self::TAG_DISPLAY);
		if($display instanceof CompoundTag){
			$display->removeTag(self::TAG_DISPLAY_NAME);
			if($display->getCount() === 0){
				$this->removeNamedTagEntry($display->getName());
			}else{
				$this->setNamedTagEntry($display);
			}
		}
		return $this;
	}
	public function getLore() : array{
		$display = $this->getNamedTagEntry(self::TAG_DISPLAY);
		if($display instanceof CompoundTag and ($lore = $display->getListTag(self::TAG_DISPLAY_LORE)) !== null){
			return $lore->getAllValues();
		}
		return [];
	}
	public function setLore(array $lines) : Item{
		$display = $this->getNamedTagEntry(self::TAG_DISPLAY);
		if(!($display instanceof CompoundTag)){
			$display = new CompoundTag(self::TAG_DISPLAY, []);
		}
		$display->setTag(new ListTag(self::TAG_DISPLAY_LORE, array_map(function(string $str) : StringTag{
			return new StringTag("", $str);
		}, $lines), NBT::TAG_String));
		$this->setNamedTagEntry($display);
		return $this;
	}
	public function getNamedTagEntry(string $name) : ?NamedTag{
		return $this->getNamedTag()->getTag($name);
	}
	public function setNamedTagEntry(NamedTag $new) : void{
		$tag = $this->getNamedTag();
		$tag->setTag($new);
		$this->setNamedTag($tag);
	}
	public function removeNamedTagEntry(string $name) : void{
		$tag = $this->getNamedTag();
		$tag->removeTag($name);
		$this->setNamedTag($tag);
	}
	public function getNamedTag() : CompoundTag{
		if(!$this->hasCompoundTag() and $this->cachedNBT === null){
			$this->cachedNBT = new CompoundTag();
		}
		return $this->cachedNBT ?? ($this->cachedNBT = self::parseCompoundTag($this->tags));
	}
    public function hasNamedTag() : bool{
        return $this->getNamedTag()->count() > 0;
    }
	public function setNamedTag(CompoundTag $tag) : Item{
		if($tag->getCount() === 0){
			return $this->clearNamedTag();
		}
		$this->cachedNBT = $tag;
		$this->tags = self::writeCompoundTag($tag);
		return $this;
	}
	public function clearNamedTag() : Item{
		return $this->setCompoundTag("");
	}
	public function getCount() : int{
		return $this->count;
	}
	public function setCount(int $count) : Item{
		$this->count = $count;
		return $this;
	}
	public function pop(int $count = 1) : Item{
		if($count > $this->count){
			throw new InvalidArgumentException("Cannot pop $count items from a stack of $this->count");
		}
		$item = clone $this;
		$item->count = $count;
		$this->count -= $count;
		return $item;
	}
	public function isNull() : bool{
		return $this->count <= 0 or $this->id === Item::AIR;
	}
	final public function getName() : string{
		return $this->hasCustomName() ? $this->getCustomName() : $this->getVanillaName();
	}
	public function getVanillaName() : string{
		return $this->name;
	}
	final public function canBePlaced() : bool{
		return $this->getBlock()->canBePlaced();
	}
	public function getBlock() : Block{
		return BlockFactory::get(self::AIR);
	}
	final public function getId() : int{
		return $this->id;
	}
	final public function getDamage() : int{
		return $this->meta;
	}
	public function setDamage(int $meta) : Item{
		$this->meta = $meta !== -1 ? $meta & 0x7FFF : -1;
		return $this;
	}
	public function hasAnyDamageValue() : bool{
		return $this->meta === -1;
	}
	public function getMaxStackSize() : int{
		return ($this->getBlock()->getId() === self::SHULKER_BOX ? 1 : 64);
	}
	public function getFuelTime() : int{
		return 0;
	}
	public function getAttackPoints() : int{
		return 1;
	}
	public function getDefensePoints() : int{
		return 0;
	}
	public function getBlockToolType() : int{
		return BlockToolType::TYPE_NONE;
	}
	public function getBlockToolHarvestLevel() : int{
		return 0;
	}
	public function getMiningEfficiency(Block $block) : float{
		return 1;
	}
	public function onUpdate(Player $player) : void{
	}
	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : bool{
		return false;
	}
	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		return false;
	}
	public function onReleaseUsing(Player $player) : bool{
		return false;
	}
	public function onDestroyBlock(Block $block) : bool{
		return false;
	}
	public function onAttackEntity(Entity $victim) : bool{
		return false;
	}
	public function onInteractWithEntity(Player $player, Entity $entity) : bool{
		return false;
	}
	public function getCooldownTicks() : int{
		return 0;
	}
	final public function equals(Item $item, bool $checkDamage = true, bool $checkCompound = true) : bool{
		if($this->id === $item->getId() and (!$checkDamage or $this->getDamage() === $item->getDamage())){
			if($checkCompound){
				if($item->getCompoundTag() === $this->getCompoundTag()){
					return true;
				}elseif($this->hasCompoundTag() and $item->hasCompoundTag()){
					return $this->getNamedTag()->equals($item->getNamedTag());
				}
			}else{
				return true;
			}
		}
		return false;
	}
	final public function canStackWith(Item $other) : bool{
		return $this->equals($other, true, true);
	}
	final public function equalsExact(Item $other) : bool{
		return $this->equals($other, true, true) and $this->count === $other->count;
	}
	final public function __toString() : string{
		return "Item " . $this->name . " (" . $this->id . ":" . ($this->hasAnyDamageValue() ? "?" : $this->meta) . ")x" . $this->count . ($this->hasCompoundTag() ? " tags:" . base64_encode($this->getCompoundTag()) : "");
	}
	final public function jsonSerialize() : array{
		$data = [
			"id" => $this->getId()
		];
		if($this->getDamage() !== 0){
			$data["damage"] = $this->getDamage();
		}
		if($this->getCount() !== 1){
			$data["count"] = $this->getCount();
		}
		if($this->hasCompoundTag()){
			$data["nbt_b64"] = base64_encode($this->getCompoundTag());
		}
		return $data;
	}
	final public static function jsonDeserialize(array $data) : Item{
		$nbt = "";
		if(isset($data["nbt"])){
			$nbt = $data["nbt"];
		}elseif(isset($data["nbt_hex"])){
			$nbt = hex2bin($data["nbt_hex"]);
		}elseif(isset($data["nbt_b64"])){
			$nbt = base64_decode($data["nbt_b64"], true);
		}
		return ItemFactory::get(
			(int) $data["id"],
			(int) ($data["damage"] ?? 0),
			(int) ($data["count"] ?? 1),
			(string) $nbt
		);
	}
	public function nbtSerialize(int $slot = -1, string $tagName = "", ?int $playerProtocol = null) : CompoundTag{
		$id = $this->id;
		$meta = $this->meta;
		$idTag = new ShortTag("id", $id);
		$result = new CompoundTag($tagName, [
			$idTag,
			new ByteTag("Count", Binary::signByte($this->count)),
			new ShortTag("Damage", $meta)
		]);
		if($this->hasCompoundTag()){
			$itemNBT = clone $this->getNamedTag();
			$itemNBT->setName("tag");
			$result->setTag($itemNBT);
		}
		if($slot !== -1){
			$result->setByte("Slot", $slot);
		}
		return $result;
	}
	public static function nbtDeserialize(CompoundTag $tag) : Item{
		if(!$tag->hasTag("id") or !$tag->hasTag("Count")){
			return ItemFactory::get(0);
		}
		$count = Binary::unsignByte($tag->getByte("Count"));
		$meta = $tag->getShort("Damage", 0);
		$idTag = $tag->getTag("id");
		if($idTag instanceof ShortTag){
			$item = ItemFactory::get($idTag->getValue(), $meta, $count);
		}elseif($idTag instanceof StringTag){ 
			try{
				$item = ItemFactory::fromString($idTag->getValue());
			}catch(InvalidArgumentException $e){
				return ItemFactory::get(Item::AIR, 0, 0);
			}
			$item->setDamage($meta);
			$item->setCount($count);
		}else{
			throw new InvalidArgumentException("Item CompoundTag ID must be an instance of StringTag or ShortTag, " . get_class($idTag) . " given");
		}
		$itemNBT = $tag->getCompoundTag("tag");
		if($itemNBT instanceof CompoundTag){
			$t = clone $itemNBT;
			$t->setName("");
			$item->setNamedTag($t);
		}
		return $item;
	}
	public function __clone(){
		$this->cachedNBT = null;
	}
	public function isOnItemFrame() : bool{
		return $this->onItemFrame;
	}
	public function setOnItemFrame(bool $onItemFrame) : void{
		$this->onItemFrame = $onItemFrame;
	}
    public function getItemProtocol(int $protocol) : ?Item{
        return null;
    }}