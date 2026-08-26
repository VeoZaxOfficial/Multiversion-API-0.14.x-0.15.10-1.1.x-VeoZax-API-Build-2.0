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
use InvalidArgumentException;use pocketmine\nbt\NBTStream;use pocketmine\nbt\ReaderTracker;use RuntimeException;use function get_class;use function str_repeat;use function strlen;
abstract class NamedTag{
	protected $__name;
	protected $cloning = false;
	public function __construct(string $name = ""){
		if(strlen($name) > 32767){
			throw new InvalidArgumentException("Tag name cannot be more than 32767 bytes, got length " . strlen($name));
		}
		$this->__name = $name;
	}
	public function getName() : string{
		return $this->__name;
	}
	public function setName(string $name) : void{
		$this->__name = $name;
	}
	abstract public function getValue();
	abstract public function getType() : int;
	abstract public function write(NBTStream $nbt) : void;
	abstract public function read(NBTStream $nbt, ReaderTracker $tracker) : void;
	public function __toString(){
		return $this->toString();
	}
	public function toString(int $indentation = 0) : string{
		return str_repeat("  ", $indentation) . get_class($this) . ": " . ($this->__name !== "" ? "name='$this->__name', " : "") . "value='" . (string) $this->getValue() . "'";
	}
	public function safeClone() : NamedTag{
		if($this->cloning){
			throw new RuntimeException("Recursive NBT tag dependency detected");
		}
		$this->cloning = true;
		$retval = clone $this;
		$this->cloning = false;
		$retval->cloning = false;
		return $retval;
	}
	public function equals(NamedTag $that) : bool{
		return $this->__name === $that->__name and $this->equalsValue($that);
	}
	protected function equalsValue(NamedTag $that) : bool{
		return $that instanceof $this and $this->getValue() === $that->getValue();
	}}