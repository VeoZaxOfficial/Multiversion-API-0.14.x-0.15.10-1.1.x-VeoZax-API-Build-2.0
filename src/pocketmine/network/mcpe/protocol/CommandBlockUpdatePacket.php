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
use pocketmine\network\mcpe\NetworkSession;use function pack;use function unpack;
class CommandBlockUpdatePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET;
	public $isBlock;
	public $x;
	public $y;
	public $z;
	public $commandBlockMode;
	public $isRedstoneMode;
	public $isConditional;
	public $minecartEid;
	public $command;
	public $lastOutput;
	public $name;
	public $filteredName;
	public $shouldTrackOutput;
	public $tickDelay;
	public $executeOnFirstTick;
	protected function decodePayload(){
		$this->isBlock = $this->getBool();
		if($this->isBlock){
			$this->getBlockPosition($this->x, $this->y, $this->z);
			$this->commandBlockMode = $this->getUnsignedVarInt();
			$this->isRedstoneMode = $this->getBool();
			$this->isConditional = $this->getBool();
		}else{
			$this->minecartEid = $this->getEntityRuntimeId();
		}
		$this->command = $this->getString();
		$this->lastOutput = $this->getString();
		$this->name = $this->getString();
		
		$this->shouldTrackOutput = $this->getBool();
		
	}
	protected function encodePayload(){
        $this->putBool($this->isBlock);
		if($this->isBlock){
			$this->putBlockPosition($this->x, $this->y, $this->z);
			$this->putUnsignedVarInt($this->commandBlockMode);
            $this->putBool($this->isRedstoneMode);
            $this->putBool($this->isConditional);
		}else{
			$this->putEntityRuntimeId($this->minecartEid);
		}
		$this->putString($this->command);
		$this->putString($this->lastOutput);
		$this->putString($this->name);
		
        $this->putBool($this->shouldTrackOutput);
		
	}
	public function mustBeDecoded() : bool{
		return true;
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCommandBlockUpdate($this);
	}}