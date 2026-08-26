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
class ServerboundDiagnosticsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SERVERBOUND_DIAGNOSTICS_PACKET;
	private float $avgFps;
	private float $avgServerSimTickTimeMS;
	private float $avgClientSimTickTimeMS;
	private float $avgBeginFrameTimeMS;
	private float $avgInputTimeMS;
	private float $avgRenderTimeMS;
	private float $avgEndFrameTimeMS;
	private float $avgRemainderTimePercent;
	private float $avgUnaccountedTimePercent;
	public static function create(
		float $avgFps,
		float $avgServerSimTickTimeMS,
		float $avgClientSimTickTimeMS,
		float $avgBeginFrameTimeMS,
		float $avgInputTimeMS,
		float $avgRenderTimeMS,
		float $avgEndFrameTimeMS,
		float $avgRemainderTimePercent,
		float $avgUnaccountedTimePercent,
	) : self{
		$result = new self;
		$result->avgFps = $avgFps;
		$result->avgServerSimTickTimeMS = $avgServerSimTickTimeMS;
		$result->avgClientSimTickTimeMS = $avgClientSimTickTimeMS;
		$result->avgBeginFrameTimeMS = $avgBeginFrameTimeMS;
		$result->avgInputTimeMS = $avgInputTimeMS;
		$result->avgRenderTimeMS = $avgRenderTimeMS;
		$result->avgEndFrameTimeMS = $avgEndFrameTimeMS;
		$result->avgRemainderTimePercent = $avgRemainderTimePercent;
		$result->avgUnaccountedTimePercent = $avgUnaccountedTimePercent;
		return $result;
	}
	public function getAvgFps() : float{ return $this->avgFps; }
	public function getAvgServerSimTickTimeMS() : float{ return $this->avgServerSimTickTimeMS; }
	public function getAvgClientSimTickTimeMS() : float{ return $this->avgClientSimTickTimeMS; }
	public function getAvgBeginFrameTimeMS() : float{ return $this->avgBeginFrameTimeMS; }
	public function getAvgInputTimeMS() : float{ return $this->avgInputTimeMS; }
	public function getAvgRenderTimeMS() : float{ return $this->avgRenderTimeMS; }
	public function getAvgEndFrameTimeMS() : float{ return $this->avgEndFrameTimeMS; }
	public function getAvgRemainderTimePercent() : float{ return $this->avgRemainderTimePercent; }
	public function getAvgUnaccountedTimePercent() : float{ return $this->avgUnaccountedTimePercent; }
	protected function decodePayload() : void{
		$this->avgFps = $this->getLFloat();
		$this->avgServerSimTickTimeMS = $this->getLFloat();
		$this->avgClientSimTickTimeMS = $this->getLFloat();
		$this->avgBeginFrameTimeMS = $this->getLFloat();
		$this->avgInputTimeMS = $this->getLFloat();
		$this->avgRenderTimeMS = $this->getLFloat();
		$this->avgEndFrameTimeMS = $this->getLFloat();
		$this->avgRemainderTimePercent = $this->getLFloat();
		$this->avgUnaccountedTimePercent = $this->getLFloat();
	}
	protected function encodePayload() : void{
		$this->putLFloat($this->avgFps);
		$this->putLFloat($this->avgServerSimTickTimeMS);
		$this->putLFloat($this->avgClientSimTickTimeMS);
		$this->putLFloat($this->avgBeginFrameTimeMS);
		$this->putLFloat($this->avgInputTimeMS);
		$this->putLFloat($this->avgRenderTimeMS);
		$this->putLFloat($this->avgEndFrameTimeMS);
		$this->putLFloat($this->avgRemainderTimePercent);
		$this->putLFloat($this->avgUnaccountedTimePercent);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleServerboundDiagnostics($this);
	}}