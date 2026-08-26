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
namespace pocketmine\network\mcpe\protocol\types\inventory\stackresponse;
use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\ProtocolInfo;
final class ItemStackResponseSlotInfo{
	private $slot;
	private $hotbarSlot;
	private $count;
	private $itemStackId;
	private $customName;
	private $filteredCustomName;
	private $durabilityCorrection;
	public function __construct(int $slot, int $hotbarSlot, int $count, int $itemStackId, string $customName, string $filteredCustomName, int $durabilityCorrection){
		$this->slot = $slot;
		$this->hotbarSlot = $hotbarSlot;
		$this->count = $count;
		$this->itemStackId = $itemStackId;
		$this->customName = $customName;
		$this->filteredCustomName = $filteredCustomName;
		$this->durabilityCorrection = $durabilityCorrection;
	}
	public function getSlot() : int{ return $this->slot; }
	public function getHotbarSlot() : int{ return $this->hotbarSlot; }
	public function getCount() : int{ return $this->count; }
	public function getItemStackId() : int{ return $this->itemStackId; }
	public function getCustomName() : string{ return $this->customName; }
    public function getFilteredCustomName() : string{ return $this->filteredCustomName; }
	public function getDurabilityCorrection() : int{ return $this->durabilityCorrection; }
	public static function read(NetworkBinaryStream $in) : self{
		$slot = $in->getByte();
		$hotbarSlot = $in->getByte();
		$count = $in->getByte();
		$itemStackId = $in->readServerItemStackId();
		
		return new self($slot, $hotbarSlot, $count, $itemStackId, $customName ?? "", $filteredCustomName ?? "", $durabilityCorrection ?? 0);
	}
	public function write(NetworkBinaryStream $out) : void{
		$out->putByte($this->slot);
		$out->putByte($this->hotbarSlot);
		$out->putByte($this->count);
		$out->writeServerItemStackId($this->itemStackId);
		
	}}