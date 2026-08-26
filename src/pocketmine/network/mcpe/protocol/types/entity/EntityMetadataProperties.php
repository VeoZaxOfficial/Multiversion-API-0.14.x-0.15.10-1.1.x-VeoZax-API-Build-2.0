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
namespace pocketmine\network\mcpe\protocol\types\entity;
interface EntityMetadataProperties{
    public const DATA_FLAGS = 0;
    public const DATA_HEALTH = 1; 
    public const DATA_VARIANT = 2; 
    public const DATA_COLOR = 3, DATA_COLOUR = 3; 
    public const DATA_NAMETAG = 4; 
    public const DATA_OWNER_EID = 5; 
    public const DATA_TARGET_EID = 6; 
    public const DATA_AIR = 7; 
    public const DATA_POTION_COLOR = 8; 
    public const DATA_POTION_AMBIENT = 9; 
    public const DATA_JUMP_DURATION = 10; 
    public const DATA_HURT_TIME = 11; 
    public const DATA_HURT_DIRECTION = 12; 
    public const DATA_PADDLE_TIME_LEFT = 13; 
    public const DATA_PADDLE_TIME_RIGHT = 14; 
    public const DATA_EXPERIENCE_VALUE = 15; 
    public const DATA_MINECART_DISPLAY_BLOCK = 16; 
    public const DATA_HORSE_FLAGS = 16; 
    public const DATA_MINECART_DISPLAY_OFFSET = 17; 
    public const DATA_SHOOTER_ID = 17; 
    public const DATA_MINECART_HAS_DISPLAY = 18; 
    public const DATA_HORSE_TYPE = 19; 
    public const DATA_CHARGE_AMOUNT = 22; 
    public const DATA_ENDERMAN_HELD_ITEM_ID = 23; 
    public const DATA_ENTITY_AGE = 24; 
    public const DATA_PLAYER_FLAGS = 26; 
    public const DATA_PLAYER_INDEX = 27; 
    public const DATA_PLAYER_BED_POSITION = 28; 
    public const DATA_FIREBALL_POWER_X = 29; 
    public const DATA_FIREBALL_POWER_Y = 30; 
    public const DATA_FIREBALL_POWER_Z = 31; 
    public const DATA_FISH_X = 33; 
    public const DATA_FISH_Z = 34; 
    public const DATA_FISH_ANGLE = 35; 
    public const DATA_POTION_AUX_VALUE = 36; 
    public const DATA_LEAD_HOLDER_EID = 37; 
    public const DATA_SCALE = 38; 
    public const DATA_HAS_NPC_COMPONENT = 39; 
    public const DATA_NPC_SKIN_INDEX = 40; 
    public const DATA_NPC_ACTIONS = 41; 
    public const DATA_MAX_AIR = 42; 
    public const DATA_MARK_VARIANT = 43; 
    public const DATA_CONTAINER_TYPE = 44; 
    public const DATA_CONTAINER_BASE_SIZE = 45; 
    public const DATA_CONTAINER_EXTRA_SLOTS_PER_STRENGTH = 46; 
    public const DATA_BLOCK_TARGET = 47; 
    public const DATA_WITHER_INVULNERABLE_TICKS = 48; 
    public const DATA_WITHER_TARGET_1 = 49; 
    public const DATA_WITHER_TARGET_2 = 50; 
    public const DATA_WITHER_TARGET_3 = 51; 
    public const DATA_AERIAL_ATTACK = 52; 
    public const DATA_BOUNDING_BOX_WIDTH = 53; 
    public const DATA_BOUNDING_BOX_HEIGHT = 54; 
    public const DATA_FUSE_LENGTH = 55; 
    public const DATA_RIDER_SEAT_POSITION = 56; 
    public const DATA_RIDER_ROTATION_LOCKED = 57; 
    public const DATA_RIDER_MAX_ROTATION = 58; 
    public const DATA_RIDER_MIN_ROTATION = 59; 
    public const DATA_AREA_EFFECT_CLOUD_RADIUS = 60; 
    public const DATA_AREA_EFFECT_CLOUD_WAITING = 61; 
    public const DATA_AREA_EFFECT_CLOUD_PARTICLE_ID = 62; 
    public const DATA_SHULKER_ATTACH_FACE = 64; 
    public const DATA_SHULKER_ATTACH_POS = 66; 
    public const DATA_TRADING_PLAYER_EID = 67; 
    public const DATA_HAS_COMMAND_BLOCK = 69; 
    public const DATA_COMMAND_BLOCK_COMMAND = 70; 
    public const DATA_COMMAND_BLOCK_LAST_OUTPUT = 71; 
    public const DATA_COMMAND_BLOCK_TRACK_OUTPUT = 72; 
    public const DATA_CONTROLLING_RIDER_SEAT_NUMBER = 73; 
    public const DATA_STRENGTH = 74; 
    public const DATA_MAX_STRENGTH = 75; 
    public const DATA_SPELL_CASTING_COLOR = 76; 
    public const DATA_LIMITED_LIFE = 77; 
    public const DATA_ARMOR_STAND_POSE_INDEX = 78; 
    public const DATA_ENDER_CRYSTAL_TIME_OFFSET = 79; 
    public const DATA_ALWAYS_SHOW_NAMETAG = 80; 
    public const DATA_COLOR_2 = 81; 
    public const DATA_SCORE_TAG = 83; 
    public const DATA_BALLOON_ATTACHED_ENTITY = 84; 
    public const DATA_PUFFERFISH_SIZE = 85; 
    public const DATA_BOAT_BUBBLE_TIME = 86; 
    public const DATA_PLAYER_AGENT_EID = 87; 
    public const DATA_EAT_COUNTER = 90; 
    public const DATA_FLAGS2 = 91; 
    public const DATA_AREA_EFFECT_CLOUD_DURATION = 94; 
    public const DATA_AREA_EFFECT_CLOUD_SPAWN_TIME = 95; 
    public const DATA_AREA_EFFECT_CLOUD_RADIUS_PER_TICK = 96; 
    public const DATA_AREA_EFFECT_CLOUD_RADIUS_CHANGE_ON_PICKUP = 97; 
    public const DATA_AREA_EFFECT_CLOUD_PICKUP_COUNT = 98; 
    public const DATA_INTERACTIVE_TAG = 99; 
    public const DATA_TRADE_TIER = 100; 
    public const DATA_MAX_TRADE_TIER = 101; 
    public const DATA_TRADE_XP = 102; 
    public const DATA_SKIN_ID = 103; 
    public const DATA_SPAWNING_FRAMES = 104; 
    public const DATA_COMMAND_BLOCK_TICK_DELAY = 105; 
    public const DATA_COMMAND_BLOCK_EXECUTE_ON_FIRST_TICK = 106; 
    public const DATA_AMBIENT_SOUND_INTERVAL_MIN = 107; 
    public const DATA_AMBIENT_SOUND_INTERVAL_RANGE = 108; 
    public const DATA_AMBIENT_SOUND_EVENT = 109; 
    public const DATA_FALL_DAMAGE_MULTIPLIER = 110; 
    public const DATA_NAME_RAW_TEXT = 111; 
    public const DATA_CAN_RIDE_TARGET = 112; 
	public const DATA_IS_BUOYANT = 119; 
	public const DATA_BUOYANCY = 121; 
    public const DATA_SWELL = 197; 
    public const DATA_OLD_SWELL = 198; 
    public const DATA_SWELL_DIRECTION = 199; 
    public const DATA_RIDER_SEAT_ROTATION_OFFSET = 200; 
    public const DATA_NO_AI = 201; 
	public const DATA_SHOW_NAMETAG = 202; 
    public const DATA_SILENT = 203; }