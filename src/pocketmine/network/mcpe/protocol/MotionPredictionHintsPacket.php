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
use pocketmine\math\Vector3;use pocketmine\network\mcpe\NetworkSession;
class MotionPredictionHintsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOTION_PREDICTION_HINTS_PACKET;
	private $entityRuntimeId;
	private $motion;
	private $onGround;
	public static function create(int $entityRuntimeId, Vector3 $motion, bool $onGround) : self{
		$result = new self;
		$result->entityRuntimeId = $entityRuntimeId;
		$result->motion = $motion;
		$result->onGround = $onGround;
		return $result;
	}
	public function getEntityRuntimeIdField() : int{ return $this->entityRuntimeId; } 
	public function getMotion() : Vector3{ return $this->motion; }
	public function isOnGround() : bool{ return $this->onGround; }
	protected function decodePayload() : void{
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->motion = $this->getVector3();
		$this->onGround = $this->getBool();
	}
	protected function encodePayload() : void{
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putVector3($this->motion);
        $this->putBool($this->onGround);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleMotionPredictionHints($this);
	}}