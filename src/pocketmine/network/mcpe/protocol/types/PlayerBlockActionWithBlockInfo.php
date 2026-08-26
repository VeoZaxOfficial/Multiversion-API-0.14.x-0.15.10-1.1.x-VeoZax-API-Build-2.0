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
namespace pocketmine\network\mcpe\protocol\types;
use pocketmine\network\mcpe\NetworkBinaryStream;use pocketmine\network\mcpe\protocol\PlayerActionPacket;
final class PlayerBlockActionWithBlockInfo implements PlayerBlockAction{
    public function __construct(
        private int $actionType,
        private int $x,
        private int $y,
        private int $z,
        private int $face
    ){
        if(!self::isValidActionType($actionType)){
            throw new \InvalidArgumentException("Invalid action type for " . self::class);
        }
    }
    public function getActionType() : int{ return $this->actionType; }
    public function getX() : int{ return $this->x; }
    public function getY() : int{ return $this->y; }
    public function getZ() : int{ return $this->z; }
    public function getFace() : int{ return $this->face; }
    public static function read(NetworkBinaryStream $in, int $actionType) : self{
        $x = $y = $z = 0;
        $in->getSignedBlockPosition($x, $y, $z);
        $face = $in->getVarInt();
        return new self($actionType, $x, $y, $z, $face);
    }
    public function write(NetworkBinaryStream $out) : void{
        $out->putSignedBlockPosition($this->x, $this->y, $this->z);
        $out->putVarInt($this->face);
    }
    public static function isValidActionType(int $actionType) : bool{
        return match($actionType){
            PlayerActionPacket::ACTION_ABORT_BREAK,
            PlayerActionPacket::ACTION_START_BREAK,
            PlayerActionPacket::ACTION_CONTINUE_BREAK,
            PlayerActionPacket::ACTION_PREDICT_DESTROY_BLOCK,
            PlayerActionPacket::ACTION_CONTINUE_DESTROY_BLOCK => true,
            default => false
        };
    }}