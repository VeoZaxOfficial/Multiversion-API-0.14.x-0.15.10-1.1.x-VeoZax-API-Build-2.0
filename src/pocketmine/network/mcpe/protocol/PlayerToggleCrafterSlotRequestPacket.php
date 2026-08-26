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
class PlayerToggleCrafterSlotRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::PLAYER_TOGGLE_CRAFTER_SLOT_REQUEST_PACKET;
	private Vector3 $position;
	private int $slot;
	private bool $disabled;
	public static function create(Vector3 $position, int $slot, bool $disabled) : self{
		$result = new self;
		$result->position = $position;
		$result->slot = $slot;
		$result->disabled = $disabled;
		return $result;
	}
	public function getPosition() : Vector3{ return $this->position; }
	public function getSelectedSlot() : int{ return $this->slot; }
	public function isDisabled() : bool{ return $this->disabled; }
	protected function decodePayload() : void{
		$x = $this->getLInt();
		$y = $this->getLInt();
		$z = $this->getLInt();
		$this->position = new Vector3($x, $y, $z);
		$this->slot = $this->getByte();
		$this->disabled = $this->getBool();
	}
	protected function encodePayload() : void{
		$this->putLInt($this->position->getX());
		$this->putLInt($this->position->getY());
		$this->putLInt($this->position->getZ());
		$this->putByte($this->slot);
		$this->putBool($this->disabled);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handlePlayerToggleCrafterSlotRequest($this);
	}}