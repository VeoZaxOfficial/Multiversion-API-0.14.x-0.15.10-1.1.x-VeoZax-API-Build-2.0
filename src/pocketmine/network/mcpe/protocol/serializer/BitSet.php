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
namespace pocketmine\network\mcpe\protocol\serializer;
use pocketmine\network\mcpe\NetworkBinaryStream;use function array_pad;use function array_slice;use function array_values;use function count;use function intdiv;use InvalidArgumentException;
class BitSet{
	private const INT_BITS = PHP_INT_SIZE * 8;
	private const SHIFT = 7;
	public function __construct(
		private readonly int $length,
		private array $parts = []
	){
		$expectedPartsCount = self::getExpectedPartsCount($length);
		$partsCount = count($parts);
		if($partsCount > $expectedPartsCount){
			throw new InvalidArgumentException("Too many parts");
		}elseif($partsCount < $expectedPartsCount){
			$parts = array_pad($parts, $expectedPartsCount, 0);
		}
		$this->parts = array_values($parts);
	}
	public function get(int $index) : bool{
		[$partIndex, $bitIndex] = $this->getPartIndex($index);
		return ($this->parts[$partIndex] & (1 << $bitIndex)) !== 0;
	}
	public function set(int $index, bool $value) : void{
		[$partIndex, $bitIndex] = $this->getPartIndex($index);
		if($value){
			$this->parts[$partIndex] |= 1 << $bitIndex;
		}else{
			$this->parts[$partIndex] &= ~(1 << $bitIndex);
		}
	}
	private function getPartIndex(int $index) : array{
		if($index < 0 or $index >= $this->length){
			throw new InvalidArgumentException("Index out of bounds");
		}
		return [
			intdiv($index, self::INT_BITS),
			$index % self::INT_BITS
		];
	}
	public function getPartsCount() : int{
		return count($this->parts);
	}
	public function getParts() : array{
		return $this->parts;
	}
	private static function getExpectedPartsCount(int $length) : int{
		return intdiv($length + self::INT_BITS - 1, self::INT_BITS);
	}
	public static function read(NetworkBinaryStream $in, int $length) : self{
		$result = [0];
		$currentIndex = 0;
		$currentShift = 0;
		for($i = 0; $i < $length; $i += self::SHIFT){
			$b = $in->getByte();
			$bits = $b & 0x7f;
			$result[$currentIndex] |= $bits << $currentShift; 
			$nextShift = $currentShift + self::SHIFT;
			if($nextShift >= self::INT_BITS){
				$nextShift -= self::INT_BITS;
				$rightShift = self::SHIFT - $nextShift;
				$result[++$currentIndex] = $bits >> $rightShift;
			}
			$currentShift = $nextShift;
			if(($b & 0x80) === 0){
				return new self($length, array_slice($result, 0, self::getExpectedPartsCount($length)));
			}
		}
		return new self($length, array_slice($result, 0, self::getExpectedPartsCount($length)));
	}
	public function write(NetworkBinaryStream $out, ?int $length = null) : void{
		$parts = $this->parts;
		$length ??= $this->length;
		if($length > $this->length){
			throw new InvalidArgumentException("Cannot write more bits than the BitSet contains");
		}
		$currentIndex = 0;
		$currentShift = 0;
		for($i = 0; $i < $length; $i += self::SHIFT){
			$bits = $parts[$currentIndex] >> $currentShift;
			$nextShift = $currentShift + self::SHIFT;
			if($nextShift >= self::INT_BITS){
				$nextShift -= self::INT_BITS;
				$bits |= ($parts[++$currentIndex] ?? 0) << (self::SHIFT - $nextShift);
			}
			$currentShift = $nextShift;
			$last = $i + self::SHIFT >= $length;
			$bits |= $last ? 0 : 0x80;
			$out->putByte($bits);
			if($last){
				break;
			}
		}
	}
	public function getLength() : int{
		return $this->length;
	}}