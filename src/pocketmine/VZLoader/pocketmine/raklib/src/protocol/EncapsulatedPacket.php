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
namespace raklib\protocol;
use function ceil;use function chr;use function ord;use function pack;use function strlen;use function substr;
use function unpack;
class EncapsulatedPacket{
	private const RELIABILITY_SHIFT = 5;
	private const RELIABILITY_FLAGS = 0b111 << self::RELIABILITY_SHIFT;
	private const SPLIT_FLAG = 0b00010000;
	public $reliability;
	public $hasSplit = false;
	public $length = 0;
	public $messageIndex;
	public $sequenceIndex;
	public $orderIndex;
	public $orderChannel;
	public $splitCount;
	public $splitID;
	public $splitIndex;
	public $buffer = "";
	public $needACK = false;
	public $identifierACK;
	public static function fromInternalBinary(string $bytes, ?int &$offset = null) : EncapsulatedPacket{
		$packet = new EncapsulatedPacket();
		$offset = 0;
		$packet->reliability = ord($bytes[$offset++]);
		$length = (unpack("N", substr($bytes, $offset, 4))[1] << 32 >> 32);
		$offset += 4;
		$packet->identifierACK = (unpack("N", substr($bytes, $offset, 4))[1] << 32 >> 32); 
		$offset += 4;
		if(PacketReliability::isSequencedOrOrdered($packet->reliability)){
			$packet->orderChannel = ord($bytes[$offset++]);
		}
		$packet->buffer = substr($bytes, $offset, $length);
		$offset += $length;
		return $packet;
	}
	public function toInternalBinary() : string{
		return
			chr($this->reliability) .
			(pack("N", strlen($this->buffer))) .
			(pack("N", $this->identifierACK ?? -1)) . 
			(PacketReliability::isSequencedOrOrdered($this->reliability) ? chr($this->orderChannel) : "") .
			$this->buffer;
	}
	public static function fromBinary(string $binary, ?int &$offset = null) : EncapsulatedPacket{
		$packet = new EncapsulatedPacket();
		$flags = ord($binary[0]);
		$packet->reliability = $reliability = ($flags & self::RELIABILITY_FLAGS) >> self::RELIABILITY_SHIFT;
		$packet->hasSplit = $hasSplit = ($flags & self::SPLIT_FLAG) > 0;
		$length = (int) ceil((unpack("n", substr($binary, 1, 2))[1]) / 8);
		$offset = 3;
		if($reliability > PacketReliability::UNRELIABLE){
			if(PacketReliability::isReliable($reliability)){
				$packet->messageIndex = (unpack("V", substr($binary, $offset, 3) . "\x00")[1]);
				$offset += 3;
			}
			if(PacketReliability::isSequenced($reliability)){
				$packet->sequenceIndex = (unpack("V", substr($binary, $offset, 3) . "\x00")[1]);
				$offset += 3;
			}
			if(PacketReliability::isSequencedOrOrdered($reliability)){
				$packet->orderIndex = (unpack("V", substr($binary, $offset, 3) . "\x00")[1]);
				$offset += 3;
				$packet->orderChannel = ord($binary[$offset++]);
			}
		}
		if($hasSplit){
			$packet->splitCount = (unpack("N", substr($binary, $offset, 4))[1] << 32 >> 32);
			$offset += 4;
			$packet->splitID = (unpack("n", substr($binary, $offset, 2))[1]);
			$offset += 2;
			$packet->splitIndex = (unpack("N", substr($binary, $offset, 4))[1] << 32 >> 32);
			$offset += 4;
		}
		$packet->buffer = substr($binary, $offset, $length);
		$offset += $length;
		return $packet;
	}
	public function toBinary() : string{
		return
			chr(($this->reliability << self::RELIABILITY_SHIFT) | ($this->hasSplit ? self::SPLIT_FLAG : 0)) .
			(pack("n", strlen($this->buffer) << 3)) .
			($this->reliability > PacketReliability::UNRELIABLE ?
				(PacketReliability::isReliable($this->reliability) ? (substr(pack("V", $this->messageIndex), 0, -1)) : "") .
				(PacketReliability::isSequenced($this->reliability) ? (substr(pack("V", $this->sequenceIndex), 0, -1)) : "") .
				(PacketReliability::isSequencedOrOrdered($this->reliability) ? (substr(pack("V", $this->orderIndex), 0, -1)) . chr($this->orderChannel) : "")
				: ""
			) .
			($this->hasSplit ? (pack("N", $this->splitCount)) . (pack("n", $this->splitID)) . (pack("N", $this->splitIndex)) : "")
			. $this->buffer;
	}
	public function getTotalLength() : int{
		return 3 + strlen($this->buffer) + ($this->messageIndex !== null ? 3 : 0) + ($this->orderIndex !== null ? 4 : 0) + ($this->hasSplit ? 10 : 0);
	}
	public function __toString() : string{
		return $this->toBinary();
	}}