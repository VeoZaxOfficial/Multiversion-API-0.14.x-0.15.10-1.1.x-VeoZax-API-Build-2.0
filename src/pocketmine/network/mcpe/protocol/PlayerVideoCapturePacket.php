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
use pocketmine\network\mcpe\NetworkSession;use LogicException;
class PlayerVideoCapturePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_VIDEO_CAPTURE_PACKET;
	private bool $recording;
	private ?int $frameRate;
	private ?string $filePrefix;
	private static function create(bool $recording, ?int $frameRate, ?string $filePrefix) : self{
		$result = new self;
		$result->recording = $recording;
		$result->frameRate = $frameRate;
		$result->filePrefix = $filePrefix;
		return $result;
	}
	public static function createStartRecording(int $frameRate, string $filePrefix) : self{
		return self::create(true, $frameRate, $filePrefix);
	}
	public static function createStopRecording() : self{
		return self::create(false, null, null);
	}
	public function isRecording() : bool{ return $this->recording; }
	public function getFrameRate() : ?int{ return $this->frameRate; }
	public function getFilePrefix() : ?string{ return $this->filePrefix; }
	protected function decodePayload() : void{
		$this->recording = $this->getBool();
		if($this->recording){
			$this->frameRate = $this->getLInt();
			$this->filePrefix = $this->getString();
		}
	}
	protected function encodePayload() : void{
		$this->putBool($this->recording);
		if($this->recording){
			if($this->frameRate === null){ 
				throw new LogicException("PlayerUpdateEntityOverridesPacket with recording=true require a frame rate to be provided");
			}
			if($this->filePrefix === null){ 
				throw new LogicException("PlayerUpdateEntityOverridesPacket with recording=true require a file prefix to be provided");
			}
			$this->putLInt($this->frameRate);
			$this->putString($this->filePrefix);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerVideoCapturePacket($this);
	}}