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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\hud\HudElement;use pocketmine\network\mcpe\protocol\types\hud\HudVisibility;use function count;
class SetHudPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_HUD_PACKET;
	private array $hudElements = [];
	private HudVisibility $visibility;
	public static function create(array $hudElements, HudVisibility $visibility) : self{
		$result = new self;
		$result->hudElements = $hudElements;
		$result->visibility = $visibility;
		return $result;
	}
	public function getHudElements() : array{ return $this->hudElements; }
	public function getVisibility() : HudVisibility{ return $this->visibility; }
	protected function decodePayload(){
		$this->hudElements = [];
		for($i = 0, $count = $this->getUnsignedVarInt(); $i < $count; ++$i){
			$this->hudElements[] = HudElement::fromPacket( ($this->getByte()) );
		}
		$this->visibility = HudVisibility::fromPacket( ($this->getByte()) );
	}
	protected function encodePayload(){
		$this->putUnsignedVarInt(count($this->hudElements));
		foreach($this->hudElements as $element){
		    
			    $this->putByte($element->value);
		    
		}
		
		    $this->putByte($this->visibility->value);
		
	}
	public function handle(NetworkSession $session) : bool{
		return $session->handleSetHud($this);
	}}