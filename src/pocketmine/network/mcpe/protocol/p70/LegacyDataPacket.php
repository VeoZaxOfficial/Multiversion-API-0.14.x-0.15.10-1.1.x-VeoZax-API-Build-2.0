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

declare(strict_types=1);namespace pocketmine\network\mcpe\protocol\p70;
use pocketmine\network\mcpe\NetworkSession;use pocketmine\network\mcpe\protocol\DataPacket;use pocketmine\item\Item;use pocketmine\utils\UUID;
abstract class LegacyDataPacket extends DataPacket {
    protected function encodeHeader() : void {}
    protected function decodeHeader() : void {}
    public function handle(NetworkSession $session) : bool { return true; }
    protected function wLong(int $v) : string {
        return pack('NN', ($v >> 32) & 0xffffffff, $v & 0xffffffff);
    }
    protected function wFloat(float $v) : string {
        $p = pack('f', $v);
        return (pack('L', 1) === "\x00\x00\x00\x01") ? $p : strrev($p);
    }
    protected function wStr(string $s) : string {
        return pack('n', strlen($s)) . $s;
    }
    protected function wInt(int $v) : string {
        return pack('N', $v);
    }
    protected function wShort(int $v) : string {
        return pack('n', $v);
    }
    protected function wByte(int $v) : string {
        return chr($v & 0xff);
    }
    protected function wUUID(UUID $uuid) : string {
        return $uuid->toBinary(); 
    }
    protected function wSlot(Item $item) : string {
        $id = $item->getId();
        if($id <= 0){
            return pack('n', 0) . "\xff\xff";
        }
        $buf  = pack('n', $id);
        $buf .= chr($item->getCount());
        $buf .= pack('n', $item->getDamage());
        $nbt  = $item->getCompoundTag() ?? '';
        $buf .= pack('n', strlen($nbt));
        $buf .= $nbt;
        return $buf;
    }
    protected function rBytes(int $n) : string {
        return $this->get($n);
    }
    protected function rLong() : int {
        $d = unpack('N2', $this->get(8));
        return PHP_INT_SIZE === 8 ? ($d[1] << 32) | $d[2] : $d[2];
    }
    protected function rFloat() : float {
        $raw = $this->get(4);
        return (pack('L', 1) === "\x00\x00\x00\x01")
            ? unpack('f', $raw)[1]
            : unpack('f', strrev($raw))[1];
    }
    protected function rStr() : string {
        $len = unpack('n', $this->get(2))[1];
        return $this->get($len);
    }
    protected function rInt() : int {
        return PHP_INT_SIZE === 8
            ? (unpack('N', $this->get(4))[1] << 32 >> 32)
            : unpack('N', $this->get(4))[1];
    }
    protected function rByte() : int {
        return $this->getByte();
    }
    protected function rShort() : int {
        return unpack('n', $this->get(2))[1];
    }}