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
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\types\inventory\CreativeGroupEntry;use pocketmine\network\mcpe\protocol\types\inventory\CreativeItemEntry;use function count;
class CreativeContentPacket extends DataPacket{
    public const NETWORK_ID = ProtocolInfo::CREATIVE_CONTENT_PACKET;
    public const CATEGORY_CONSTRUCTION = 1;
    public const CATEGORY_NATURE = 2;
    public const CATEGORY_EQUIPMENT = 3;
    public const CATEGORY_ITEMS = 4;
    public const CREATIVE_GROUP_NONE = 0xffffffff;
    public $groups;
    public $items;
    public static function create(array $groups, array $items) : self{
        $result = new self;
        $result->groups = $groups;
        $result->items = $items;
        return $result;
    }
    public function getGroups() : array{ return $this->groups; }
    public function getItems() : array{ return $this->items; }
    protected function decodePayload(){
		$this->groups = [];
        
	    	for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
		    	$this->groups[] = CreativeGroupEntry::read($this);
            }
		
		$this->items = [];
		for($i = 0, $len = $this->getUnsignedVarInt(); $i < $len; ++$i){
			$this->items[] = CreativeItemEntry::read($this);
		}
    }
    protected function encodePayload(){
        
            $this->putUnsignedVarInt(count($this->groups));
            foreach($this->groups as $entry){
                $entry->write($this);
            }
        
        $this->putUnsignedVarInt(count($this->items));
        foreach($this->items as $entry){
            $entry->write($this);
        }
    }
	public function handle(NetworkSession $session) : bool{
		return $session->handleCreativeContent($this);
	}
}