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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\TrimMaterial;use pocketmine\network\mcpe\protocol\types\TrimPattern;use function count;
class TrimDataPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::TRIM_DATA_PACKET;
	private $trimPatterns;
	private $trimMaterials;
	public static function create(array $trimPatterns, array $trimMaterials) : self{
		$result = new self;
		$result->trimPatterns = $trimPatterns;
		$result->trimMaterials = $trimMaterials;
		return $result;
	}
	public function getTrimPatterns() : array{ return $this->trimPatterns; }
	public function getTrimMaterials() : array{ return $this->trimMaterials; }
	protected function decodePayload(){
		$this->trimPatterns = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->trimPatterns[] = TrimPattern::read($this);
		}
		$this->trimMaterials = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->trimMaterials[] = TrimMaterial::read($this);
		}
	}
	protected function encodePayload(){
		$this->putUnsignedVarInt(count($this->trimPatterns));
		foreach($this->trimPatterns as $trimPattern){
			$trimPattern->write($this);
		}
		$this->putUnsignedVarInt(count($this->trimMaterials));
		foreach($this->trimMaterials as $trimMaterial){
			$trimMaterial->write($this);
		}
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleTrimData($this);
	}}