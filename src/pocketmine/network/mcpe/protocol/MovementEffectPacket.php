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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\MovementEffectType;
class MovementEffectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOVEMENT_EFFECT_PACKET;
	private int $actorRuntimeId;
	private MovementEffectType $effectType;
	private int $duration;
	private int $tick;
	public static function create(int $actorRuntimeId, MovementEffectType $effectType, int $duration, int $tick) : self{
		$result = new self;
		$result->actorRuntimeId = $actorRuntimeId;
		$result->effectType = $effectType;
		$result->duration = $duration;
		$result->tick = $tick;
		return $result;
	}
	public function getActorRuntimeId() : int{ return $this->actorRuntimeId; }
	public function getEffectType() : MovementEffectType{ return $this->effectType; }
	public function getDuration() : int{ return $this->duration; }
	public function getTick() : int{ return $this->tick; }
	protected function decodePayload() : void{
		$this->actorRuntimeId = $this->getEntityRuntimeId();
		$this->effectType = MovementEffectType::fromPacket($this->getUnsignedVarInt());
		$this->duration = $this->getUnsignedVarInt();
		$this->tick = $this->getUnsignedVarLong();
	}
	protected function encodePayload() : void{
		$this->putEntityRuntimeId($this->actorRuntimeId);
		$this->putUnsignedVarInt($this->effectType->value);
		$this->putUnsignedVarInt($this->duration);
		$this->putUnsignedVarLong($this->tick);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleMovementEffect($this);
	}}