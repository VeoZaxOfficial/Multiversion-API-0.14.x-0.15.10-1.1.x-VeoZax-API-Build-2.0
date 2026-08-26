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
use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\network\mcpe\multiversion\MetadataConvertor;
use pocketmine\network\mcpe\protocol\legacy\p84\AddEntityPacket       as LegAddEntity;use pocketmine\network\mcpe\protocol\legacy\p84\AddItemEntityPacket    as LegAddItemEntity;use pocketmine\network\mcpe\protocol\legacy\p84\AddPaintingPacket      as LegAddPainting;use pocketmine\network\mcpe\protocol\legacy\p84\AddPlayerPacket        as LegAddPlayer;use pocketmine\network\mcpe\protocol\legacy\p84\AdventureSettingsPacket as LegAdventure;use pocketmine\network\mcpe\protocol\legacy\p84\AnimatePacket          as LegAnimate;use pocketmine\network\mcpe\protocol\legacy\p84\BatchPacket            as LegBatch;use pocketmine\network\mcpe\protocol\legacy\p84\BlockEntityDataPacket  as LegBlockEntity;use pocketmine\network\mcpe\protocol\legacy\p84\BlockEventPacket       as LegBlockEvent;use pocketmine\network\mcpe\protocol\legacy\p84\ChangeDimensionPacket  as LegChangeDim;use pocketmine\network\mcpe\protocol\legacy\p84\ChunkRadiusUpdatedPacket as LegChunkRadius;use pocketmine\network\mcpe\protocol\legacy\p84\ContainerClosePacket   as LegContClose;use pocketmine\network\mcpe\protocol\legacy\p84\ContainerOpenPacket    as LegContOpen;use pocketmine\network\mcpe\protocol\legacy\p84\ContainerSetContentPacket as LegContContent;use pocketmine\network\mcpe\protocol\legacy\p84\ContainerSetDataPacket  as LegContData;use pocketmine\network\mcpe\protocol\legacy\p84\ContainerSetSlotPacket  as LegContSlot;use pocketmine\network\mcpe\protocol\legacy\p84\CraftingDataPacket     as LegCraftData;use pocketmine\network\mcpe\protocol\legacy\p84\CraftingEventPacket    as LegCraftEvent;use pocketmine\network\mcpe\protocol\legacy\p84\DisconnectPacket       as LegDisconnect;use pocketmine\network\mcpe\protocol\legacy\p84\DropItemPacket         as LegDropItem;use pocketmine\network\mcpe\protocol\legacy\p84\EntityEventPacket      as LegEntityEvent;use pocketmine\network\mcpe\protocol\legacy\p84\ExplodePacket          as LegExplode;use pocketmine\network\mcpe\protocol\legacy\p84\FullChunkDataPacket    as LegChunkData;use pocketmine\network\mcpe\protocol\legacy\p84\HurtArmorPacket        as LegHurtArmor;use pocketmine\network\mcpe\protocol\legacy\p84\InteractPacket         as LegInteract;use pocketmine\network\mcpe\protocol\legacy\p84\LevelEventPacket       as LegLevelEvent;use pocketmine\network\mcpe\protocol\legacy\p84\MobArmorEquipmentPacket as LegMobArmor;use pocketmine\network\mcpe\protocol\legacy\p84\MobEffectPacket        as LegMobEffect;use pocketmine\network\mcpe\protocol\legacy\p84\MobEquipmentPacket     as LegMobEquip;use pocketmine\network\mcpe\protocol\legacy\p84\MoveEntityPacket       as LegMoveEntity;use pocketmine\network\mcpe\protocol\legacy\p84\MovePlayerPacket       as LegMovePlayer;use pocketmine\network\mcpe\protocol\legacy\p84\PlayerActionPacket     as LegPlayerAction;use pocketmine\network\mcpe\protocol\legacy\p84\PlayerInputPacket      as LegPlayerInput;use pocketmine\network\mcpe\protocol\legacy\p84\PlayerListPacket       as LegPlayerList;use pocketmine\network\mcpe\protocol\legacy\p84\PlayStatusPacket       as LegPlayStatus;use pocketmine\network\mcpe\protocol\legacy\p84\RemoveBlockPacket      as LegRemoveBlock;use pocketmine\network\mcpe\protocol\legacy\p84\RemoveEntityPacket     as LegRemoveEntity;use pocketmine\network\mcpe\protocol\legacy\p84\RespawnPacket          as LegRespawn;use pocketmine\network\mcpe\protocol\legacy\p84\SetDifficultyPacket    as LegSetDifficulty;use pocketmine\network\mcpe\protocol\legacy\p84\SetEntityDataPacket    as LegSetEntityData;use pocketmine\network\mcpe\protocol\legacy\p84\SetEntityLinkPacket    as LegSetEntityLink;use pocketmine\network\mcpe\protocol\legacy\p84\SetEntityMotionPacket  as LegSetEntityMotion;use pocketmine\network\mcpe\protocol\legacy\p84\SetHealthPacket        as LegSetHealth;use pocketmine\network\mcpe\protocol\legacy\p84\SetPlayerGameTypePacket as LegSetGametype;use pocketmine\network\mcpe\protocol\legacy\p84\SetSpawnPositionPacket as LegSetSpawn;use pocketmine\network\mcpe\protocol\legacy\p84\SetTimePacket          as LegSetTime;use pocketmine\network\mcpe\protocol\legacy\p84\StartGamePacket        as LegStartGame;use pocketmine\network\mcpe\protocol\legacy\p84\TakeItemEntityPacket   as LegTakeItem;use pocketmine\network\mcpe\protocol\legacy\p84\TextPacket             as LegText;use pocketmine\network\mcpe\protocol\legacy\p84\UpdateAttributesPacket as LegUpdateAttr;use pocketmine\network\mcpe\protocol\legacy\p84\UpdateBlockPacket      as LegUpdateBlock;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;use pocketmine\network\mcpe\protocol\DisconnectPacket;use pocketmine\network\mcpe\protocol\BatchPacket;use pocketmine\network\mcpe\protocol\TextPacket;use pocketmine\network\mcpe\protocol\SetTimePacket;use pocketmine\network\mcpe\protocol\StartGamePacket;use pocketmine\network\mcpe\protocol\AddPlayerPacket;use pocketmine\network\mcpe\protocol\AddActorPacket;use pocketmine\network\mcpe\protocol\RemoveActorPacket;use pocketmine\network\mcpe\protocol\AddItemActorPacket;use pocketmine\network\mcpe\protocol\TakeItemActorPacket;use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;use pocketmine\network\mcpe\protocol\MovePlayerPacket;use pocketmine\network\mcpe\protocol\RemoveBlockPacket;use pocketmine\network\mcpe\protocol\UpdateBlockPacket;use pocketmine\network\mcpe\protocol\AddPaintingPacket;use pocketmine\network\mcpe\protocol\ExplodePacket;use pocketmine\network\mcpe\protocol\LevelEventPacket;use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;use pocketmine\network\mcpe\protocol\BlockEventPacket;use pocketmine\network\mcpe\protocol\ActorEventPacket;use pocketmine\network\mcpe\protocol\MobEffectPacket;use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;use pocketmine\network\mcpe\protocol\MobEquipmentPacket;use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;use pocketmine\network\mcpe\protocol\ContainerSetSlotPacket;use pocketmine\network\mcpe\protocol\InteractPacket;use pocketmine\network\mcpe\protocol\HurtArmorPacket;use pocketmine\network\mcpe\protocol\SetActorDataPacket;use pocketmine\network\mcpe\protocol\SetActorMotionPacket;use pocketmine\network\mcpe\protocol\SetActorLinkPacket;use pocketmine\network\mcpe\protocol\SetHealthPacket;use pocketmine\network\mcpe\protocol\SetSpawnPositionPacket;use pocketmine\network\mcpe\protocol\AnimatePacket;use pocketmine\network\mcpe\protocol\RespawnPacket;use pocketmine\network\mcpe\protocol\ContainerOpenPacket;use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;use pocketmine\network\mcpe\protocol\ContainerClosePacket;use pocketmine\network\mcpe\protocol\InventorySlotPacket;use pocketmine\network\mcpe\protocol\InventoryContentPacket;use pocketmine\network\mcpe\protocol\CraftingDataPacket;use pocketmine\network\mcpe\protocol\CraftingEventPacket;use pocketmine\network\mcpe\protocol\DropItemPacket;use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;use pocketmine\network\mcpe\protocol\BlockActorDataPacket;use pocketmine\network\mcpe\protocol\PlayerInputPacket;use pocketmine\network\mcpe\protocol\LevelChunkPacket;use pocketmine\network\mcpe\protocol\SetDifficultyPacket;use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;use pocketmine\network\mcpe\protocol\PlayerListPacket;use pocketmine\network\mcpe\protocol\ChunkRadiusUpdatedPacket;use pocketmine\Player;use pocketmine\Server;use pocketmine\block\Block;use pocketmine\network\mcpe\multiversion\MultiversionEnums;use pocketmine\level\particle\Particle;
class P84VersionManager {
    public static function parsePacket(Player $player, $packet): ?\pocketmine\network\mcpe\protocol\legacy\p84\DataPacket {
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\DataPacket) {
            return $packet;
        }
        if($packet instanceof PlayStatusPacket) {
            $pk = new LegPlayStatus();
            $pk->status = $packet->status;
            return $pk;
        }
        if($packet instanceof DisconnectPacket) {
            $pk = new LegDisconnect();
            $pk->message = $packet->message ?? "";
            return $pk;
        }
        if($packet instanceof TextPacket) {
            $pk = new LegText();
            static $modernToLegacyType = [
                TextPacket::TYPE_RAW           => LegText::TYPE_RAW,
                TextPacket::TYPE_CHAT          => LegText::TYPE_CHAT,
                TextPacket::TYPE_TRANSLATION   => LegText::TYPE_TRANSLATION,
                TextPacket::TYPE_POPUP         => LegText::TYPE_POPUP,
                TextPacket::TYPE_JUKEBOX_POPUP => LegText::TYPE_POPUP,
                TextPacket::TYPE_TIP           => LegText::TYPE_TIP,
                TextPacket::TYPE_SYSTEM        => LegText::TYPE_SYSTEM,
            ];
            $pk->type = $modernToLegacyType[$packet->type] ?? LegText::TYPE_RAW;
            if(property_exists($packet, 'sourceName')){
                $pk->source = $packet->sourceName ?? "";
            }elseif(property_exists($packet, 'source')){
                $pk->source = $packet->source ?? "";
            }else{
                $pk->source = "";
            }
            $pk->message    = property_exists($packet, 'message') ? ($packet->message ?? "") : "";
            $pk->parameters = property_exists($packet, 'parameters') ? ($packet->parameters ?? []) : [];
            return $pk;
        }
        if($packet instanceof SetTimePacket) {
            $pk = new LegSetTime();
            $pk->time    = $packet->time;
            $pk->started = true;
            return $pk;
        }
        if($packet instanceof StartGamePacket) {
            $pk = new LegStartGame();
            $pk->seed      = $packet->seed ?? -1;
            $pk->dimension = isset($packet->spawnSettings)
                ? ($packet->spawnSettings->getDimension() ?? 0) : 0;
            $pk->generator = 1;
            $pk->gamemode  = ($packet->playerGamemode ?? 0) & 0x01;
            $pk->eid       = 0;
            $pk->spawnX    = $packet->spawnX ?? 0;
            $pk->spawnY    = $packet->spawnY ?? 64;
            $pk->spawnZ    = $packet->spawnZ ?? 0;
            $pk->x = isset($packet->playerPosition) ? $packet->playerPosition->x : 0.0;
            $pk->y = isset($packet->playerPosition) ? $packet->playerPosition->y : 64.0;
            $pk->z = isset($packet->playerPosition) ? $packet->playerPosition->z : 0.0;
            $pk->unknown = "";
            return $pk;
        }
        if($packet instanceof AddPlayerPacket) {
            $pk = new LegAddPlayer();
            $pk->uuid     = $packet->uuid;
            $pk->username = $packet->username;
            $pk->eid      = $packet->entityRuntimeId ?? 0;
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 64.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->speedX = isset($packet->motion) ? $packet->motion->x : 0.0;
            $pk->speedY = isset($packet->motion) ? $packet->motion->y : 0.0;
            $pk->speedZ = isset($packet->motion) ? $packet->motion->z : 0.0;
            $pk->pitch    = $packet->pitch ?? 0.0;
            $pk->yaw      = $packet->yaw ?? 0.0;
            $pk->item     = $packet->item ?? null;
            $pk->metadata = MetadataConvertor::updateMeta($packet->metadata ?? [], ProtocolInfo::PROTOCOL_81);
            return $pk;
        }
        if($packet instanceof AddActorPacket) {
            $pk = new LegAddEntity();
            $pk->eid    = $packet->entityRuntimeId ?? 0;
            $pk->type   = $packet->type ?? 0;
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 64.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->speedX = isset($packet->motion) ? $packet->motion->x : 0.0;
            $pk->speedY = isset($packet->motion) ? $packet->motion->y : 0.0;
            $pk->speedZ = isset($packet->motion) ? $packet->motion->z : 0.0;
            $pk->yaw      = $packet->yaw ?? 0.0;
            $pk->pitch    = $packet->pitch ?? 0.0;
            $pk->metadata = MetadataConvertor::updateMeta($packet->metadata ?? [], ProtocolInfo::PROTOCOL_81);
            $pk->links    = [];
            return $pk;
        }
        if($packet instanceof RemoveActorPacket) {
            $pk = new LegRemoveEntity();
            $pk->eid = $packet->entityUniqueId ?? 0;
            return $pk;
        }
        if($packet instanceof AddItemActorPacket) {
            $pk = new LegAddItemEntity();
            $pk->eid  = $packet->entityRuntimeId ?? 0;
            $pk->item = $packet->item instanceof \pocketmine\item\Item
                ? $packet->item : \pocketmine\item\Item::get(0, 0, 0);
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 64.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->speedX = isset($packet->motion) ? $packet->motion->x : 0.0;
            $pk->speedY = isset($packet->motion) ? $packet->motion->y : 0.0;
            $pk->speedZ = isset($packet->motion) ? $packet->motion->z : 0.0;
            return $pk;
        }
        if($packet instanceof TakeItemActorPacket) {
            $pk = new LegTakeItem();
            $pk->target = $packet->target ?? 0;
            $realEid = $packet->eid ?? 0;
            $pk->eid = ($realEid === $player->getId()) ? 0 : $realEid;
            return $pk;
        }
        if($packet instanceof MoveActorAbsolutePacket) {
            $pk = new LegMoveEntity();
            $eid = $packet->entityRuntimeId ?? 0;
            $x = isset($packet->position) ? $packet->position->x : 0.0;
            $y = isset($packet->position) ? $packet->position->y : 64.0;
            $z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->entities = [[$eid, $x, $y, $z,
                $packet->yaw ?? 0.0, $packet->headYaw ?? $packet->yaw ?? 0.0, $packet->pitch ?? 0.0]];
            return $pk;
        }
        if($packet instanceof MovePlayerPacket) {
            $pk = new LegMovePlayer();
            $pk->eid  = ($packet->entityRuntimeId ?? 0) === $player->getId() ? 0 : ($packet->entityRuntimeId ?? 0);
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 64.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->yaw      = $packet->yaw ?? 0.0;
            $pk->bodyYaw  = $packet->headYaw ?? $packet->yaw ?? 0.0;
            $pk->pitch    = $packet->pitch ?? 0.0;
            switch ($packet->mode ?? 0) {
                case \pocketmine\network\mcpe\protocol\MovePlayerPacket::MODE_TELEPORT:
                    $pk->mode = LegMovePlayer::MODE_RESET;
                    break;
                case \pocketmine\network\mcpe\protocol\MovePlayerPacket::MODE_PITCH:
                    $pk->mode = LegMovePlayer::MODE_ROTATION;
                    break;
                case \pocketmine\network\mcpe\protocol\MovePlayerPacket::MODE_RESET:
                    $pk->mode = LegMovePlayer::MODE_RESET;
                    break;
                default:
                    $pk->mode = LegMovePlayer::MODE_NORMAL;
            }
            $pk->onGround = $packet->onGround ?? false;
            return $pk;
        }
        if($packet instanceof RemoveBlockPacket) {
            $pk = new LegRemoveBlock();
            $pk->eid = 0;
            $pk->x   = $packet->x ?? 0;
            $pk->y   = $packet->y ?? 0;
            $pk->z   = $packet->z ?? 0;
            return $pk;
        }
        if($packet instanceof UpdateBlockPacket) {
            $pk = new LegUpdateBlock();
            $pk->x         = $packet->x    ?? 0;
            $pk->z         = $packet->z    ?? 0;
            $pk->y         = $packet->y    ?? 0;
            $pk->blockId   = $packet->blockId   ?? 0;
            $pk->blockData = $packet->blockMeta ?? $packet->blockData ?? 0;
            $pk->flags     = $packet->flags     ?? 0;
            return $pk;
        }
        if($packet instanceof AddPaintingPacket) {
            $pk = new LegAddPainting();
            $pk->eid       = $packet->entityRuntimeId ?? 0;
            $pk->x         = $packet->x ?? 0;
            $pk->y         = $packet->y ?? 0;
            $pk->z         = $packet->z ?? 0;
            $pk->direction = $packet->direction ?? 0;
            $pk->title     = $packet->title ?? "";
            return $pk;
        }
        if($packet instanceof ExplodePacket) {
            $pk = new LegExplode();
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 0.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->radius  = $packet->radius ?? 0.0;
            $pk->records = array_filter(
                $packet->records ?? [],
                fn($r) => $r instanceof \pocketmine\math\Vector3
            );
            return $pk;
        }
        if($packet instanceof LevelEventPacket) {
            $evid = $packet->evid ?? $packet->eventId ?? 0;
            if ($evid >= LevelEventPacket::EVENT_BLOCK_START_BREAK && $evid <= LevelEventPacket::EVENT_PARTICLE_PUNCH_BLOCK_EAST) {
                return null;
            }
            $pk = new LegLevelEvent();
            $evidToSend = $evid;
            if($evid >= LevelEventPacket::EVENT_ADD_PARTICLE_MASK){
                $internalType = $evid % LevelEventPacket::EVENT_ADD_PARTICLE_MASK;
                try{
                    $wireType = MultiversionEnums::getParticleId(ProtocolInfo::PROTOCOL_81, $internalType);
                    $evidToSend = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | $wireType;
                }catch(\Throwable $e){
                    return null;
                }
            }
            $pk->evid = $evidToSend;
            $pk->x = isset($packet->position) ? $packet->position->x : ($packet->x ?? 0.0);
            $pk->y = isset($packet->position) ? $packet->position->y : ($packet->y ?? 0.0);
            $pk->z = isset($packet->position) ? $packet->position->z : ($packet->z ?? 0.0);
            $data = $packet->data ?? 0;
            if($evid === LevelEventPacket::EVENT_PARTICLE_DESTROY){
                $id = $data >> Block::INTERNAL_METADATA_BITS;
                $meta = $data & Block::INTERNAL_METADATA_MASK;
                $data = $id + ($meta << 8);
            }
            if($evid >= LevelEventPacket::EVENT_ADD_PARTICLE_MASK){
                $particleId = $evid % LevelEventPacket::EVENT_ADD_PARTICLE_MASK;
                if($particleId === Particle::TYPE_ITEM_BREAK){
                    $itemId = ($data >> 16) & 0xFFFF;
                    $meta   = $data & 0xFFFF;
                    $data   = ($meta << 16) | $itemId;
                }
            }
            $pk->data = $data;
            return $pk;
        }
        if($packet instanceof LevelSoundEventPacket) {
            static $soundToLegacyEvent = [
                LevelSoundEventPacket::SOUND_BOW                => LegLevelEvent::EVENT_SOUND_SHOOT,
                LevelSoundEventPacket::SOUND_SHOOT               => LegLevelEvent::EVENT_SOUND_SHOOT,
                LevelSoundEventPacket::SOUND_FIZZ                => LegLevelEvent::EVENT_SOUND_FIZZ,
                LevelSoundEventPacket::SOUND_ITEM_FIZZ           => LegLevelEvent::EVENT_SOUND_FIZZ,
                LevelSoundEventPacket::SOUND_EXPLODE             => LegLevelEvent::EVENT_SOUND_EXPLODE,
                LevelSoundEventPacket::SOUND_SPLASH              => LegLevelEvent::EVENT_SOUND_SPLASH,
                LevelSoundEventPacket::SOUND_DOOR_OPEN           => LegLevelEvent::EVENT_SOUND_DOOR,
                LevelSoundEventPacket::SOUND_DOOR_CLOSE          => LegLevelEvent::EVENT_SOUND_DOOR,
                LevelSoundEventPacket::SOUND_TRAPDOOR_OPEN       => LegLevelEvent::EVENT_SOUND_DOOR,
                LevelSoundEventPacket::SOUND_TRAPDOOR_CLOSE      => LegLevelEvent::EVENT_SOUND_DOOR,
                LevelSoundEventPacket::SOUND_FENCE_GATE_OPEN     => LegLevelEvent::EVENT_SOUND_DOOR,
                LevelSoundEventPacket::SOUND_FENCE_GATE_CLOSE    => LegLevelEvent::EVENT_SOUND_DOOR,
                LevelSoundEventPacket::SOUND_BUTTON_CLICK_ON     => LegLevelEvent::EVENT_SOUND_BUTTON_CLICK,
                LevelSoundEventPacket::SOUND_BUTTON_CLICK_OFF    => LegLevelEvent::EVENT_SOUND_BUTTON_CLICK,
                LevelSoundEventPacket::SOUND_BLOCK_CLICK         => LegLevelEvent::EVENT_SOUND_CLICK,
                LevelSoundEventPacket::SOUND_BLOCK_CLICK_FAIL    => LegLevelEvent::EVENT_SOUND_CLICK_FAIL,
                LevelSoundEventPacket::SOUND_RANDOM_ANVIL_USE    => LegLevelEvent::EVENT_SOUND_ANVIL_USE,
            ];
            $legacyEvid = $soundToLegacyEvent[$packet->sound ?? -1] ?? null;
            if($legacyEvid === null) {
                return null;
            }
            $pk = new LegLevelEvent();
            $pk->evid = $legacyEvid;
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 0.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            $pk->data = 0;
            return $pk;
        }
        if($packet instanceof BlockEventPacket) {
            $pk = new LegBlockEvent();
            $pk->x     = $packet->x ?? 0;
            $pk->y     = $packet->y ?? 0;
            $pk->z     = $packet->z ?? 0;
            $pk->case1 = $packet->eventType ?? 0;
            $pk->case2 = $packet->eventData ?? 0;
            return $pk;
        }
        if($packet instanceof ActorEventPacket) {
            $pk = new LegEntityEvent();
            $realEid = $packet->entityRuntimeId ?? 0;
            $pk->eid   = ($realEid === $player->getId()) ? 0 : $realEid;
            $pk->event = $packet->event ?? 0;
            return $pk;
        }
        if($packet instanceof MobEffectPacket) {
            $pk = new LegMobEffect();
            $realEid = $packet->entityRuntimeId ?? 0;
            $pk->eid       = ($realEid === $player->getId()) ? 0 : $realEid;
            $pk->eventId   = $packet->eventId ?? 0;
            $pk->effectId  = $packet->effectId ?? 0;
            $pk->amplifier = $packet->amplifier ?? 0;
            $pk->particles = $packet->particles ?? true;
            $pk->duration  = $packet->duration ?? 0;
            return $pk;
        }
        if($packet instanceof UpdateAttributesPacket) {
            $pk = new LegUpdateAttr();
            $realEid = $packet->entityRuntimeId ?? 0;
            $pk->entityId = ($realEid === $player->getId()) ? 0 : $realEid;
            $pk->entries  = $packet->entries ?? [];
            return $pk;
        }
        if($packet instanceof MobEquipmentPacket) {
            $pk = new LegMobEquip();
            $pk->eid          = $packet->entityRuntimeId ?? 0;
            $pk->item         = $packet->item instanceof \pocketmine\item\Item
                ? $packet->item : \pocketmine\item\Item::get(0, 0, 0);
            $pk->slot         = $packet->inventorySlot ?? $packet->slot ?? 0;
            $pk->selectedSlot = $packet->hotbarSlot ?? 0;
            return $pk;
        }
        if($packet instanceof MobArmorEquipmentPacket) {
            $pk = new LegMobArmor();
            $pk->eid   = $packet->entityRuntimeId ?? 0;
            $air = \pocketmine\item\Item::get(0, 0, 0);
            $pk->slots = [
                $packet->head  instanceof \pocketmine\item\Item ? $packet->head  : $air,
                $packet->chest instanceof \pocketmine\item\Item ? $packet->chest : $air,
                $packet->legs  instanceof \pocketmine\item\Item ? $packet->legs  : $air,
                $packet->feet  instanceof \pocketmine\item\Item ? $packet->feet  : $air,
            ];
            return $pk;
        }
        if($packet instanceof SetActorDataPacket) {
            $pk = new LegSetEntityData();
            $realEid = $packet->entityRuntimeId ?? 0;
            $pk->eid = ($realEid === $player->getId()) ? 0 : $realEid;
            $pk->metadata = \pocketmine\network\mcpe\multiversion\MetadataConvertor::updateMeta(
                $packet->metadata ?? [],
                ProtocolInfo::PROTOCOL_81
            );
            return $pk;
        }
        if($packet instanceof SetActorMotionPacket) {
            $pk = new LegSetEntityMotion();
            $realEid = $packet->entityRuntimeId ?? 0;
            $eid = ($realEid === $player->getId()) ? 0 : $realEid;
            $legacyHorizontalScale = 0.55;
            $mx  = (isset($packet->motion) ? $packet->motion->x : 0.0) * $legacyHorizontalScale;
            $my  = isset($packet->motion) ? $packet->motion->y : 0.0;
            $mz  = (isset($packet->motion) ? $packet->motion->z : 0.0) * $legacyHorizontalScale;
            $pk->entities = [[$eid, $mx, $my, $mz]];
            return $pk;
        }
        if($packet instanceof SetActorLinkPacket) {
            $pk = new LegSetEntityLink();
            if (isset($packet->link)) {
                $pk->from = $packet->link->fromEntityUniqueId ?? 0;
                $pk->to   = $packet->link->toEntityUniqueId ?? 0;
                $pk->type = $packet->link->type ?? 0;
            } else {
                $pk->from = 0; $pk->to = 0; $pk->type = 0;
            }
            return $pk;
        }
        if($packet instanceof SetHealthPacket) {
            $pk = new LegSetHealth();
            $pk->health = $packet->health ?? 20;
            return $pk;
        }
        if($packet instanceof SetSpawnPositionPacket) {
            $pk = new LegSetSpawn();
            $pk->x = $packet->x ?? 0;
            $pk->y = $packet->y ?? 64;
            $pk->z = $packet->z ?? 0;
            return $pk;
        }
        if($packet instanceof AnimatePacket) {
            $pk = new LegAnimate();
            $pk->action = $packet->action ?? 0;
            $realEid = $packet->entityRuntimeId ?? 0;
            $pk->eid    = ($realEid === $player->getId()) ? 0 : $realEid;
            return $pk;
        }
        if($packet instanceof RespawnPacket) {
            $pk = new LegRespawn();
            $pk->x = isset($packet->position) ? $packet->position->x : 0.0;
            $pk->y = isset($packet->position) ? $packet->position->y : 64.0;
            $pk->z = isset($packet->position) ? $packet->position->z : 0.0;
            return $pk;
        }
        if($packet instanceof ContainerOpenPacket) {
            $pk = new LegContOpen();
            $pk->windowid = $packet->windowId ?? 0;
            $pk->type     = $packet->type ?? 0;
            static $slotCounts = [
                0 => 27,  
                1 => 9,   
                2 => 3,   
                3 => 9,   
                4 => 5,   
                5 => 3,   
                6 => 9,   
                7 => 9,   
                8 => 5,   
            ];
            $pk->slots    = $slotCounts[$packet->type ?? 0] ?? 27;
            $pk->x        = $packet->x ?? 0;
            $pk->y        = $packet->y ?? 0;
            $pk->z        = $packet->z ?? 0;
            $pk->entityId = $packet->entityUniqueId ?? -1;
            return $pk;
        }
        if($packet instanceof ContainerClosePacket) {
            $pk = new LegContClose();
            $pk->windowid = $packet->windowId ?? 0;
            return $pk;
        }
        if($packet instanceof ContainerSetDataPacket) {
            $pk = new LegContData();
            $pk->windowid = $packet->windowId ?? 0;
            $pk->property = $packet->property ?? 0;
            $pk->value    = $packet->value ?? 0;
            return $pk;
        }
        if($packet instanceof ClientboundMapItemDataPacket) {
            return null;
        }
        if($packet instanceof InventorySlotPacket) {
            $pk = new LegContSlot();
            $pk->windowid   = $packet->windowId ?? 0;
            $pk->slot       = $packet->inventorySlot ?? 0;
            $pk->hotbarSlot = 0;
            $pk->item       = $packet->item instanceof \pocketmine\item\Item
                ? $packet->item : \pocketmine\item\Item::get(0, 0, 0);
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\ContainerSetSlotPacket) {
            $pk = new LegContSlot();
            $pk->windowid   = $packet->windowid ?? 0;
            $pk->slot       = $packet->slot ?? 0;
            $pk->hotbarSlot = $packet->hotbarSlot ?? 0;
            $pk->item       = $packet->item instanceof \pocketmine\item\Item
                ? $packet->item : \pocketmine\item\Item::get(0, 0, 0);
            return $pk;
        }
        if($packet instanceof InventoryContentPacket) {
            $pk = new LegContContent();
            $pk->windowid = $packet->windowId ?? 0;
            $air = \pocketmine\item\Item::get(0, 0, 0);
            $pk->slots = array_map(
                fn($item) => $item instanceof \pocketmine\item\Item ? $item : $air,
                $packet->items ?? []
            );
            $pk->hotbar = [];
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\ContainerSetContentPacket) {
            $pk = new LegContContent();
            $pk->windowid = $packet->windowid ?? 0;
            $air = \pocketmine\item\Item::get(0, 0, 0);
            $pk->slots = array_map(
                fn($item) => $item instanceof \pocketmine\item\Item ? $item : $air,
                $packet->slots ?? []
            );
            $pk->hotbar = $packet->hotbar ?? [];
            return $pk;
        }
        if($packet instanceof CraftingDataPacket) {
            $pk = new LegCraftData();
            $pk->entries      = $packet->entries;
            $pk->cleanRecipes = $packet->cleanRecipes ?? false;
            return $pk;
        }
        if($packet instanceof AdventureSettingsPacket) {
            $pk = new LegAdventure();
            $pk->flags            = $packet->flags ?? 0;
            $pk->userPermission   = $packet->commandPermission ?? 0;
            $pk->globalPermission = $packet->playerPermission ?? 0;
            return $pk;
        }
        if($packet instanceof BlockActorDataPacket) {
            $pk = new LegBlockEntity();
            $pk->x        = $packet->x ?? 0;
            $pk->y        = $packet->y ?? 0;
            $pk->z        = $packet->z ?? 0;
            $pk->namedtag = $packet->namedtag ?? "";
            return $pk;
        }
        if($packet instanceof LevelChunkPacket) {
            $pk = new LegChunkData();
            $chunkPos   = $packet->getChunkPosition();
            $pk->chunkX = $chunkPos->getX();
            $pk->chunkZ = $chunkPos->getZ();
            $pk->order  = 1; 
            $pk->data   = $packet->getExtraPayload();
            return $pk;
        }
        if($packet instanceof SetDifficultyPacket) {
            $pk = new LegSetDifficulty();
            $pk->difficulty = $packet->difficulty ?? 0;
            return $pk;
        }
        if($packet instanceof ChangeDimensionPacket) {
            $pk = new LegChangeDim();
            $pk->dimension = $packet->dimension ?? 0;
            return $pk;
        }
        if($packet instanceof SetPlayerGameTypePacket) {
            $pk = new LegSetGametype();
            $pk->gamemode = $packet->gamemode ?? 0;
            return $pk;
        }
        if($packet instanceof PlayerListPacket) {
            $pk = new LegPlayerList();
            $pk->type = $packet->type ?? 0;
            $pk->entries = [];
            foreach ($packet->entries ?? [] as $entry) {
                if($pk->type === 0) { 
                    $skinId   = "";
                    $skinData = "";
                    if(isset($entry->skin) && $entry->skin instanceof \pocketmine\entity\Skin) {
                        $skinId   = $entry->skin->getSkinId();
                        $skinData = $entry->skin->getSkinData();
                        $skinLen = \strlen($skinData);
                        if($skinLen > 64 * 64 * 4){
                            $skinData = \substr($skinData, 0, 64 * 64 * 4);
                        }
                    }
                    $pk->entries[] = [
                        $entry->uuid ?? \pocketmine\utils\UUID::fromRandom(),
                        $entry->entityUniqueId ?? 0,
                        $entry->username ?? "",
                        $skinId,
                        $skinData,
                    ];
                } else { 
                    $pk->entries[] = [
                        $entry->uuid ?? \pocketmine\utils\UUID::fromRandom(),
                    ];
                }
            }
            return $pk;
        }
        if($packet instanceof ChunkRadiusUpdatedPacket) {
            $pk = new LegChunkRadius();
            $pk->radius = $packet->radius ?? 8;
            return $pk;
        }
        return null;
    }
    public static function parseLegacyPacket(Player $player, \pocketmine\network\mcpe\protocol\legacy\p84\DataPacket $packet): ?\pocketmine\network\mcpe\protocol\DataPacket {
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\RequestChunkRadiusPacket) {
            $pk = new \pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->radius = $packet->radius;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegAnimate) {
            $pk = new AnimatePacket();
            $pk->setProtocol($player->getProtocol());
            $pk->action = $packet->action;
            $pk->entityRuntimeId = $packet->eid;
            $pk->rowingTime = 0.0; 
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegInteract) {
            $pk = new InteractPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->action = $packet->action;
            $pk->target = $packet->target;
            $pk->x = 0.0;
            $pk->y = 0.0;
            $pk->z = 0.0;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\MovePlayerPacket) {
            $pk = new \pocketmine\network\mcpe\protocol\MovePlayerPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->entityRuntimeId = $packet->eid;
            $pk->position = new \pocketmine\math\Vector3($packet->x, $packet->y, $packet->z);
            $pk->yaw = $packet->yaw;
            $pk->headYaw = $packet->bodyYaw;
            $pk->pitch = $packet->pitch;
            $pk->mode = $packet->mode;
            $pk->onGround = $packet->onGround;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\PlayerActionPacket) {
            $pk = new \pocketmine\network\mcpe\protocol\PlayerActionPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->entityRuntimeId = $packet->eid;
            $pk->action = \pocketmine\network\mcpe\multiversion\MultiversionEnums::getPlayerActionName($player->getProtocol(), $packet->action);
            $pk->x = $packet->x;
            $pk->y = $packet->y;
            $pk->z = $packet->z;
            $pk->face = $packet->face;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket) {
            static $legacyToModernType = [
                \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::TYPE_RAW         => TextPacket::TYPE_RAW,
                \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::TYPE_CHAT        => TextPacket::TYPE_CHAT,
                \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::TYPE_TRANSLATION => TextPacket::TYPE_TRANSLATION,
                \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::TYPE_POPUP       => TextPacket::TYPE_POPUP,
                \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::TYPE_TIP         => TextPacket::TYPE_TIP,
                \pocketmine\network\mcpe\protocol\legacy\p84\TextPacket::TYPE_SYSTEM      => TextPacket::TYPE_SYSTEM,
            ];
            $pk = new TextPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->type       = $legacyToModernType[$packet->type] ?? TextPacket::TYPE_CHAT;
            $pk->sourceName = $packet->source ?? "";
            $pk->message    = $packet->message ?? "";
            $pk->parameters = $packet->parameters ?? [];
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\UseItemPacket) {
            $pk = new \pocketmine\network\mcpe\protocol\InventoryTransactionPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->requestId = 0;
            $pk->requestChangedSlots = [];
            if ($packet->face === 0xff) {
                $pk->trData = \pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData::new(
                    [],
                    \pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData::ACTION_CLICK_AIR,
                    \pocketmine\network\mcpe\protocol\types\inventory\TriggerType::PLAYER_INPUT,
                    new \pocketmine\math\Vector3(0, 0, 0), 
                    0, 
                    $player->getInventory()->getHeldItemIndex(),
                    $player->getInventory()->getItemInHand(), 
                    new \pocketmine\math\Vector3($packet->posX, $packet->posY, $packet->posZ),
                    new \pocketmine\math\Vector3(0, 0, 0), 
                    0,
                    \pocketmine\network\mcpe\protocol\types\inventory\PredictedResult::SUCCESS
                );
                $pk->wasDecoded = true;
                return $pk;
            }
            $pk->trData = \pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData::new(
                [],
                \pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData::ACTION_CLICK_BLOCK,
                \pocketmine\network\mcpe\protocol\types\inventory\TriggerType::PLAYER_INPUT,
                new \pocketmine\math\Vector3($packet->x, $packet->y, $packet->z),
                $packet->face,
                $player->getInventory()->getHeldItemIndex(),
                $player->getInventory()->getItemInHand(),
                new \pocketmine\math\Vector3($packet->posX, $packet->posY, $packet->posZ),
                new \pocketmine\math\Vector3($packet->fx, $packet->fy, $packet->fz),
                0,
                \pocketmine\network\mcpe\protocol\types\inventory\PredictedResult::SUCCESS
            );
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof \pocketmine\network\mcpe\protocol\legacy\p84\RemoveBlockPacket) {
            $pos = new \pocketmine\math\Vector3($packet->x, $packet->y, $packet->z);
            $pk = new \pocketmine\network\mcpe\protocol\InventoryTransactionPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->requestId = 0;
            $pk->requestChangedSlots = [];
            $pk->trData = \pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData::new(
                [],
                \pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData::ACTION_BREAK_BLOCK,
                \pocketmine\network\mcpe\protocol\types\inventory\TriggerType::PLAYER_INPUT,
                $pos,
                0,
                $player->getInventory()->getHeldItemIndex(),
                $player->getInventory()->getItemInHand(),
                $pos,
                new \pocketmine\math\Vector3(0.0, 0.0, 0.0),
                0,
                \pocketmine\network\mcpe\protocol\types\inventory\PredictedResult::SUCCESS
            );
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegMobEquip) {
            $pk = new MobEquipmentPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->entityRuntimeId = $packet->eid;
            $pk->item            = $packet->item;
            $pk->inventorySlot   = $packet->slot;
            $pk->hotbarSlot      = $packet->selectedSlot;
            $pk->windowId        = 0; 
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegMobArmor) {
            $pk = new MobArmorEquipmentPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->entityRuntimeId = $packet->eid;
            $pk->head  = $packet->slots[0];
            $pk->chest = $packet->slots[1];
            $pk->legs  = $packet->slots[2];
            $pk->feet  = $packet->slots[3];
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegContSlot) {
            $pk = new ContainerSetSlotPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->windowid   = $packet->windowid;
            $pk->slot       = $packet->slot;
            $pk->hotbarSlot = $packet->hotbarSlot;
            $pk->item       = $packet->item;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegRespawn) {
            $pk = new RespawnPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->position = new \pocketmine\math\Vector3($packet->x, $packet->y, $packet->z);
            $pk->respawnState = RespawnPacket::CLIENT_READY_TO_SPAWN;
            $pk->entityRuntimeId = $player->getId();
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegDropItem) {
            $pk = new DropItemPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->type = $packet->type;
            $pk->item = $packet->item;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegContClose) {
            $pk = new ContainerClosePacket();
            $pk->setProtocol($player->getProtocol());
            $pk->windowId   = $packet->windowid;
            $pk->windowType = 0;
            $pk->server     = false;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegCraftEvent) {
            $pk = new CraftingEventPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->windowId = $packet->windowId;
            $pk->type     = $packet->type;
            $pk->id       = \pocketmine\utils\UUID::fromBinary($packet->id->toBinary());
            $pk->input    = $packet->input;
            $pk->output   = $packet->output;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegPlayerInput) {
            $pk = new PlayerInputPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->motionX  = $packet->motX;
            $pk->motionY  = $packet->motY;
            $pk->jumping  = $packet->jumping;
            $pk->sneaking = $packet->sneaking;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegEntityEvent) {
            $pk = new ActorEventPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->entityRuntimeId = $packet->eid;
            $pk->event = $packet->event;
            $pk->data  = 0;
            $pk->wasDecoded = true; 
            return $pk;
        }
        if($packet instanceof LegAdventure) {
            $pk = new AdventureSettingsPacket();
            $pk->setProtocol($player->getProtocol());
            $pk->flags             = $packet->flags ?? 0;
            $pk->commandPermission = $packet->userPermission ?? 0;
            $pk->playerPermission  = $packet->globalPermission ?? 0;
            $pk->flags2            = -1;
            $pk->customFlags       = 0;
            $pk->entityUniqueId    = $player->getId();
            $pk->wasDecoded = true; 
            return $pk;
        }
        return null;
    }}