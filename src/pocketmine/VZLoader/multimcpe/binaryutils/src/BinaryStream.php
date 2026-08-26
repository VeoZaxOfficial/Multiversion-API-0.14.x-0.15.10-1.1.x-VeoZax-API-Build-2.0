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
namespace pocketmine\utils;
use function chr;use function ord;use function pack;use function round;use function strlen;use function substr;use function unpack;
class BinaryStream{
	public $offset;
	public $buffer;
	public function __construct(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}
	public function reset(){
		$this->buffer = "";
		$this->offset = 0;
	}
	public function rewind() : void{
		$this->offset = 0;
	}
	public function setOffset(int $offset) : void{
		$this->offset = $offset;
	}
	public function setBuffer(string $buffer = "", int $offset = 0){
		$this->buffer = $buffer;
		$this->offset = $offset;
	}
	public function getOffset() : int{
		return $this->offset;
	}
	public function getBuffer() : string{
		return $this->buffer;
	}
	public function get($len) : string{
		if($len === 0){
			return "";
		}
		$buflen = strlen($this->buffer);
		if($len === true){
			$str = substr($this->buffer, $this->offset);
			$this->offset = $buflen;
			return $str;
		}
		if($len < 0){
			$this->offset = $buflen - 1;
			return "";
		}
		$remaining = $buflen - $this->offset;
		if($remaining < $len){
			throw new BinaryDataException("Not enough bytes left in buffer: need $len, have $remaining");
		}
		return $len === 1 ? $this->buffer[$this->offset++] : substr($this->buffer, ($this->offset += $len) - $len, $len);
	}
	public function getRemaining() : string{
		$str = substr($this->buffer, $this->offset);
		if($str === false){
			throw new BinaryDataException("No bytes left to read");
		}
		$this->offset = strlen($this->buffer);
		return $str;
	}
	public function put(string $str){
		$this->buffer .= $str;
	}
	public function getBool() : bool{
		return $this->get(1) !== "\x00";
	}
	public function putBool(bool $v){
		$this->buffer .= ($v ? "\x01" : "\x00");
	}
	public function getByte() : int{
		return ord($this->get(1));
	}
	public function putByte(int $v){
		$this->buffer .= chr($v);
	}
	public function getShort() : int{
		return (unpack("n", $this->get(2))[1]);
	}
	public function getSignedShort() : int{
		return (unpack("n", $this->get(2))[1] << 48 >> 48);
	}
	public function putShort(int $v){
		$this->buffer .= (pack("n", $v));
	}
	public function getLShort() : int{
		return (unpack("v", $this->get(2))[1]);
	}
	public function getSignedLShort() : int{
		return (unpack("v", $this->get(2))[1] << 48 >> 48);
	}
	public function putLShort(int $v){
		$this->buffer .= (pack("v", $v));
	}
	public function getTriad() : int{
		return (unpack("N", "\x00" . $this->get(3))[1]);
	}
	public function putTriad(int $v){
		$this->buffer .= (substr(pack("N", $v), 1));
	}
	public function getLTriad() : int{
		return (unpack("V", $this->get(3) . "\x00")[1]);
	}
	public function putLTriad(int $v){
		$this->buffer .= (substr(pack("V", $v), 0, -1));
	}
	public function getInt() : int{
		return (unpack("N", $this->get(4))[1] << 32 >> 32);
	}
	public function putInt(int $v){
		$this->buffer .= (pack("N", $v));
	}
	public function getLInt() : int{
		return (unpack("V", $this->get(4))[1] << 32 >> 32);
	}
	public function putLInt(int $v){
		$this->buffer .= (pack("V", $v));
	}
	public function getFloat() : float{
		return (unpack("G", $this->get(4))[1]);
	}
	public function getRoundedFloat(int $accuracy) : float{
		return (round((unpack("G", $this->get(4))[1]),  $accuracy));
	}
	public function putFloat(float $v){
		$this->buffer .= (pack("G", $v));
	}
	public function getLFloat() : float{
		return (unpack("g", $this->get(4))[1]);
	}
	public function getRoundedLFloat(int $accuracy) : float{
		return (round((unpack("g", $this->get(4))[1]),  $accuracy));
	}
	public function putLFloat(float $v){
		$this->buffer .= (pack("g", $v));
	}
	public function getDouble() : float{
		return (unpack("E", $this->get(8))[1]);
	}
	public function putDouble(float $v) : void{
		$this->buffer .= (pack("E", $v));
	}
	public function getLDouble() : float{
		return (unpack("e", $this->get(8))[1]);
	}
	public function putLDouble(float $v) : void{
		$this->buffer .= (pack("e", $v));
	}
	public function getLong() : int{
		return Binary::readLong($this->get(8));
	}
	public function putLong(int $v){
		$this->buffer .= (pack("NN", $v >> 32, $v & 0xFFFFFFFF));
	}
	public function getLLong() : int{
		return Binary::readLLong($this->get(8));
	}
	public function putLLong(int $v){
		$this->buffer .= (pack("VV", $v & 0xFFFFFFFF, $v >> 32));
	}
	public function getUnsignedVarInt() : int{
		return Binary::readUnsignedVarInt($this->buffer, $this->offset);
	}
	public function putUnsignedVarInt(int $v){
		($this->buffer .= Binary::writeUnsignedVarInt($v));
	}
	public function getVarInt() : int{
		return Binary::readVarInt($this->buffer, $this->offset);
	}
	public function putVarInt(int $v){
		($this->buffer .= Binary::writeVarInt($v));
	}
	public function getUnsignedVarLong() : int{
		return Binary::readUnsignedVarLong($this->buffer, $this->offset);
	}
	public function putUnsignedVarLong(int $v){
		$this->buffer .= Binary::writeUnsignedVarLong($v);
	}
	public function getVarLong() : int{
		return Binary::readVarLong($this->buffer, $this->offset);
	}
	public function putVarLong(int $v){
		$this->buffer .= Binary::writeVarLong($v);
	}
	public function feof() : bool{
		return !isset($this->buffer[$this->offset]);
	}}