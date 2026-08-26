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
use ArrayAccess;use Countable;use Iterator;use LogicException;use OutOfRangeException;use pocketmine\nbt\NBT;use pocketmine\nbt\NBTStream;use pocketmine\nbt\ReaderTracker;use SplDoublyLinkedList;use TypeError;use UnexpectedValueException;use function chr;use function get_class;use function gettype;use function is_object;use function ord;use function str_repeat;
class ListTag extends NamedTag implements ArrayAccess, Countable, Iterator{
	use NoDynamicFieldsTrait;
	private $tagType;
	private $value;
	public function __construct(string $name = "", array $value = [], int $tagType = NBT::TAG_End){
		parent::__construct($name);
		$this->tagType = $tagType;
		$this->value = new SplDoublyLinkedList();
		foreach($value as $tag){
			$this->push($tag);
		}
	}
	public function getValue() : array{
		$value = [];
		foreach($this->value as $k => $v){
			$value[$k] = $v;
		}
		return $value;
	}
	public function getAllValues() : array{
		$result = [];
		foreach($this->value as $tag){
			if($tag instanceof ArrayAccess){
				$result[] = $tag;
			}else{
				$result[] = $tag->getValue();
			}
		}
		return $result;
	}
	public function offsetExists($offset) : bool{
		return isset($this->value[$offset]);
	}
	public function offsetGet($offset){
		$value = $this->value[$offset] ?? null;
		if($value instanceof ArrayAccess){
			return $value;
		}elseif($value !== null){
			return $value->getValue();
		}
		return null;
	}
	public function offsetSet($offset, $value) : void{
		if($value instanceof NamedTag){
			$this->checkTagType($value);
			$this->value[$offset] = $value;
		}else{
			throw new TypeError("Value set by ArrayAccess must be an instance of " . NamedTag::class . ", got " . (is_object($value) ? " instance of " . get_class($value) : gettype($value)));
		}
	}
	public function offsetUnset($offset) : void{
		unset($this->value[$offset]);
	}
	public function count() : int{
		return $this->value->count();
	}
	public function getCount() : int{
		return $this->value->count();
	}
	public function push(NamedTag $tag) : void{
		$this->checkTagType($tag);
		$this->value->push($tag);
	}
	public function pop() : NamedTag{
		return $this->value->pop();
	}
	public function unshift(NamedTag $tag) : void{
		$this->checkTagType($tag);
		$this->value->unshift($tag);
	}
	public function shift() : NamedTag{
		return $this->value->shift();
	}
	public function insert(int $offset, NamedTag $tag){
		$this->checkTagType($tag);
		$this->value->add($offset, $tag);
	}
	public function remove(int $offset) : void{
		unset($this->value[$offset]);
	}
	public function get(int $offset) : NamedTag{
		return $this->value[$offset];
	}
	public function first() : NamedTag{
		return $this->value->bottom();
	}
	public function last() : NamedTag{
		return $this->value->top();
	}
	public function set(int $offset, NamedTag $tag) : void{
		$this->checkTagType($tag);
		$this->value[$offset] = $tag;
	}
	public function isset(int $offset) : bool{
		return isset($this->value[$offset]);
	}
	public function empty() : bool{
		return $this->value->isEmpty();
	}
	public function getType() : int{
		return NBT::TAG_List;
	}
	public function getTagType() : int{
		return $this->tagType;
	}
	public function setTagType(int $type){
		if(!$this->value->isEmpty()){
			throw new LogicException("Cannot change tag type of non-empty ListTag");
		}
		$this->tagType = $type;
	}
	private function checkTagType(NamedTag $tag) : void{
		$type = $tag->getType();
		if($type !== $this->tagType){
			if($this->tagType === NBT::TAG_End){
				$this->tagType = $type;
			}else{
				throw new TypeError("Invalid tag of type " . get_class($tag) . " assigned to ListTag, expected " . get_class(NBT::createTag($this->tagType)));
			}
		}
	}
	public function read(NBTStream $nbt, ReaderTracker $tracker) : void{
		$this->value = new SplDoublyLinkedList();
		$this->tagType = (ord($nbt->get(1)));
		$size = $nbt->getInt();
		if($size > 0){
			if($this->tagType === NBT::TAG_End){
				throw new UnexpectedValueException("Unexpected non-empty list of TAG_End");
			}
			$tracker->protectDepth(function() use($nbt, $tracker, $size){
				$tagBase = NBT::createTag($this->tagType);
				for($i = 0; $i < $size; ++$i){
					$tag = clone $tagBase;
					$tag->read($nbt, $tracker);
					$this->value->push($tag);
				}
			});
		}else{
			$this->tagType = NBT::TAG_End; 
		}
	}
	public function write(NBTStream $nbt) : void{
		($nbt->buffer .= chr($this->tagType));
		$nbt->putInt($this->value->count());
		foreach($this->value as $tag){
			$tag->write($nbt);
		}
	}
	public function toString(int $indentation = 0) : string{
		$str = str_repeat("  ", $indentation) . get_class($this) . ": " . ($this->__name !== "" ? "name='$this->__name', " : "") . "value={\n";
		foreach($this->value as $tag){
			$str .= $tag->toString($indentation + 1) . "\n";
		}
		return $str . str_repeat("  ", $indentation) . "}";
	}
	public function __clone(){
		$new = new SplDoublyLinkedList();
		foreach($this->value as $tag){
			$new->push($tag->safeClone());
		}
		$this->value = $new;
	}
	public function next() : void{
		$this->value->next();
	}
	public function valid() : bool{
		return $this->value->valid();
	}
	public function current() : ?NamedTag{
		return $this->value->current();
	}
	public function key() : int{
		return (int) $this->value->key();
	}
	public function rewind() : void{
		$this->value->rewind();
	}
	protected function equalsValue(NamedTag $that) : bool{
		if(!($that instanceof $this) or $this->count() !== $that->count()){
			return false;
		}
		foreach($this as $k => $v){
			$other = $that->get($k);
			if($other === null or !$v->equalsValue($other)){ 
				return false;
			}
		}
		return true;
	}}