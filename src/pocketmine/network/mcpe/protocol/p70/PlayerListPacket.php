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

declare(strict_types=1);namespace pocketmine\network\mcpe\protocol\p70;use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds70;use pocketmine\utils\UUID;class PlayerListPacket extends LegacyDataPacket {
    public const NETWORK_ID = PacketsIds70::PLAYER_LIST_PACKET;
    public const TYPE_ADD    = 0;
    public const TYPE_REMOVE = 1;
    public int   $type    = self::TYPE_ADD;
    public array $entries = [];
    protected function decodePayload() : void {}
    protected function encodePayload() : void {
        $this->buffer  = $this->wByte(self::NETWORK_ID);
        $this->buffer .= $this->wByte($this->type);
        $this->buffer .= $this->wInt(count($this->entries));
        foreach($this->entries as $e){
            $this->buffer .= $this->wUUID($e[0]); 
            if($this->type === self::TYPE_ADD){
                $this->buffer .= $this->wLong((int)$e[1]);  
                $this->buffer .= $this->wStr((string)$e[2]); 
                $this->buffer .= $this->wStr((string)$e[3]); 
                $this->buffer .= $this->wStr((string)$e[4]); 
            }
        }
    }}