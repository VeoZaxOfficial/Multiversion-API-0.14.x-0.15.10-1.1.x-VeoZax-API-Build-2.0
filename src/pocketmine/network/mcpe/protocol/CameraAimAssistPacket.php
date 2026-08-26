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
use pocketmine\math\Vector2;use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\camera\CameraAimAssistActionType;use pocketmine\network\mcpe\protocol\types\camera\CameraAimAssistTargetMode;
class CameraAimAssistPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CAMERA_AIM_ASSIST_PACKET;
	private Vector2 $viewAngle;
	private float $distance;
	private CameraAimAssistTargetMode $targetMode;
	private CameraAimAssistActionType $actionType;
	public static function create(Vector2 $viewAngle, float $distance, CameraAimAssistTargetMode $targetMode, CameraAimAssistActionType $actionType) : self{
		$result = new self;
		$result->viewAngle = $viewAngle;
		$result->distance = $distance;
		$result->targetMode = $targetMode;
		$result->actionType = $actionType;
		return $result;
	}
	public function getViewAngle() : Vector2{ return $this->viewAngle; }
	public function getDistance() : float{ return $this->distance; }
	public function getTargetMode() : CameraAimAssistTargetMode{ return $this->targetMode; }
	public function getActionType() : CameraAimAssistActionType{ return $this->actionType; }
	protected function decodePayload() : void{
		$this->viewAngle = $this->getVector2();
		$this->distance = $this->getLFloat();
		$this->targetMode = CameraAimAssistTargetMode::fromPacket($this->getByte());
		$this->actionType = CameraAimAssistActionType::fromPacket($this->getByte());
	}
	protected function encodePayload() : void{
		$this->putVector2($this->viewAngle);
		$this->putLFloat($this->distance);
		$this->putByte($this->targetMode->value);
		$this->putByte($this->actionType->value);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCameraAimAssist($this);
	}}