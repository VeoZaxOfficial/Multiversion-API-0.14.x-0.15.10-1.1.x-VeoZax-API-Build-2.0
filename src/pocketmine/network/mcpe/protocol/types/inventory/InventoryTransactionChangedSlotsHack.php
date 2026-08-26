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
namespace pocketmine\network\mcpe\protocol\types\inventory;
use pocketmine\network\mcpe\NetworkBinaryStream;use UnexpectedValueException;use function count;
final class InventoryTransactionChangedSlotsHack{
	private $containerId;
	private $changedSlotIndexes;
	public function __construct(int $containerId, array $changedSlotIndexes){
		$this->containerId = $containerId;
		$this->changedSlotIndexes = $changedSlotIndexes;
	}
	public function getContainerId() : int{ return $this->containerId; }
	public function getChangedSlotIndexes() : array{ return $this->changedSlotIndexes; }
	public static function read(NetworkBinaryStream $in) : self{
		$containerId = $in->getByte();
		$len = $in->getUnsignedVarInt();
		if($len >= 128){
			throw new UnexpectedValueException("Too many slots count in inventory transaction: " . $len);
		}
		$changedSlots = [];
		for($i = 0, $len; $i < $len; ++$i){
			$changedSlots[] = $in->getByte();
		}
		return new self($containerId, $changedSlots);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putByte($this->containerId);
		$out->putUnsignedVarInt(count($this->changedSlotIndexes));
		foreach($this->changedSlotIndexes as $index){
			$out->putByte($index);
		}
	}}