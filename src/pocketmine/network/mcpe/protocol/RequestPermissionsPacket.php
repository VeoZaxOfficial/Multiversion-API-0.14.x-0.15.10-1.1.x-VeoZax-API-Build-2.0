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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
class RequestPermissionsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::REQUEST_PERMISSIONS_PACKET;
	public const FLAG_BUILD = 1 << 0;
	public const FLAG_MINE = 1 << 1;
	public const FLAG_DOORS_AND_SWITCHES = 1 << 2;
	public const FLAG_OPEN_CONTAINERS = 1 << 3;
	public const FLAG_ATTACK_PLAYERS = 1 << 4;
	public const FLAG_ATTACK_MOBS = 1 << 5;
	public const FLAG_OPERATOR = 1 << 6;
	public const FLAG_TELEPORT = 1 << 7;
	private int $targetActorUniqueId;
	private int $playerPermission;
	private int $customFlags;
	public static function create(int $targetActorUniqueId, int $playerPermission, int $customFlags) : self{
		$result = new self;
		$result->targetActorUniqueId = $targetActorUniqueId;
		$result->playerPermission = $playerPermission;
		$result->customFlags = $customFlags;
		return $result;
	}
	public function getTargetActorUniqueId() : int{ return $this->targetActorUniqueId; }
	public function getPlayerPermission() : int{ return $this->playerPermission; }
	public function getCustomFlags() : int{ return $this->customFlags; }
	protected function decodePayload(){
		$this->targetActorUniqueId = $this->getLLong();
		$this->playerPermission = $this->getVarInt();
		$this->customFlags = $this->getLShort();
	}
	protected function encodePayload(){
		$this->putLLong($this->targetActorUniqueId);
		$this->putVarInt($this->playerPermission);
		$this->putLShort($this->customFlags);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleRequestPermissions($this);
	}}