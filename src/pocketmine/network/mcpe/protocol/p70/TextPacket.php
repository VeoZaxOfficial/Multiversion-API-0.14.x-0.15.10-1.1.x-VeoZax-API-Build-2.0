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

declare(strict_types=1);namespace pocketmine\network\mcpe\protocol\p70;use pocketmine\network\mcpe\protocol\PacketsIds\PacketsIds70;class TextPacket extends LegacyDataPacket {
    public const NETWORK_ID = PacketsIds70::TEXT_PACKET;
    public const TYPE_RAW         = 0;
    public const TYPE_CHAT        = 1;
    public const TYPE_TRANSLATION = 2;
    public const TYPE_POPUP       = 3;
    public const TYPE_TIP         = 4;
    public const TYPE_SYSTEM      = 5;
    public int    $type       = self::TYPE_RAW;
    public string $source     = '';
    public string $message    = '';
    public array  $parameters = [];
    protected function decodePayload() : void {
        $this->type = $this->rByte();
        switch($this->type){
            case self::TYPE_POPUP:
            case self::TYPE_TIP:
            case self::TYPE_CHAT:
                $this->source = $this->rStr();
            case self::TYPE_RAW:
            case self::TYPE_SYSTEM:
                $this->message = $this->rStr();
                break;
            case self::TYPE_TRANSLATION:
                $this->message = $this->rStr();
                $cnt = $this->rByte();
                for($i = 0; $i < $cnt; ++$i) $this->parameters[] = $this->rStr();
                break;
        }
    }
    protected function encodePayload() : void {
        $type = ($this->type === self::TYPE_TIP) ? self::TYPE_POPUP : $this->type;
        $this->buffer = $this->wByte(self::NETWORK_ID) . $this->wByte($type);
        switch($this->type){
            case self::TYPE_POPUP:
            case self::TYPE_TIP:
            case self::TYPE_CHAT:
                $this->buffer .= $this->wStr($this->source);
            case self::TYPE_RAW:
            case self::TYPE_SYSTEM:
                $this->buffer .= $this->wStr($this->message);
                break;
            case self::TYPE_TRANSLATION:
                $this->buffer .= $this->wStr($this->message);
                $this->buffer .= $this->wByte(count($this->parameters));
                foreach($this->parameters as $p) $this->buffer .= $this->wStr($p);
                break;
        }
    }}