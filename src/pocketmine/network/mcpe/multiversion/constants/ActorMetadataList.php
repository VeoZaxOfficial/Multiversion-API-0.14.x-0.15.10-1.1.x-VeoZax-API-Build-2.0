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
namespace pocketmine\network\mcpe\multiversion\constants;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
abstract class ActorMetadataList{
	public const METADATA = [
				ProtocolInfo::PROTOCOL_110 => [
			"DATA_SWELL" => 19,
			"DATA_OLD_SWELL" => 20,
            "DATA_SWELL_DIRECTION" => 21,
		    "DATA_ENDERMAN_HELD_ITEM_ID" => 23,
		    "DATA_ENDERMAN_HELD_ITEM_DAMAGE" => 24,
		    "DATA_ENTITY_AGE" => 25,
            "DATA_PLAYER_FLAGS" => 27,
            "DATA_PLAYER_INDEX" => 28,
            "DATA_PLAYER_BED_POSITION" => 29,
		    "DATA_FIREBALL_POWER_X" => 30,
		    "DATA_FIREBALL_POWER_Y" => 31,
		    "DATA_FIREBALL_POWER_Z" => 32,
		    "DATA_POTION_AUX_VALUE" => 37,
		    "DATA_LEAD_HOLDER_EID" => 38,
			"DATA_SCALE" => 39,
			"DATA_INTERACTIVE_TAG" => 40,
			"DATA_NPC_SKIN_INDEX" => 41,
			"DATA_NPC_ACTIONS" => 42,
			"DATA_MAX_AIR" => 43,
			"DATA_MARK_VARIANT" => 44,
			"DATA_BLOCK_TARGET" => 48,
			"DATA_WITHER_INVULNERABLE_TICKS" => 49,
			"DATA_WITHER_TARGET_1" => 50,
			"DATA_WITHER_TARGET_2" => 51,
			"DATA_WITHER_TARGET_3" => 52,
			"DATA_BOUNDING_BOX_WIDTH" => 54,
			"DATA_BOUNDING_BOX_HEIGHT" => 55,
			"DATA_FUSE_LENGTH" => 56,
			"DATA_RIDER_SEAT_POSITION" => 57,
			"DATA_RIDER_ROTATION_LOCKED" => 58,
			"DATA_RIDER_MAX_ROTATION" => 59,
			"DATA_RIDER_MIN_ROTATION" => 60,
			"DATA_AREA_EFFECT_CLOUD_RADIUS" => 61,
			"DATA_AREA_EFFECT_CLOUD_WAITING" => 62,
			"DATA_AREA_EFFECT_CLOUD_PARTICLE_ID" => 63,
			"DATA_SHULKER_ATTACH_FACE" => 65,
			"DATA_SHULKER_ATTACH_POS" => 67,
			"DATA_TRADING_PLAYER_EID" => 68,
			"DATA_COMMAND_BLOCK_COMMAND" => 71,
			"DATA_COMMAND_BLOCK_LAST_OUTPUT" => 72,
			"DATA_COMMAND_BLOCK_TRACK_OUTPUT" => 73,
			"DATA_CONTROLLING_RIDER_SEAT_NUMBER" => 74,
			"DATA_STRENGTH" => 75,
			"DATA_MAX_STRENGTH" => 76
		],
			ProtocolInfo::PROTOCOL_90 => [
			"DATA_FLAGS" => 0,
			"DATA_HEALTH" => 1,
			"DATA_VARIANT" => 2,
			"DATA_COLOR" => 3,
			"DATA_COLOUR" => 3,
			"DATA_NAMETAG" => 4,
			"DATA_OWNER_EID" => 5,
			"DATA_TARGET_EID" => 6,
			"DATA_AIR" => 7,
			"DATA_POTION_COLOR" => 8,
			"DATA_POTION_AMBIENT" => 9,
			"DATA_HURT_TIME" => 11,
			"DATA_HURT_DIRECTION" => 12,
			"DATA_PADDLE_TIME_LEFT" => 13,
			"DATA_PADDLE_TIME_RIGHT" => 14,
			"DATA_EXPERIENCE_VALUE" => 15,
			"DATA_MINECART_DISPLAY_BLOCK" => 16,
			"DATA_HORSE_FLAGS" => 16,
			"DATA_MINECART_DISPLAY_OFFSET" => 17,
			"DATA_SHOOTER_ID" => 17,
			"DATA_MINECART_HAS_DISPLAY" => 18,
			"DATA_SWELL" => 19,
			"DATA_OLD_SWELL" => 20,
			"DATA_SWELL_DIRECTION" => 21,
			"DATA_CHARGE_AMOUNT" => 22,
			"DATA_ENDERMAN_HELD_ITEM_ID" => 23,
			"DATA_ENDERMAN_HELD_ITEM_DAMAGE" => 24,
			"DATA_ENTITY_AGE" => 25,
			"DATA_PLAYER_FLAGS" => 27,
			"DATA_PLAYER_INDEX" => 28,
			"DATA_PLAYER_BED_POSITION" => 29,
			"DATA_FIREBALL_POWER_X" => 30,
			"DATA_FIREBALL_POWER_Y" => 31,
			"DATA_FIREBALL_POWER_Z" => 32,
			"DATA_POTION_AUX_VALUE" => 37,
			"DATA_LEAD_HOLDER_EID" => 38,
			"DATA_SCALE" => 39,
			"DATA_INTERACTIVE_TAG" => 40,
			"DATA_NPC_SKIN_INDEX" => 42,
			"DATA_NPC_ACTIONS" => 43,
			"DATA_MAX_AIR" => 44,
			"DATA_MARK_VARIANT" => 45,
			"DATA_BOUNDING_BOX_WIDTH" => 54,
			"DATA_BOUNDING_BOX_HEIGHT" => 55,
			"DATA_FUSE_LENGTH" => 56,
			"DATA_RIDER_SEAT_POSITION" => 57,
			"DATA_RIDER_ROTATION_LOCKED" => 58,
			"DATA_RIDER_MAX_ROTATION" => 59,
			"DATA_RIDER_MIN_ROTATION" => 60
		],
	    ProtocolInfo::PROTOCOL_41 => [
	        "DATA_FLAGS" => 0,
	        "DATA_AIR" => 1,
	        "DATA_NAMETAG" => 2,
	        "DATA_SHOW_NAMETAG" => 3,
	        "DATA_SILENT" => 4,
			"DATA_POTION_COLOR" => 7,
			"DATA_POTION_AMBIENT" => 8,
			"DATA_ENTITY_AGE" => 14,
			"DATA_NO_AI" => 15,
			"DATA_PLAYER_FLAGS" => 16,
	        "DATA_PLAYER_BED_POSITION" => 17,
			"DATA_VARIANT" => 18,
			"DATA_SWELL" => 19,
			"DATA_BOAT_COLOR" => 20,
			"DATA_IN_LOVE" => 21,
			"DATA_LEAD_HOLDER_EID" => 23,
			"DATA_LEAD" => 24
	    ],
											];
	public const FLAGS = [
				ProtocolInfo::PROTOCOL_110 => [
			"DATA_FLAG_CAN_CLIMB" => 19,
			"DATA_FLAG_SWIMMER" => 20,
			"DATA_FLAG_CAN_FLY" => 21,
			"DATA_FLAG_RESTING" => 22,
			"DATA_FLAG_SITTING" => 23,
			"DATA_FLAG_ANGRY" => 24,
			"DATA_FLAG_INTERESTED" => 25,
			"DATA_FLAG_CHARGED" => 26,
			"DATA_FLAG_TAMED" => 27,
			"DATA_FLAG_LEASHED" => 28,
			"DATA_FLAG_SHEARED" => 29,
			"DATA_FLAG_GLIDING" => 30,
			"DATA_FLAG_ELDER" => 31,
			"DATA_FLAG_MOVING" => 32,
			"DATA_FLAG_BREATHING" => 33,
			"DATA_FLAG_CHESTED" => 34,
			"DATA_FLAG_STACKABLE" => 35,
			"DATA_FLAG_SHOWBASE" => 36,
			"DATA_FLAG_REARING" => 37,
			"DATA_FLAG_VIBRATING" => 38,
			"DATA_FLAG_IDLING" => 39,
			"DATA_FLAG_EVOKER_SPELL" => 40,
			"DATA_FLAG_CHARGE_ATTACK" => 41,
			"DATA_FLAG_WASD_CONTROLLED" => 43,
			"DATA_FLAG_CAN_POWER_JUMP" => 44,
			"DATA_FLAG_LINGER" => 45
		],
			ProtocolInfo::PROTOCOL_90 => [
			"DATA_FLAG_ONFIRE" => 0,
			"DATA_FLAG_SNEAKING" => 1,
			"DATA_FLAG_RIDING" => 2,
			"DATA_FLAG_SPRINTING" => 3,
			"DATA_FLAG_ACTION" => 4,
			"DATA_FLAG_INVISIBLE" => 5,
			"DATA_FLAG_TEMPTED" => 6,
			"DATA_FLAG_INLOVE" => 7,
			"DATA_FLAG_SADDLED" => 8,
			"DATA_FLAG_POWERED" => 9,
			"DATA_FLAG_IGNITED" => 10,
			"DATA_FLAG_BABY" => 11,
			"DATA_FLAG_CONVERTING" => 12,
			"DATA_FLAG_CRITICAL" => 13,
			"DATA_FLAG_CAN_SHOW_NAMETAG" => 14,
			"DATA_FLAG_ALWAYS_SHOW_NAMETAG" => 15,
			"DATA_FLAG_IMMOBILE" => 16,
			"DATA_FLAG_NO_AI" => 16,
			"DATA_FLAG_SILENT" => 17,
			"DATA_FLAG_WALLCLIMBING" => 18,
			"DATA_FLAG_RESTING" => 19,
			"DATA_FLAG_SITTING" => 20,
			"DATA_FLAG_ANGRY" => 21,
			"DATA_FLAG_INTERESTED" => 22,
			"DATA_FLAG_CHARGED" => 23,
			"DATA_FLAG_TAMED" => 24,
			"DATA_FLAG_LEASHED" => 25,
			"DATA_FLAG_SHEARED" => 26,
			"DATA_FLAG_GLIDING" => 27,
			"DATA_FLAG_ELDER" => 28,
			"DATA_FLAG_MOVING" => 29,
			"DATA_FLAG_BREATHING" => 30,
			"DATA_FLAG_CHESTED" => 31,
			"DATA_FLAG_STACKABLE" => 32,
			"DATA_FLAG_SHOWBASE" => 33,
			"DATA_FLAG_REARING" => 34,
			"DATA_FLAG_VIBRATING" => 35,
			"DATA_FLAG_IDLING" => 36
		],
	    ProtocolInfo::PROTOCOL_41 => [
			"DATA_FLAG_ONFIRE" => 0,
			"DATA_FLAG_SNEAKING" => 1,
			"DATA_FLAG_RIDING" => 2,
			"DATA_FLAG_SPRINTING" => 3,
			"DATA_FLAG_ACTION" => 4,
			"DATA_FLAG_INVISIBLE" => 5
	    ],
											];}