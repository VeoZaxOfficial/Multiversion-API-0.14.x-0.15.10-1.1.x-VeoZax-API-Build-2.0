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
final class ParticleIds{
	private function __construct(){
	}
	public const PARTICLE_IDS = [
		ProtocolInfo::PROTOCOL_41 => [
			"TYPE_BUBBLE" => 1,
			"TYPE_CRITICAL" => 2,
			"TYPE_BLOCK_FORCE_FIELD" => 3,
			"TYPE_SMOKE" => 4,
			"TYPE_EXPLODE" => 5,
			"TYPE_EVAPORATION" => 6,
			"TYPE_FLAME" => 7,
			"TYPE_LAVA" => 8,
			"TYPE_LARGE_SMOKE" => 9,
			"TYPE_REDSTONE" => 10,
			"TYPE_RISING_RED_DUST" => 11,
			"TYPE_ITEM_BREAK" => 12,
			"TYPE_SNOWBALL_POOF" => 13,
			"TYPE_HUGE_EXPLODE" => 14,
			"TYPE_HUGE_EXPLODE_SEED" => 15,
			"TYPE_MOB_FLAME" => 16,
			"TYPE_HEART" => 17,
			"TYPE_TERRAIN" => 18,
			"TYPE_SUSPENDED_TOWN" => 19, "TYPE_TOWN_AURA" => 19,
			"TYPE_PORTAL" => 20,
			"TYPE_SPLASH" => 21, "TYPE_WATER_SPLASH" => 21,
			"TYPE_WATER_WAKE" => 22,
			"TYPE_DRIP_WATER" => 23,
			"TYPE_DRIP_LAVA" => 24,
			"TYPE_FALLING_DUST" => 25, "DUST" => 25,
			"TYPE_MOB_SPELL" => 26,
			"TYPE_MOB_SPELL_AMBIENT" => 27,
			"TYPE_MOB_SPELL_INSTANTANEOUS" => 28,
			"TYPE_INK" => 29,
			"TYPE_SLIME" => 30,
			"TYPE_RAIN_SPLASH" => 31,
			"TYPE_VILLAGER_ANGRY" => 32,
			"TYPE_VILLAGER_HAPPY" => 33,
			"TYPE_ENCHANTMENT_TABLE" => 34,
			"TYPE_TRACKING_EMITTER" => 35,
			"TYPE_NOTE" => 36,
			"TYPE_WITCH_SPELL" => 37,
			"TYPE_CARROT" => 38,
			"TYPE_END_ROD" => 40,
			"TYPE_DRAGONS_BREATH" => 41,
			"TYPE_SPIT" => 42,
			"TYPE_TOTEM" => 43,
			"TYPE_FOOD" => 44,
			"TYPE_FIREWORKS_STARTER" => 45,
			"TYPE_FIREWORKS_SPARK" => 46,
			"TYPE_FIREWORKS_OVERLAY" => 47,
			"TYPE_BALLOON_GAS" => 48,
			"TYPE_COLORED_FLAME" => 49,
			"TYPE_SPARKLER" => 50,
			"TYPE_CONDUIT" => 51,
			"TYPE_BUBBLE_COLUMN_UP" => 52,
			"TYPE_BUBBLE_COLUMN_DOWN" => 53,
			"TYPE_SNEEZE" => 54
		]
	];}