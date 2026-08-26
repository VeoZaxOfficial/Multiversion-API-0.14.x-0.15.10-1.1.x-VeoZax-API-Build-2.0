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
use pocketmine\entity\Skin;use pocketmine\utils\Color;use pocketmine\utils\UUID;
class PlayerListEntry{
	public $uuid;
	public $entityUniqueId;
	public $username;
	public $thirdPartyName = "";
	public $platform = 0;
	public $skin;
	public $xboxUserId = "";
	public $platformChatId = "";
	public $buildPlatform = DeviceOS::UNKNOWN;
	public $isTeacher = false;
	public $isHost = false;
	public $isSubClient = false;
	public $color = null;
	public static function createRemovalEntry(UUID $uuid) : PlayerListEntry{
		$entry = new PlayerListEntry();
		$entry->uuid = $uuid;
		return $entry;
	}
	public static function createAdditionEntry(UUID $uuid, int $entityUniqueId, string $username, Skin $skin, string $xboxUserId = "", string $platformChatId = "", string $thirdPartyName = "", int $platform = 0, int $buildPlatform = -1, bool $isTeacher = false, bool $isHost = false, bool $isSubClient = false, ?Color $color = null) : PlayerListEntry{
		$entry = new PlayerListEntry();
		$entry->uuid = $uuid;
		$entry->entityUniqueId = $entityUniqueId;
		$entry->username = $username;
		$entry->thirdPartyName = $thirdPartyName;
		$entry->platform = $platform;
		$entry->skin = $skin;
		$entry->xboxUserId = $xboxUserId;
		$entry->platformChatId = $platformChatId;
		$entry->buildPlatform = $buildPlatform;
		$entry->isTeacher = $isTeacher;
		$entry->isHost = $isHost;
		$entry->isSubClient = $isSubClient;
		$entry->color = $color;
		return $entry;
	}}