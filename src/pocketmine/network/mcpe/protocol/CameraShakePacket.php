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
class CameraShakePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_SHAKE_PACKET;
	public const TYPE_POSITIONAL = 0;
	public const TYPE_ROTATIONAL = 1;
	public const ACTION_ADD = 0;
	public const ACTION_STOP = 1;
	private $intensity;
	private $duration;
	private $shakeType;
	private $shakeAction;
	public static function create(float $intensity, float $duration, int $shakeType, int $shakeAction) : self{
		$result = new self;
		$result->intensity = $intensity;
		$result->duration = $duration;
		$result->shakeType = $shakeType;
		$result->shakeAction = $shakeAction;
		return $result;
	}
	public function getIntensity() : float{ return $this->intensity; }
	public function getDuration() : float{ return $this->duration; }
	public function getShakeType() : int{ return $this->shakeType; }
	public function getShakeAction() : int{ return $this->shakeAction; }
	protected function decodePayload() : void{
		$this->intensity = $this->getLFloat();
		$this->duration = $this->getLFloat();
		$this->shakeType = $this->getByte();
		
	}
	protected function encodePayload() : void{
        $this->putLFloat($this->intensity);
        $this->putLFloat($this->duration);
        $this->putByte($this->shakeType);
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCameraShake($this);
	}}