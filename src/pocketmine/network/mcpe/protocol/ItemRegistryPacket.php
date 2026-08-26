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
namespace pocketmine\network\mcpe\protocol;
use pocketmine\nbt\NetworkLittleEndianNBTStream;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;use function count;
class ItemRegistryPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ITEM_REGISTRY_PACKET;
	private $entries;
	public static function create(array $entries) : self{
		$result = new self;
		$result->entries = $entries;
		return $result;
	}
	public function getEntries() : array{ return $this->entries; }
	protected function decodePayload() : void{
		$this->entries = [];
		for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
			$stringId = $this->getString();
			$numericId = $this->getSignedLShort();
			$isComponentBased = $this->getBool();
			$version = $this->getVarInt();
			$nbt = $this->getNbtCompoundRoot();
			$this->entries[] = new ItemTypeEntry($stringId, $numericId, $isComponentBased, $version, $nbt);
		}
	}
	protected function encodePayload() : void{
		$this->putUnsignedVarInt(count($this->entries));
		foreach($this->entries as $entry){
			$this->putString($entry->getStringId());
			$this->putLShort($entry->getNumericId());
			$this->putBool($entry->isComponentBased());
			$this->putVarInt($entry->getVersion());
			$this->put((new NetworkLittleEndianNBTStream())->write($entry->getComponentNbt()));
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleItemRegistry($this);
	}}