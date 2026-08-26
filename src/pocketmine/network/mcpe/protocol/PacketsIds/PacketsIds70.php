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
namespace pocketmine\network\mcpe\protocol\PacketsIds;
final class PacketsIds70 {
    public const LOGIN_PACKET                    = 0x8f;
    public const PLAY_STATUS_PACKET              = 0x90;
    public const DISCONNECT_PACKET               = 0x91;
    public const BATCH_PACKET                    = 0x92;
    public const TEXT_PACKET                     = 0x93;
    public const SET_TIME_PACKET                 = 0x94;
    public const START_GAME_PACKET               = 0x95;
    public const ADD_PLAYER_PACKET               = 0x96;
    public const ADD_ACTOR_PACKET                = 0x98;
    public const REMOVE_ACTOR_PACKET             = 0x99;
    public const ADD_ITEM_ACTOR_PACKET           = 0x9a;
    public const TAKE_ITEM_ACTOR_PACKET          = 0x9b;
    public const MOVE_ACTOR_ABSOLUTE_PACKET      = 0x9c;
    public const MOVE_PLAYER_PACKET              = 0x9d;
    public const REMOVE_BLOCK_PACKET             = 0x9e;
    public const UPDATE_BLOCK_PACKET             = 0x9f;
    public const ADD_PAINTING_PACKET             = 0xa0;
    public const EXPLODE_PACKET                  = 0xa1;
    public const LEVEL_EVENT_PACKET              = 0xa2;
    public const BLOCK_EVENT_PACKET              = 0xa3;
    public const ACTOR_EVENT_PACKET              = 0xa4;
    public const MOB_EFFECT_PACKET               = 0xa5;
    public const UPDATE_ATTRIBUTES_PACKET        = 0xa6;
    public const MOB_EQUIPMENT_PACKET            = 0xa7;
    public const MOB_ARMOR_EQUIPMENT_PACKET      = 0xa8;
    public const INTERACT_PACKET                 = 0xa9;
    public const USE_ITEM_PACKET                 = 0xaa;
    public const PLAYER_ACTION_PACKET            = 0xab;
    public const HURT_ARMOR_PACKET               = 0xac;
    public const SET_ACTOR_DATA_PACKET           = 0xad;
    public const SET_ACTOR_MOTION_PACKET         = 0xae;
    public const SET_ACTOR_LINK_PACKET           = 0xaf;
    public const SET_HEALTH_PACKET               = 0xb0;
    public const SET_SPAWN_POSITION_PACKET       = 0xb1;
    public const ANIMATE_PACKET                  = 0xb2;
    public const RESPAWN_PACKET                  = 0xb3;
    public const DROP_ITEM_PACKET                = 0xb4;
    public const CONTAINER_OPEN_PACKET           = 0xb5;
    public const CONTAINER_CLOSE_PACKET          = 0xb6;
    public const CONTAINER_SET_SLOT_PACKET       = 0xb7;
    public const CONTAINER_SET_DATA_PACKET       = 0xb8;
    public const CONTAINER_SET_CONTENT_PACKET    = 0xb9;
    public const CRAFTING_DATA_PACKET            = 0xba;
    public const CRAFTING_EVENT_PACKET           = 0xbb;
    public const ADVENTURE_SETTINGS_PACKET       = 0xbc;
    public const BLOCK_ACTOR_DATA_PACKET         = 0xbd;
    public const PLAYER_INPUT_PACKET             = 0xbe;
    public const LEVEL_CHUNK_PACKET              = 0xbf;
    public const SET_DIFFICULTY_PACKET           = 0xc0;
    public const CHANGE_DIMENSION_PACKET         = 0xc1;
    public const SET_PLAYER_GAME_TYPE_PACKET     = 0xc2;
    public const PLAYER_LIST_PACKET              = 0xc3;
    public const SIMPLE_EVENT_PACKET             = 0xc4;
    public const SPAWN_EXPERIENCE_ORB_PACKET     = 0xc5;
    public const CLIENTBOUND_MAP_ITEM_DATA_PACKET = 0xc6;
    public const MAP_INFO_REQUEST_PACKET         = 0xc7;
    public const REQUEST_CHUNK_RADIUS_PACKET     = 0xc8;
    public const CHUNK_RADIUS_UPDATED_PACKET     = 0xc9;
    public const ITEM_FRAME_DROP_ITEM_PACKET     = 0xca;}