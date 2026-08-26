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
namespace pocketmine\network\mcpe\protocol\p70;
use pocketmine\utils\BinaryStream;
class LoginPacket {
    public $username = "";
    public $protocol = 70;
    public $clientId = 0;
    public $clientUUID = "";
    public $serverAddress = "";
    public $clientSecret = "";
    public $skinName = "";
    public $skin = "";
    public static function fromBuffer(string $rawBuffer) : self {
        $stream = new BinaryStream($rawBuffer, 2);
        $pkt = new static();
        $pkt->username = $stream->get($stream->getShort());
        $stream->get(4); 
        $stream->get(4); 
        $pkt->protocol = 70;
        $pkt->clientId = self::readLong($stream->get(8));
        $uuidBytes = $stream->get(16);
        $pkt->clientUUID = self::formatUUID(bin2hex($uuidBytes));
        $pkt->serverAddress = $stream->get($stream->getShort());
        $pkt->clientSecret  = $stream->get($stream->getShort());
        $pkt->skinName      = $stream->get($stream->getShort());
        $pkt->skin          = $stream->get($stream->getShort());
        return $pkt;
    }
    public static function isLegacyLogin(string $rawBuffer) : bool {
        return strlen($rawBuffer) >= 2
            && ord($rawBuffer[0]) === 0x8e
            && ord($rawBuffer[1]) === 0x8f;
    }
    private static function readLong(string $str) : int {
        $data = unpack("N2", $str);
        if (PHP_INT_SIZE === 8) {
            return ($data[1] << 32) | $data[2];
        }
        return $data[2];
    }
    private static function formatUUID(string $hex) : string {
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex,  0, 8),
            substr($hex,  8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }}