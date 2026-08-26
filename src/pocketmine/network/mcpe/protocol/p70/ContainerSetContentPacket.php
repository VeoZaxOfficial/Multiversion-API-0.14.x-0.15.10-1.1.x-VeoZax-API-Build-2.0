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

declare(strict_types=1);namespace pocketmine\network\mcpe\protocol\p70;use pocketmine\item\Item;use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds70;class ContainerSetContentPacket extends LegacyDataPacket {
    public const NETWORK_ID      = PacketsIds70::CONTAINER_SET_CONTENT_PACKET;
    public const SPECIAL_INVENTORY = 0;
    public const SPECIAL_ARMOR     = 0x78;
    public const SPECIAL_CREATIVE  = 0x79;
    public int   $windowid = 0;
    public array $slots    = [];
    public array $hotbar   = [];
    protected function decodePayload() : void {}
    protected function encodePayload() : void {
        $this->buffer  = $this->wByte(self::NETWORK_ID);
        $this->buffer .= $this->wByte($this->windowid);
        $this->buffer .= $this->wShort(count($this->slots));
        foreach($this->slots as $s) $this->buffer .= $this->wSlot($s instanceof Item ? $s : Item::get(0));
        if($this->windowid === self::SPECIAL_INVENTORY && count($this->hotbar) > 0){
            $this->buffer .= $this->wShort(count($this->hotbar));
            foreach($this->hotbar as $h) $this->buffer .= $this->wInt((int)$h);
        }else{
            $this->buffer .= $this->wShort(0);
        }
    }}