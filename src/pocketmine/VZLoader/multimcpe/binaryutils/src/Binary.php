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
use InvalidArgumentException;use function chr;use function define;use function defined;use function ord;use function pack;use function preg_replace;use function round;use function sprintf;use function substr;use function unpack;use const PHP_INT_MAX;
if(!defined("ENDIANNESS")){
	define("ENDIANNESS", (pack("s", 1) === "\0\1" ? Binary::BIG_ENDIAN : Binary::LITTLE_ENDIAN));}
class Binary{
	public const BIG_ENDIAN = 0x00;
	public const LITTLE_ENDIAN = 0x01;
	public static function signByte(int $value) : int{
		return $value << 56 >> 56;
	}
	public static function unsignByte(int $value) : int{
		return $value & 0xff;
	}
	public static function signShort(int $value) : int{
		return $value << 48 >> 48;
	}
	public static function unsignShort(int $value) : int{
		return $value & 0xffff;
	}
	public static function signInt(int $value) : int{
		return $value << 32 >> 32;
	}
	public static function unsignInt(int $value) : int{
		return $value & 0xffffffff;
	}
	public static function flipShortEndianness(int $value) : int{
		return self::readLShort(self::writeShort($value));
	}
	public static function flipIntEndianness(int $value) : int{
		return self::readLInt(self::writeInt($value));
	}
	public static function flipLongEndianness(int $value) : int{
		return self::readLLong(self::writeLong($value));
	}
	public static function readBool(string $b) : bool{
		return $b !== "\x00";
	}
	public static function writeBool(bool $b) : string{
		return $b ? "\x01" : "\x00";
	}
	public static function readByte(string $c) : int{
		return ord($c[0]);
	}
	public static function readSignedByte(string $c) : int{
		return self::signByte(ord($c[0]));
	}
	public static function writeByte(int $c) : string{
		return chr($c);
	}
	public static function readShort(string $str) : int{
		return unpack("n", $str)[1];
	}
	public static function readSignedShort(string $str) : int{
		return self::signShort(unpack("n", $str)[1]);
	}
	public static function writeShort(int $value) : string{
		return pack("n", $value);
	}
	public static function readLShort(string $str) : int{
		return unpack("v", $str)[1];
	}
	public static function readSignedLShort(string $str) : int{
		return self::signShort(unpack("v", $str)[1]);
	}
	public static function writeLShort(int $value) : string{
		return pack("v", $value);
	}
	public static function readTriad(string $str) : int{
		return unpack("N", "\x00" . $str)[1];
	}
	public static function writeTriad(int $value) : string{
		return substr(pack("N", $value), 1);
	}
	public static function readLTriad(string $str) : int{
		return unpack("V", $str . "\x00")[1];
	}
	public static function writeLTriad(int $value) : string{
		return substr(pack("V", $value), 0, -1);
	}
	public static function readInt(string $str) : int{
		return self::signInt(unpack("N", $str)[1]);
	}
	public static function writeInt(int $value) : string{
		return pack("N", $value);
	}
	public static function readLInt(string $str) : int{
		return self::signInt(unpack("V", $str)[1]);
	}
	public static function writeLInt(int $value) : string{
		return pack("V", $value);
	}
	public static function readFloat(string $str) : float{
		return unpack("G", $str)[1];
	}
	public static function readRoundedFloat(string $str, int $accuracy) : float{
		return round(self::readFloat($str), $accuracy);
	}
	public static function writeFloat(float $value) : string{
		return pack("G", $value);
	}
	public static function readLFloat(string $str) : float{
		return unpack("g", $str)[1];
	}
	public static function readRoundedLFloat(string $str, int $accuracy) : float{
		return round(self::readLFloat($str), $accuracy);
	}
	public static function writeLFloat(float $value) : string{
		return pack("g", $value);
	}
	public static function printFloat(float $value) : string{
		return preg_replace("/(\\.\\d+?)0+$/", "$1", sprintf("%F", $value));
	}
	public static function readDouble(string $str) : float{
		return unpack("E", $str)[1];
	}
	public static function writeDouble(float $value) : string{
		return pack("E", $value);
	}
	public static function readLDouble(string $str) : float{
		return unpack("e", $str)[1];
	}
	public static function writeLDouble(float $value) : string{
		return pack("e", $value);
	}
	public static function readLong(string $str) : int{
		return unpack("J", $str)[1];
	}
	public static function writeLong(int $value) : string{
		return pack("J", $value);
	}
	public static function readLLong(string $str) : int{
		return unpack("P", $str)[1];
	}
	public static function writeLLong(int $value) : string{
		return pack("P", $value);
	}
	public static function readVarInt(string $buffer, int &$offset) : int{
		$raw = self::readUnsignedVarInt($buffer, $offset);
		$temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
		return $temp ^ ($raw & (1 << 63));
	}
	public static function readUnsignedVarInt(string $buffer, int &$offset) : int{
		$value = 0;
		for($i = 0; $i <= 28; $i += 7){
			if(!isset($buffer[$offset])){
				throw new BinaryDataException("No bytes left in buffer");
			}
			$b = ord($buffer[$offset++]);
			$value |= (($b & 0x7f) << $i);
			if(($b & 0x80) === 0){
				return $value;
			}
		}
		throw new BinaryDataException("VarInt did not terminate after 5 bytes!");
	}
	public static function writeVarInt(int $v) : string{
		$v = ($v << 32 >> 32);
		return self::writeUnsignedVarInt(($v << 1) ^ ($v >> 31));
	}
	public static function writeUnsignedVarInt(int $value) : string{
		$buf = "";
		$value &= 0xffffffff;
		for($i = 0; $i < 5; ++$i){
			if(($value >> 7) !== 0){
				$buf .= chr($value | 0x80);
			}else{
				$buf .= chr($value & 0x7f);
				return $buf;
			}
			$value = (($value >> 7) & (PHP_INT_MAX >> 6)); 
		}
		throw new InvalidArgumentException("Value too large to be encoded as a VarInt");
	}
	public static function readVarLong(string $buffer, int &$offset) : int{
		$raw = self::readUnsignedVarLong($buffer, $offset);
		$temp = ((($raw << 63) >> 63) ^ $raw) >> 1;
		return $temp ^ ($raw & (1 << 63));
	}
	public static function readUnsignedVarLong(string $buffer, int &$offset) : int{
		$value = 0;
		for($i = 0; $i <= 63; $i += 7){
			if(!isset($buffer[$offset])){
				throw new BinaryDataException("No bytes left in buffer");
			}
			$b = ord($buffer[$offset++]);
			$value |= (($b & 0x7f) << $i);
			if(($b & 0x80) === 0){
				return $value;
			}
		}
		throw new BinaryDataException("VarLong did not terminate after 10 bytes!");
	}
	public static function writeVarLong(int $v) : string{
		return self::writeUnsignedVarLong(($v << 1) ^ ($v >> 63));
	}
	public static function writeUnsignedVarLong(int $value) : string{
		$buf = "";
		for($i = 0; $i < 10; ++$i){
			if(($value >> 7) !== 0){
				$buf .= chr($value | 0x80); 
			}else{
				$buf .= chr($value & 0x7f);
				return $buf;
			}
			$value = (($value >> 7) & (PHP_INT_MAX >> 6)); 
		}
		throw new InvalidArgumentException("Value too large to be encoded as a VarLong");
	}}