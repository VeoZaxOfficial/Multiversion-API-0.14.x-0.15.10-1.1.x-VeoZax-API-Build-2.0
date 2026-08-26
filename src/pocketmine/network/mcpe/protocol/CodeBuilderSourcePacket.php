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
class CodeBuilderSourcePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::CODE_BUILDER_SOURCE_PACKET;
	private int $operation;
	private int $category;
	private string $value;
	private int $codeStatus;
	public static function create(int $operation, int $category, string $value, int $codeStatus) : self{
		$result = new self;
		$result->operation = $operation;
		$result->category = $category;
		$result->value = $value;
		$result->codeStatus = $codeStatus;
		return $result;
	}
	public function getOperation() : int{ return $this->operation; }
	public function getCategory() : int{ return $this->category; }
	public function getValue() : string{ return $this->value; }
	public function getCodeStatus() : int{ return $this->codeStatus; }
	protected function decodePayload(){
		$this->operation = $this->getByte();
		$this->category = $this->getByte();
		
			$this->value = $this->getString();
		
	}
	protected function encodePayload(){
		$this->putByte($this->operation);
		$this->putByte($this->category);
		
	    	$this->putString($this->value);
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleCodeBuilderSource($this);
	}}