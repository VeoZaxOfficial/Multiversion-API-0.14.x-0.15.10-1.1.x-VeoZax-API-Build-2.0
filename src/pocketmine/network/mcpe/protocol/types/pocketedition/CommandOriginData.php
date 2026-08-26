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
namespace pocketmine\network\mcpe\protocol\types\pocketedition;
use pocketmine\network\mcpe\NetworkBinaryStream;
class CommandOriginData{
    public const ORIGIN_PLAYER = 0;
    public const ORIGIN_BLOCK = 1;
    public const ORIGIN_MINECART_BLOCK = 2;
    public const ORIGIN_DEV_CONSOLE = 3;
    public const ORIGIN_AUTOMATION_PLAYER = 4;
    public const ORIGIN_CLIENT_AUTOMATION = 5;
    public const ORIGIN_DEDICATED_SERVER = 6;
    public const ORIGIN_ENTITY = 7;
    public const ORIGIN_VIRTUAL = 8;
    public const ORIGIN_GAME_ARGUMENT = 9;
    public const ORIGIN_INTERNAL = 10;
    public $originType;
    public $sourceId;
    public $entityUniqueId;
    public $encapsulatedOrigin;
    public $outputReceiver;
    public $blockPosX;
    public $blockPosY;
    public $blockPosZ;
    public static function read(NetworkBinaryStream $in) : self{
        $originData = new self();
        $originData->originType = $in->getVarLong();
        $originData->sourceId = $in->getString();
        switch($originData->originType){
            case self::ORIGIN_PLAYER:
            case self::ORIGIN_DEV_CONSOLE:
            case self::ORIGIN_AUTOMATION_PLAYER:
            case self::ORIGIN_ENTITY:
                $originData->entityUniqueId = $in->getEntityUniqueId();
                break;
            case self::ORIGIN_VIRTUAL:
                $originData->encapsulatedOrigin = self::read($in);
                $originData->outputReceiver = self::read($in);
                $in->getSignedBlockPosition($originData->blockPosX, $originData->blockPosY, $originData->blockPosZ);
                break;
        }
        return $originData;
    }
    public function write(NetworkBinaryStream $out) : void{
        $out->putVarLong($this->originType);
        $out->putString($this->sourceId);
        switch($this->originType){
            case self::ORIGIN_PLAYER:
            case self::ORIGIN_DEV_CONSOLE:
            case self::ORIGIN_AUTOMATION_PLAYER:
            case self::ORIGIN_ENTITY:
                $out->putEntityUniqueId($this->entityUniqueId);
                break;
            case self::ORIGIN_VIRTUAL:
                $this->encapsulatedOrigin->write($out);
                $this->outputReceiver->write($out);
                $out->putSignedBlockPosition($this->blockPosX, $this->blockPosY, $this->blockPosZ);
                break;
        }
    }}