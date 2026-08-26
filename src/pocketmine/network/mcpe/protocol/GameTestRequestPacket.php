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
class GameTestRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::GAME_TEST_REQUEST_PACKET;
	public const ROTATION_0 = 0;
	public const ROTATION_90 = 1;
	public const ROTATION_180 = 2;
	public const ROTATION_270 = 3;
	private int $maxTestsPerBatch;
	private int $repeatCount;
	private int $rotation;
	private bool $stopOnFailure;
	private int $x = 0;
    private int $y = 0;
    private int $z = 0;
	private int $testsPerRow;
	private string $testName;
	public static function create(
		int $maxTestsPerBatch,
		int $repeatCount,
		int $rotation,
		bool $stopOnFailure,
		int $x,
        int $y,
        int $z,
		int $testsPerRow,
		string $testName,
	) : self{
		$result = new self;
		$result->maxTestsPerBatch = $maxTestsPerBatch;
		$result->repeatCount = $repeatCount;
		$result->rotation = $rotation;
		$result->stopOnFailure = $stopOnFailure;
		$result->x = $x;
        $result->y = $y;
        $result->z = $z;
		$result->testsPerRow = $testsPerRow;
		$result->testName = $testName;
		return $result;
	}
	public function getMaxTestsPerBatch() : int{ return $this->maxTestsPerBatch; }
	public function getRepeatCount() : int{ return $this->repeatCount; }
	public function getRotation() : int{ return $this->rotation; }
	public function isStopOnFailure() : bool{ return $this->stopOnFailure; }
    public function getX() : int{ return $this->x; }
    public function getY() : int{ return $this->y; }
    public function getZ() : int{ return $this->z; }
	public function getTestsPerRow() : int{ return $this->testsPerRow; }
	public function getTestName() : string{ return $this->testName; }
	protected function decodePayload() : void{
		$this->maxTestsPerBatch = $this->getVarInt();
		$this->repeatCount = $this->getVarInt();
		$this->rotation = $this->getByte();
		$this->stopOnFailure = $this->getBool();
		$this->getSignedBlockPosition($this->x, $this->y, $this->z);
		$this->testsPerRow = $this->getVarInt();
		$this->testName = $this->getString();
	}
	protected function encodePayload() : void{
		$this->putVarInt($this->maxTestsPerBatch);
		$this->putVarInt($this->repeatCount);
		$this->putByte($this->rotation);
		$this->putBool($this->stopOnFailure);
		$this->putSignedBlockPosition($this->x, $this->y, $this->z);
		$this->putVarInt($this->testsPerRow);
		$this->putString($this->testName);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleGameTestRequest($this);
	}}