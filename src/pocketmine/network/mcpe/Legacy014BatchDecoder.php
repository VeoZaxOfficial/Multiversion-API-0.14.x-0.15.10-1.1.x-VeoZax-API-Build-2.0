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

declare(strict_types=1);namespace pocketmine\network\mcpe;
use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\AnimatePacket;use pocketmine\network\mcpe\protocol\ContainerClosePacket;use pocketmine\network\mcpe\protocol\InteractPacket;use pocketmine\network\mcpe\protocol\LoginPacket;use pocketmine\network\mcpe\protocol\MobEquipmentPacket;use pocketmine\network\mcpe\protocol\MovePlayerPacket;use pocketmine\network\mcpe\protocol\PlayerActionPacket;use pocketmine\network\mcpe\protocol\PlayerInputPacket;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;use pocketmine\network\mcpe\protocol\TextPacket;use pocketmine\network\mcpe\protocol\UseItemPacket;use pocketmine\network\mcpe\protocol\legacy\LegacyBinaryStream;use pocketmine\Player;use function ord;use function strlen;use function substr;use function unpack;use function zlib_decode;use function in_array;use function base64_encode;
class Legacy014BatchDecoder {
    private const ACCEPTED = [45, 46, 60, 70];
    private const BATCH = 0x92;
    private const LOGIN = 0x8f;
    private const MOVE_PLAYER_PACKET      = 0x9d;
    private const PLAYER_ACTION_PACKET    = 0xab;
    private const USE_ITEM_PACKET         = 0xaa;
    private const TEXT_PACKET             = 0x93;
    private const REQUEST_CHUNK_RADIUS    = 0xc8;
    private const INTERACT_PACKET         = 0xa9;
    private const CONTAINER_CLOSE_PACKET  = 0xb6;
    private const MOB_EQUIPMENT_PACKET    = 0xa7;
    private const ANIMATE_PACKET          = 0xb2;
    private const PLAYER_INPUT_PACKET     = 0xbe;
    private const DROP_ITEM_PACKET        = 0xb4;
    private const CRAFTING_EVENT_PACKET   = 0xbb;
    private const ADVENTURE_SETTINGS      = 0xbc;
    public static function handle(Player $player, string $buffer): void {
        if(strlen($buffer) < 2) return;
        $firstByte = ord($buffer[0]);
        if($firstByte === 0xfe && strlen($buffer) > 1) {
            $buffer = substr($buffer, 1);
            $firstByte = ord($buffer[0]);
        }
        if($firstByte === self::LOGIN) {
            self::handleLogin($player, $buffer);
            return;
        }
        if($firstByte !== self::BATCH) {
            return; 
        }
        if(strlen($buffer) < 5) return;
        $compLen   = unpack("N", substr($buffer, 1, 4))[1];
        $compressed = substr($buffer, 5, $compLen);
        if(strlen($compressed) === 0) return;
        $payload = @zlib_decode($compressed, 1024 * 1024 * 4);
        if($payload === false || $payload === '') return;
        $offset = 0;
        $total  = strlen($payload);
        $count  = 0;
        while($offset < $total && $count++ < 512) {
            if($offset + 4 > $total) break;
            $pktLen = unpack("N", substr($payload, $offset, 4))[1];
            $offset += 4;
            if($pktLen <= 0 || $offset + $pktLen > $total) break;
            $pktBuf = substr($payload, $offset, $pktLen);
            $offset += $pktLen;
            if(strlen($pktBuf) < 2) continue;
            $pid = ord($pktBuf[1]);
            self::dispatch($player, $pid, $pktBuf);
        }
    }
    private static function dispatch(Player $player, int $pid, string $buf): void {
        if($player->getSessionAdapter() === null) return;
        $s = new LegacyBinaryStream($buf, 2);
        try {
            switch($pid) {
                case self::LOGIN:
                    self::handleLogin($player, $buf);
                    break;
                case self::MOVE_PLAYER_PACKET:
                    $eid     = $s->getLong();
                    $x       = $s->getFloat();
                    $y       = $s->getFloat();
                    $z       = $s->getFloat();
                    $yaw     = $s->getFloat();
                    $bodyYaw = $s->getFloat();
                    $pitch   = $s->getFloat();
                    $mode    = $s->getByte();
                    $onGround= $s->getByte() > 0;
                    $pk = new MovePlayerPacket();
                    $pk->entityRuntimeId = $eid;
                    $pk->position  = new Vector3($x, $y, $z);
                    $pk->yaw       = $yaw;
                    $pk->headYaw   = $bodyYaw;
                    $pk->pitch     = $pitch;
                    $pk->mode      = $mode;
                    $pk->onGround  = $onGround;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleMovePlayer($pk);
                    break;
                case self::PLAYER_ACTION_PACKET:
                    $eid    = $s->getLong();
                    $action = $s->getInt();
                    $x      = $s->getInt();
                    $y      = $s->getInt();
                    $z      = $s->getInt();
                    $face   = $s->getInt();
                    $pk = new PlayerActionPacket();
                    $pk->entityRuntimeId = $eid;
                    $pk->action = $action;
                    $pk->x = $x; $pk->y = $y; $pk->z = $z;
                    $pk->rx = 0; $pk->ry = 0; $pk->rz = 0;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handlePlayerAction($pk);
                    break;
                case self::USE_ITEM_PACKET:
                    $x    = $s->getInt();
                    $y    = $s->getInt();
                    $z    = $s->getInt();
                    $face = $s->getByte();
                    $fx   = $s->getFloat(); $fy = $s->getFloat(); $fz = $s->getFloat();
                    $posX = $s->getFloat(); $posY = $s->getFloat(); $posZ = $s->getFloat();
                    $item = $s->getSlot();
                    $pk = new UseItemPacket();
                    $pk->x = $x; $pk->y = $y; $pk->z = $z;
                    $pk->face     = $face;
                    $pk->item     = $item;
                    $pk->clickPos = new Vector3($fx, $fy, $fz);
                    $pk->playerPos = new Vector3($posX, $posY, $posZ);
                    $pk->slot     = 0;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleUseItem($pk);
                    break;
                case self::TEXT_PACKET:
                    $type    = $s->getByte();
                    $source  = ($type === 1) ? $s->getString() : ""; 
                    $message = $s->getString();
                    $pk = new TextPacket();
                    $pk->type       = $type;
                    $pk->sourceName = $source;
                    $pk->message    = $message;
                    $pk->parameters = [];
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleText($pk);
                    break;
                case self::REQUEST_CHUNK_RADIUS:
                    $radius = $s->getInt();
                    $pk = new RequestChunkRadiusPacket();
                    $pk->radius    = $radius;
                    $pk->maxRadius = $radius;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleRequestChunkRadius($pk);
                    break;
                case self::INTERACT_PACKET:
                    $action = $s->getByte();
                    $target = $s->getLong();
                    $pk = new InteractPacket();
                    $pk->action = $action;
                    $pk->target = $target;
                    $pk->x = $pk->y = $pk->z = 0.0;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleInteract($pk);
                    break;
                case self::CONTAINER_CLOSE_PACKET:
                    $windowId = $s->getByte();
                    $pk = new ContainerClosePacket();
                    $pk->windowId   = $windowId;
                    $pk->windowType = 0;
                    $pk->server     = false;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleContainerClose($pk);
                    break;
                case self::MOB_EQUIPMENT_PACKET:
                    $eid          = $s->getLong();
                    $item         = $s->getSlot();
                    $slot         = $s->getByte();
                    $selectedSlot = $s->getByte();
                    $pk = new MobEquipmentPacket();
                    $pk->entityRuntimeId = $eid;
                    $pk->item            = $item instanceof \pocketmine\item\Item
                                          ? $item : \pocketmine\item\Item::get(0);
                    $pk->inventorySlot   = $slot;
                    $pk->hotbarSlot      = $selectedSlot;
                    $pk->windowId        = 0;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleMobEquipment($pk);
                    break;
                case self::ANIMATE_PACKET:
                    $action = $s->getByte();
                    $eid    = $s->getLong();
                    $pk = new AnimatePacket();
                    $pk->action          = $action;
                    $pk->entityRuntimeId = $eid;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handleAnimate($pk);
                    break;
                case self::PLAYER_INPUT_PACKET:
                    $motX    = $s->getFloat();
                    $motY    = $s->getFloat();
                    $flags   = $s->getByte();
                    $pk = new PlayerInputPacket();
                    $pk->motionX  = $motX;
                    $pk->motionY  = $motY;
                    $pk->jumping  = ($flags & 0x80) > 0;
                    $pk->sneaking = ($flags & 0x40) > 0;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handlePlayerInput($pk);
                    break;
                case self::DROP_ITEM_PACKET:
                    $pk = new PlayerActionPacket();
                    $pk->entityRuntimeId = $player->getId();
                    $pk->action = PlayerActionPacket::ACTION_DROP_ITEM;
                    $pk->x = $pk->y = $pk->z = $pk->rx = $pk->ry = $pk->rz = 0;
                    $pk->wasDecoded = true;
                    $player->getSessionAdapter()->handlePlayerAction($pk);
                    break;
                case self::CRAFTING_EVENT_PACKET:
                case self::ADVENTURE_SETTINGS:
                    break;
                default:
                    break;
            }
        } catch(\Throwable $e) {
        }
    }
    private static function handleLogin(Player $player, string $buffer): void {
        try {
            $startOffset = 0;
            if(strlen($buffer) > 1 && ord($buffer[0]) === 0xfe) {
                $startOffset = 2; 
            } elseif(strlen($buffer) > 0 && ord($buffer[0]) === self::LOGIN) {
                $startOffset = 1; 
            }
            $s = new LegacyBinaryStream($buffer, $startOffset);
            $username     = $s->getString();
            $proto1       = $s->getInt();
            $proto2       = $s->getInt();
            $clientId     = $s->getLong();
            $uuidBytes    = $s->get(16);
            $serverAddr   = $s->getString();
            $clientSecret = $s->getString();
            $skinName     = $s->getString();
            $skin         = $s->getString(); 
            if(!in_array($proto1, ProtocolInfo::ACCEPTED_PROTOCOLS)) {
                $player->close("", "Incompatible protocol version ($proto1)");
                return;
            }
            $pk = new LoginPacket();
            $pk->protocol    = $proto1;
            $pk->username    = $username;
            $pk->clientId    = $clientId;
            $pk->clientUUID  = strlen($uuidBytes) === 16
                ? \pocketmine\utils\UUID::fromBinary($uuidBytes)->toString()
                : \pocketmine\utils\UUID::fromRandom()->toString();
            $pk->serverAddress = $serverAddr;
            $pk->locale        = "en_US";
            $pk->xuid          = "";
            $pk->chainData     = [];
            $pk->isValidProtocol = true;
            $pk->clientData = [
                "SkinId"      => ($skinName !== "") ? $skinName : "Standard_Custom",
                "SkinData"    => base64_encode($skin),
                "GameVersion" => "0.14",
                "DeviceOS"    => 1,
                "DeviceModel" => "Legacy 0.14",
            ];
            $pk->clientDataJwt    = "";
            $pk->identityPublicKey = "";
            $pk->wasDecoded = true;
            if($player->getSessionAdapter() !== null) {
                $player->getSessionAdapter()->handleLogin($pk);
            }
        } catch(\Throwable $e) {
            $player->close("", "Login error: " . $e->getMessage());
        }
    }}