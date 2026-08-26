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
class BossEventPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::BOSS_EVENT_PACKET;
	public const TYPE_SHOW = 0;
	public const TYPE_REGISTER_PLAYER = 1;
	public const TYPE_HIDE = 2;
	public const TYPE_UNREGISTER_PLAYER = 3;
	public const TYPE_HEALTH_PERCENT = 4;
	public const TYPE_TITLE = 5;
	public const TYPE_UNKNOWN_6 = 6;
	public const TYPE_TEXTURE = 7;
	public const TYPE_QUERY = 8;
	public $bossEid;
	public $eventType;
	public $playerEid;
	public $healthPercent;
	public $title;
	public $filteredTitle = "";
	public $unknownShort;
	public $color;
	public $overlay;
	protected function decodePayload(){
		$this->bossEid = $this->getEntityUniqueId();
		$this->eventType = $this->getUnsignedVarInt();
		switch($this->eventType){
			case self::TYPE_REGISTER_PLAYER:
			case self::TYPE_UNREGISTER_PLAYER:
			case self::TYPE_QUERY:
				$this->playerEid = $this->getEntityUniqueId();
				break;
			case self::TYPE_SHOW:
				$this->title = $this->getString();
				
				$this->healthPercent = $this->getLFloat();
			case self::TYPE_UNKNOWN_6:
				$this->unknownShort = $this->getLShort();
			case self::TYPE_TEXTURE:
				$this->color = $this->getUnsignedVarInt();
				$this->overlay = $this->getUnsignedVarInt();
				break;
			case self::TYPE_HEALTH_PERCENT:
				$this->healthPercent = $this->getLFloat();
				break;
			case self::TYPE_TITLE:
				$this->title = $this->getString();
				
				break;
			default:
				break;
		}
	}
	protected function encodePayload(){
		$this->putEntityUniqueId($this->bossEid);
		$this->putUnsignedVarInt($this->eventType);
		switch($this->eventType){
			case self::TYPE_REGISTER_PLAYER:
			case self::TYPE_UNREGISTER_PLAYER:
			case self::TYPE_QUERY:
				$this->putEntityUniqueId($this->playerEid);
				break;
			case self::TYPE_SHOW:
				$this->putString($this->title);
				
                $this->putLFloat($this->healthPercent);
			case self::TYPE_UNKNOWN_6:
                $this->putLShort($this->unknownShort);
			case self::TYPE_TEXTURE:
				$this->putUnsignedVarInt($this->color);
				$this->putUnsignedVarInt($this->overlay);
				break;
			case self::TYPE_HEALTH_PERCENT:
                $this->putLFloat($this->healthPercent);
				break;
			case self::TYPE_TITLE:
				$this->putString($this->title);
				
				break;
			default:
				break;
		}
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleBossEvent($this);
	}}