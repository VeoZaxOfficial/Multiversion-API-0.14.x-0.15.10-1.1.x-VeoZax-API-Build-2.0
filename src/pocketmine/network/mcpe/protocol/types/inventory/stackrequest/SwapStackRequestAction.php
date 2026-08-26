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
namespace pocketmine\network\mcpe\protocol\types\inventory\stackrequest;
use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\types\GetTypeIdFromConstTrait;
final class SwapStackRequestAction extends ItemStackRequestAction{
	use GetTypeIdFromConstTrait;
	public const ID = ItemStackRequestActionType::SWAP;
	private $slot1;
	private $slot2;
	public function __construct(ItemStackRequestSlotInfo $slot1, ItemStackRequestSlotInfo $slot2){
		$this->slot1 = $slot1;
		$this->slot2 = $slot2;
	}
	public function getSlot1() : ItemStackRequestSlotInfo{ return $this->slot1; }
	public function getSlot2() : ItemStackRequestSlotInfo{ return $this->slot2; }
	public static function read(NetworkBinaryStream $in) : self{
		$slot1 = ItemStackRequestSlotInfo::read($in);
		$slot2 = ItemStackRequestSlotInfo::read($in);
		return new self($slot1, $slot2);
	}
	public function write(NetworkBinaryStream $out) : void{
		$this->slot1->write($out);
		$this->slot2->write($out);
	}}