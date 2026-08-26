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
use InvalidArgumentException;use LogicException;use pocketmine\network\mcpe\NetworkSession;use function is_bool;use function is_float;
class RequestAbilityPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REQUEST_ABILITY_PACKET;
	private const VALUE_TYPE_BOOL = 1;
	private const VALUE_TYPE_FLOAT = 2;
	public const ABILITY_FLYING = 9;
	public const ABILITY_NOCLIP = 17;
	private int $abilityId;
	private float|bool $abilityValue;
	public static function create(int $abilityId, float|bool $abilityValue) : self{
		$result = new self;
		$result->abilityId = $abilityId;
		$result->abilityValue = $abilityValue;
		return $result;
	}
	public function getAbilityId() : int{ return $this->abilityId; }
	public function getAbilityValue() : float|bool{ return $this->abilityValue; }
	protected function decodePayload(){
		$this->abilityId = $this->getVarInt();
		$valueType = $this->getByte();
		$boolValue = $this->getBool();
		$floatValue = $this->getLFloat();
		$this->abilityValue = match($valueType){
			self::VALUE_TYPE_BOOL => $boolValue,
			self::VALUE_TYPE_FLOAT => $floatValue,
			default => throw new InvalidArgumentException("Unknown ability value type $valueType")
		};
	}
	protected function encodePayload(){
		$this->putVarInt($this->abilityId);
		[$valueType, $boolValue, $floatValue] = match(true){
			is_bool($this->abilityValue) => [self::VALUE_TYPE_BOOL, $this->abilityValue, 0.0],
			is_float($this->abilityValue) => [self::VALUE_TYPE_FLOAT, false, $this->abilityValue],
			default => throw new LogicException("Unreachable")
		};
		$this->putByte($valueType);
		$this->putBool($boolValue);
		$this->putLFloat($floatValue);
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleRequestAbility($this);
	}}