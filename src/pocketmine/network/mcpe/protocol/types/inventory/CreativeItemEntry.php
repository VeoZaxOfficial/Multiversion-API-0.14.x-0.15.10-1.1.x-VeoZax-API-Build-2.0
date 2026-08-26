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
namespace pocketmine\network\mcpe\protocol\types\inventory;
use pocketmine\item\Item;use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\ProtocolInfo;
final class CreativeItemEntry{
    public function __construct(
        private int $entryId,
        private Item $item,
        private int $groupId
    ){}
    public function getEntryId() : int{ return $this->entryId; }
    public function getItem() : Item{ return $this->item; }
    public function getGroupId() : int{ return $this->groupId; }
    public function setEntryId(int $entryId) : void{ $this->entryId = $entryId; }
    public static function read(NetworkBinaryStream $in) : self{
        
            $entryId = $in->getUnsignedVarInt();
            
                $in->getUnsignedVarInt();
            
        
        $item = $in->getSlot(false);
        
        return new self($entryId, $item, $groupId ?? 0);
    }
    public function write(NetworkBinaryStream $out) : void{
        
            $out->putUnsignedVarInt($this->entryId);
            
                $out->putUnsignedVarInt($this->groupId);
            
        
        $out->putSlot($this->item, false);
        
    }}