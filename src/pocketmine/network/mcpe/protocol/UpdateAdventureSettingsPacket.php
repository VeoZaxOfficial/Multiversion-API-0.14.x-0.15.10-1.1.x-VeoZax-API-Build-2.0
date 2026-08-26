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
class UpdateAdventureSettingsPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::UPDATE_ADVENTURE_SETTINGS_PACKET;
	private bool $noAttackingMobs;
	private bool $noAttackingPlayers;
	private bool $worldImmutable;
	private bool $showNameTags;
	private bool $autoJump;
	public static function create(bool $noAttackingMobs, bool $noAttackingPlayers, bool $worldImmutable, bool $showNameTags, bool $autoJump) : self{
		$result = new self;
		$result->noAttackingMobs = $noAttackingMobs;
		$result->noAttackingPlayers = $noAttackingPlayers;
		$result->worldImmutable = $worldImmutable;
		$result->showNameTags = $showNameTags;
		$result->autoJump = $autoJump;
		return $result;
	}
	public function isNoAttackingMobs() : bool{ return $this->noAttackingMobs; }
	public function isNoAttackingPlayers() : bool{ return $this->noAttackingPlayers; }
	public function isWorldImmutable() : bool{ return $this->worldImmutable; }
	public function isShowNameTags() : bool{ return $this->showNameTags; }
	public function isAutoJump() : bool{ return $this->autoJump; }
	protected function decodePayload(){
		$this->noAttackingMobs = $this->getBool();
		$this->noAttackingPlayers = $this->getBool();
		$this->worldImmutable = $this->getBool();
		$this->showNameTags = $this->getBool();
		$this->autoJump = $this->getBool();
	}
	protected function encodePayload(){
		$this->putBool($this->noAttackingMobs);
		$this->putBool($this->noAttackingPlayers);
		$this->putBool($this->worldImmutable);
		$this->putBool($this->showNameTags);
		$this->putBool($this->autoJump);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleUpdateAdventureSettings($this);
	}}