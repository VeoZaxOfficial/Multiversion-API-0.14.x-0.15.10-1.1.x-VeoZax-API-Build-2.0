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
use pocketmine\network\mcpe\NetworkSession;
class ChangeMobPropertyPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CHANGE_MOB_PROPERTY_PACKET;
	private int $actorUniqueId;
	private string $propertyName;
	private bool $boolValue;
	private string $stringValue;
	private int $intValue;
	private float $floatValue;
	private static function create(int $actorUniqueId, string $propertyName, bool $boolValue, string $stringValue, int $intValue, float $floatValue) : self{
		$result = new self;
		$result->actorUniqueId = $actorUniqueId;
		$result->propertyName = $propertyName;
		$result->boolValue = $boolValue;
		$result->stringValue = $stringValue;
		$result->intValue = $intValue;
		$result->floatValue = $floatValue;
		return $result;
	}
	public static function boolValue(int $actorUniqueId, string $propertyName, bool $value) : self{
		return self::create($actorUniqueId, $propertyName, $value, "", 0, 0);
	}
	public static function stringValue(int $actorUniqueId, string $propertyName, string $value) : self{
		return self::create($actorUniqueId, $propertyName, false, $value, 0, 0);
	}
	public static function intValue(int $actorUniqueId, string $propertyName, int $value) : self{
		return self::create($actorUniqueId, $propertyName, false, "", $value, 0);
	}
	public static function floatValue(int $actorUniqueId, string $propertyName, float $value) : self{
		return self::create($actorUniqueId, $propertyName, false, "", 0, $value);
	}
	public function getActorUniqueId() : int{ return $this->actorUniqueId; }
	public function getPropertyName() : string{ return $this->propertyName; }
	public function isBoolValue() : bool{ return $this->boolValue; }
	public function getStringValue() : string{ return $this->stringValue; }
	public function getIntValue() : int{ return $this->intValue; }
	public function getFloatValue() : float{ return $this->floatValue; }
	protected function decodePayload(){
		$this->actorUniqueId = $this->getEntityUniqueId();
		$this->propertyName = $this->getString();
		$this->boolValue = $this->getBool();
		$this->stringValue = $this->getString();
		$this->intValue = $this->getVarInt();
		$this->floatValue = $this->getLFloat();
	}
	protected function encodePayload(){
		$this->putEntityUniqueId($this->actorUniqueId);
		$this->putString($this->propertyName);
		$this->putBool($this->boolValue);
		$this->putString($this->stringValue);
		$this->putVarInt($this->intValue);
		$this->putLFloat($this->floatValue);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleChangeMobProperty($this);
	}}