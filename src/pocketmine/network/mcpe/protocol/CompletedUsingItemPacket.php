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
class CompletedUsingItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::COMPLETED_USING_ITEM_PACKET;
	public const ACTION_UNKNOWN = -1;
	public const ACTION_EQUIP_ARMOR = 0;
	public const ACTION_EAT = 1;
	public const ACTION_ATTACK = 2;
	public const ACTION_CONSUME = 3;
	public const ACTION_THROW = 4;
	public const ACTION_SHOOT = 5;
	public const ACTION_PLACE = 6;
	public const ACTION_FILL_BOTTLE = 7;
	public const ACTION_FILL_BUCKET = 8;
	public const ACTION_POUR_BUCKET = 9;
	public const ACTION_USE_TOOL = 10;
	public const ACTION_INTERACT = 11;
	public const ACTION_RETRIEVED = 12;
	public const ACTION_DYED = 13;
	public const ACTION_TRADED = 14;
	public const ACTION_BRUSHING_COMPLETED = 15;
	public $itemId;
	public $action;
	public function decodePayload() : void{
		$this->itemId = $this->getShort();
		$this->action = $this->getLInt();
	}
	public function encodePayload() : void{
		$this->putShort($this->itemId);
		$this->putLInt($this->action);
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCompletedUsingItem($this);
	}}