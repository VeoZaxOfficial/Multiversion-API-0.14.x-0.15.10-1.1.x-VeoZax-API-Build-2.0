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

namespace pocketmine\network\mcpe\protocol\legacy\p70;
use pocketmine\utils\p70\Binary;use pocketmine\entity\Attribute;
class UpdateAttributesPacket extends DataPacket{
	const NETWORK_ID = Info::UPDATE_ATTRIBUTES_PACKET;
	public $entityId;
	public $entries = [];
	public function decode(){
	}
	private const LEGACY_ATTRIBUTE_NAMES = [
		"minecraft:absorption"              => "generic.absorption",
		"minecraft:player.saturation"       => "player.saturation",
		"minecraft:player.exhaustion"       => "player.exhaustion",
		"minecraft:knockback_resistance"    => "generic.knockbackResistance",
		"minecraft:health"                  => "generic.health",
		"minecraft:movement"                => "generic.movementSpeed",
		"minecraft:follow_range"            => "generic.followRange",
		"minecraft:player.hunger"           => "player.hunger",
		"minecraft:attack_damage"           => "generic.attackDamage",
		"minecraft:player.level"            => "player.level",
		"minecraft:player.experience"       => "player.experience",
	];
	public function encode(){
		$this->buffer = chr(self::NETWORK_ID); $this->offset = 0;;
		$this->buffer .= Binary::writeLong($this->entityId);
		$body = "";
		$sentCount = 0;
		foreach($this->entries as $entry){
			$legacyName = self::LEGACY_ATTRIBUTE_NAMES[$entry->getName()] ?? null;
			if($legacyName === null){
				continue;
			}
			$body .= (ENDIANNESS === 0 ? pack("f", $entry->getMinValue()) : strrev(pack("f", $entry->getMinValue())));
			$body .= (ENDIANNESS === 0 ? pack("f", $entry->getMaxValue()) : strrev(pack("f", $entry->getMaxValue())));
			$body .= (ENDIANNESS === 0 ? pack("f", $entry->getValue()) : strrev(pack("f", $entry->getValue())));
			$nameLen = strlen($legacyName);
			$body .= pack("n", $nameLen) . $legacyName;
			$sentCount++;
		}
		$this->buffer .= pack("n", $sentCount);
		$this->buffer .= $body;
	}}