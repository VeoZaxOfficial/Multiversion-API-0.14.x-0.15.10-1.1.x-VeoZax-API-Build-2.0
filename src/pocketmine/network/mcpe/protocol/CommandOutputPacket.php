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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\CommandOriginData;use pocketmine\network\mcpe\protocol\types\CommandOutputMessage;use function chr;use function count;use function ord;
class CommandOutputPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::COMMAND_OUTPUT_PACKET;
	public $originData;
	public $outputType;
	public $successCount;
	public $messages = [];
	public $unknownString;
	protected function decodePayload(){
		$this->originData = $this->getCommandOriginData();
		$this->outputType = $this->getByte();
		$this->successCount = $this->getUnsignedVarInt();
		for($i = 0, $size = $this->getUnsignedVarInt(); $i < $size; ++$i){
			$this->messages[] = $this->getCommandMessage();
		}
		if($this->outputType === 4){
			$this->unknownString = $this->getString();
		}
	}
	protected function getCommandMessage() : CommandOutputMessage{
		$message = new CommandOutputMessage();
		$message->isInternal = $this->getBool();
		$message->messageId = $this->getString();
		for($i = 0, $size = $this->getUnsignedVarInt(); $i < $size; ++$i){
			$message->parameters[] = $this->getString();
		}
		return $message;
	}
	protected function encodePayload(){
		$this->putCommandOriginData($this->originData);
        $this->putByte($this->outputType);
		$this->putUnsignedVarInt($this->successCount);
		$this->putUnsignedVarInt(count($this->messages));
		foreach($this->messages as $message){
			$this->putCommandMessage($message);
		}
		if($this->outputType === 4){
			$this->putString($this->unknownString);
		}
	}
	protected function putCommandMessage(CommandOutputMessage $message){
        $this->putBool($message->isInternal);
		$this->putString($message->messageId);
		$this->putUnsignedVarInt(count($message->parameters));
		foreach($message->parameters as $parameter){
			$this->putString($parameter);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCommandOutput($this);
	}}