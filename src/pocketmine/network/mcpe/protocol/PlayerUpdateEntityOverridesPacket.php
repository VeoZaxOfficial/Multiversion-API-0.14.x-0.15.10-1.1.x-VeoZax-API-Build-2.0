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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\OverrideUpdateType;use LogicException;
class PlayerUpdateEntityOverridesPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_UPDATE_ENTITY_OVERRIDES_PACKET;
	private int $actorRuntimeId;
	private int $propertyIndex;
	private OverrideUpdateType $updateType;
	private ?int $intOverrideValue;
	private ?float $floatOverrideValue;
	private static function create(int $actorRuntimeId, int $propertyIndex, OverrideUpdateType $updateType, ?int $intOverrideValue, ?float $floatOverrideValue) : self{
		$result = new self;
		$result->actorRuntimeId = $actorRuntimeId;
		$result->propertyIndex = $propertyIndex;
		$result->updateType = $updateType;
		$result->intOverrideValue = $intOverrideValue;
		$result->floatOverrideValue = $floatOverrideValue;
		return $result;
	}
	public static function createIntOverride(int $actorRuntimeId, int $propertyIndex, int $value) : self{
		return self::create($actorRuntimeId, $propertyIndex, OverrideUpdateType::SET_INT_OVERRIDE, $value, null);
	}
	public static function createFloatOverride(int $actorRuntimeId, int $propertyIndex, float $value) : self{
		return self::create($actorRuntimeId, $propertyIndex, OverrideUpdateType::SET_FLOAT_OVERRIDE, null, $value);
	}
	public static function createClearOverrides(int $actorRuntimeId, int $propertyIndex) : self{
		return self::create($actorRuntimeId, $propertyIndex, OverrideUpdateType::CLEAR_OVERRIDES, null, null);
	}
	public static function createRemoveOverride(int $actorRuntimeId, int $propertyIndex) : self{
		return self::create($actorRuntimeId, $propertyIndex, OverrideUpdateType::REMOVE_OVERRIDE, null, null);
	}
	public function getActorRuntimeId() : int{ return $this->actorRuntimeId; }
	public function getPropertyIndex() : int{ return $this->propertyIndex; }
	public function getUpdateType() : OverrideUpdateType{ return $this->updateType; }
	public function getIntOverrideValue() : ?int{ return $this->intOverrideValue; }
	public function getFloatOverrideValue() : ?float{ return $this->floatOverrideValue; }
	protected function decodePayload() : void{
		$this->actorRuntimeId = $this->getActorRuntimeId();
		$this->propertyIndex = $this->getUnsignedVarInt();
		$this->updateType = OverrideUpdateType::fromPacket($this->getByte());
		if($this->updateType === OverrideUpdateType::SET_INT_OVERRIDE){
			$this->intOverrideValue = $this->getLInt();
		}elseif($this->updateType === OverrideUpdateType::SET_FLOAT_OVERRIDE){
			$this->floatOverrideValue = $this->getLFloat();
		}
	}
	protected function encodePayload() : void{
		$this->putActorRuntimeId($this->actorRuntimeId);
		$this->putUnsignedVarInt($this->propertyIndex);
		$this->putByte($this->updateType->value);
		if($this->updateType === OverrideUpdateType::SET_INT_OVERRIDE){
			if($this->intOverrideValue === null){ 
				throw new LogicException("PlayerUpdateEntityOverridesPacket with type SET_INT_OVERRIDE require an intOverrideValue to be provided");
			}
			$this->putLInt($this->intOverrideValue);
		}elseif($this->updateType === OverrideUpdateType::SET_FLOAT_OVERRIDE){
			if($this->floatOverrideValue === null){ 
				throw new LogicException("PlayerUpdateEntityOverridesPacket with type SET_INT_OVERRIDE require an intOverrideValue to be provided");
			}
			$this->putLFloat($this->floatOverrideValue);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerUpdateEntityOverridesPacket($this);
	}}