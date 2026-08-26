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
class AgentAnimationPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::AGENT_ANIMATION_PACKET;
	public const TYPE_ARM_SWING = 0;
	public const TYPE_SHRUG = 1;
	private int $animationType;
	private int $actorRuntimeId;
	public static function create(int $animationType, int $actorRuntimeId) : self{
		$result = new self;
		$result->animationType = $animationType;
		$result->actorRuntimeId = $actorRuntimeId;
		return $result;
	}
	public function getAnimationType() : int{ return $this->animationType; }
	public function getActorRuntimeId() : int{ return $this->actorRuntimeId; }
	protected function decodePayload(){
		$this->animationType = $this->getByte();
		$this->actorRuntimeId = $this->getEntityRuntimeId();
	}
	protected function encodePayload(){
		$this->putByte($this->animationType);
		$this->putEntityRuntimeId($this->actorRuntimeId);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleAgentAnimation($this);
	}}