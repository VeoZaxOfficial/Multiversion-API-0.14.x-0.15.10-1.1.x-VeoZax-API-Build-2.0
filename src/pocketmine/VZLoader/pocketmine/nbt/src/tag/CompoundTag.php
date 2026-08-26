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
namespace pocketmine\nbt\tag;
use ArrayAccess;use Countable;use InvalidArgumentException;use Iterator;use pocketmine\nbt\NBT;use pocketmine\nbt\NBTStream;use pocketmine\nbt\ReaderTracker;use RuntimeException;use TypeError;use UnexpectedValueException;use function assert;use function count;use function current;use function get_class;use function gettype;use function is_a;use function is_int;use function is_object;use function key;use function next;use function reset;use function str_repeat;
class CompoundTag extends NamedTag implements ArrayAccess, Iterator, Countable{
	use NoDynamicFieldsTrait;
	private $value = [];
	public function __construct(string $name = "", array $value = []){
		parent::__construct($name);
		foreach($value as $tag){
			$this->setTag($tag);
		}
	}
	public function count() : int{
		return count($this->value);
	}
	public function getCount(){
		return count($this->value);
	}
	public function getValue(){
		return $this->value;
	}
	public function getTag(string $name, string $expectedClass = NamedTag::class) : ?NamedTag{
		assert(is_a($expectedClass, NamedTag::class, true));
		$tag = $this->value[$name] ?? null;
		if($tag !== null and !($tag instanceof $expectedClass)){
			throw new RuntimeException("Expected a tag of type $expectedClass, got " . get_class($tag));
		}
		return $tag;
	}
	public function getListTag(string $name) : ?ListTag{
		return $this->getTag($name, ListTag::class);
	}
	public function getCompoundTag(string $name) : ?CompoundTag{
		return $this->getTag($name, CompoundTag::class);
	}
	public function setTag(NamedTag $tag, bool $force = false) : void{
		if(!$force){
			$existing = $this->value[$tag->__name] ?? null;
			if($existing !== null and !($tag instanceof $existing)){
				throw new RuntimeException("Cannot set tag at \"$tag->__name\": tried to overwrite " . get_class($existing) . " with " . get_class($tag));
			}
		}
		$this->value[$tag->__name] = $tag;
	}
	public function removeTag(string ...$names) : void{
		foreach($names as $name){
			unset($this->value[$name]);
		}
	}
	public function hasTag(string $name, string $expectedClass = NamedTag::class) : bool{
		assert(is_a($expectedClass, NamedTag::class, true));
		return ($this->value[$name] ?? null) instanceof $expectedClass;
	}
	public function getTagValue(string $name, string $expectedClass, $default = null, bool $badTagDefault = false){
		$tag = $this->getTag($name, $badTagDefault ? NamedTag::class : $expectedClass);
		if($tag instanceof $expectedClass){
			return $tag->getValue();
		}
		if($default === null){
			throw new RuntimeException("Tag with name \"$name\" " . ($tag !== null ? "not of expected type" : "not found") . " and no valid default value given");
		}
		return $default;
	}
	public function getByte(string $name, ?int $default = null, bool $badTagDefault = false) : int{
		return $this->getTagValue($name, ByteTag::class, $default, $badTagDefault);
	}
	public function getShort(string $name, ?int $default = null, bool $badTagDefault = false) : int{
		return $this->getTagValue($name, ShortTag::class, $default, $badTagDefault);
	}
	public function getInt(string $name, ?int $default = null, bool $badTagDefault = false) : int{
		return $this->getTagValue($name, IntTag::class, $default, $badTagDefault);
	}
	public function getLong(string $name, ?int $default = null, bool $badTagDefault = false) : int{
		return $this->getTagValue($name, LongTag::class, $default, $badTagDefault);
	}
	public function getFloat(string $name, ?float $default = null, bool $badTagDefault = false) : float{
		return $this->getTagValue($name, FloatTag::class, $default, $badTagDefault);
	}
	public function getDouble(string $name, ?float $default = null, bool $badTagDefault = false) : float{
		return $this->getTagValue($name, DoubleTag::class, $default, $badTagDefault);
	}
	public function getByteArray(string $name, ?string $default = null, bool $badTagDefault = false) : string{
		return $this->getTagValue($name, ByteArrayTag::class, $default, $badTagDefault);
	}
	public function getString(string $name, ?string $default = null, bool $badTagDefault = false) : string{
		return $this->getTagValue($name, StringTag::class, $default, $badTagDefault);
	}
	public function getIntArray(string $name, ?array $default = null, bool $badTagDefault = false) : array{
		return $this->getTagValue($name, IntArrayTag::class, $default, $badTagDefault);
	}
	public function setByte(string $name, int $value, bool $force = false) : void{
		$this->setTag(new ByteTag($name, $value), $force);
	}
	public function setShort(string $name, int $value, bool $force = false) : void{
		$this->setTag(new ShortTag($name, $value), $force);
	}
	public function setInt(string $name, int $value, bool $force = false) : void{
		$this->setTag(new IntTag($name, $value), $force);
	}
	public function setLong(string $name, int $value, bool $force = false) : void{
		$this->setTag(new LongTag($name, $value), $force);
	}
	public function setFloat(string $name, float $value, bool $force = false) : void{
		$this->setTag(new FloatTag($name, $value), $force);
	}
	public function setDouble(string $name, float $value, bool $force = false) : void{
		$this->setTag(new DoubleTag($name, $value), $force);
	}
	public function setByteArray(string $name, string $value, bool $force = false) : void{
		$this->setTag(new ByteArrayTag($name, $value), $force);
	}
	public function setString(string $name, string $value, bool $force = false) : void{
		$this->setTag(new StringTag($name, $value), $force);
	}
	public function setIntArray(string $name, array $value, bool $force = false) : void{
		$this->setTag(new IntArrayTag($name, $value), $force);
	}
	public function offsetExists($offset){
		return isset($this->value[$offset]);
	}
	public function offsetGet($offset){
		if(isset($this->value[$offset])){
			if($this->value[$offset] instanceof ArrayAccess){
				return $this->value[$offset];
			}else{
				return $this->value[$offset]->getValue();
			}
		}
		assert(false, "Offset $offset not found");
		return null;
	}
	public function offsetSet($offset, $value){
		if($offset === null){
			throw new InvalidArgumentException("Array access push syntax is not supported");
		}
		if($value instanceof NamedTag){
			if($offset !== $value->getName()){
				throw new UnexpectedValueException("Given tag has a name which does not match the offset given (offset: \"$offset\", tag name: \"" . $value->getName() . "\")");
			}
			$this->value[$offset] = $value;
		}else{
			throw new TypeError("Value set by ArrayAccess must be an instance of " . NamedTag::class . ", got " . (is_object($value) ? " instance of " . get_class($value) : gettype($value)));
		}
	}
	public function offsetUnset($offset){
		unset($this->value[$offset]);
	}
	public function getType() : int{
		return NBT::TAG_Compound;
	}
	public function read(NBTStream $nbt, ReaderTracker $tracker) : void{
		$this->value = [];
		$tracker->protectDepth(function() use($nbt, $tracker){
			do{
				$tag = $nbt->readTag($tracker);
				if($tag !== null and $tag->__name !== ""){
					$this->value[$tag->__name] = $tag;
				}
			}while($tag !== null);
		});
	}
	public function write(NBTStream $nbt) : void{
		foreach($this->value as $tag){
			$nbt->writeTag($tag);
		}
		$nbt->writeEnd();
	}
	public function toString(int $indentation = 0) : string{
		$str = str_repeat("  ", $indentation) . get_class($this) . ": " . ($this->__name !== "" ? "name='$this->__name', " : "") . "value={\n";
		foreach($this->value as $tag){
			$str .= $tag->toString($indentation + 1) . "\n";
		}
		return $str . str_repeat("  ", $indentation) . "}";
	}
	public function __clone(){
		foreach($this->value as $key => $tag){
			$this->value[$key] = $tag->safeClone();
		}
	}
	public function next() : void{
		next($this->value);
	}
	public function valid() : bool{
		return key($this->value) !== null;
	}
	public function key() : ?string{
		$k = key($this->value);
		if(is_int($k)){
			$k = (string) $k;
		}
		return $k;
	}
	public function current() : ?NamedTag{
		return current($this->value) ?: null;
	}
	public function rewind() : void{
		reset($this->value);
	}
	protected function equalsValue(NamedTag $that) : bool{
		if(!($that instanceof $this) or $this->count() !== $that->count()){
			return false;
		}
		foreach($this as $k => $v){
			$other = $that->getTag($k);
			if($other === null or !$v->equals($other)){
				return false;
			}
		}
		return true;
	}
	public function merge(CompoundTag $other) : CompoundTag{
		$new = clone $this;
		foreach($other as $namedTag){
			$new->setTag(clone $namedTag);
		}
		return $new;
	}}