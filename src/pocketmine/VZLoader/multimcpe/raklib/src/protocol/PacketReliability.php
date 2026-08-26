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
namespace raklib\protocol;
abstract class PacketReliability{
	public const UNRELIABLE = 0;
	public const UNRELIABLE_SEQUENCED = 1;
	public const RELIABLE = 2;
	public const RELIABLE_ORDERED = 3;
	public const RELIABLE_SEQUENCED = 4;
	public const UNRELIABLE_WITH_ACK_RECEIPT = 5;
	public const RELIABLE_WITH_ACK_RECEIPT = 6;
	public const RELIABLE_ORDERED_WITH_ACK_RECEIPT = 7;
	public static function isReliable(int $reliability) : bool{
		return (
			$reliability === self::RELIABLE or
			$reliability === self::RELIABLE_ORDERED or
			$reliability === self::RELIABLE_SEQUENCED or
			$reliability === self::RELIABLE_WITH_ACK_RECEIPT or
			$reliability === self::RELIABLE_ORDERED_WITH_ACK_RECEIPT
		);
	}
	public static function isSequenced(int $reliability) : bool{
		return (
			$reliability === self::UNRELIABLE_SEQUENCED or
			$reliability === self::RELIABLE_SEQUENCED
		);
	}
	public static function isOrdered(int $reliability) : bool{
		return (
			$reliability === self::RELIABLE_ORDERED or
			$reliability === self::RELIABLE_ORDERED_WITH_ACK_RECEIPT
		);
	}
	public static function isSequencedOrOrdered(int $reliability) : bool{
		return (
			$reliability === self::UNRELIABLE_SEQUENCED or
			$reliability === self::RELIABLE_ORDERED or
			$reliability === self::RELIABLE_SEQUENCED or
			$reliability === self::RELIABLE_ORDERED_WITH_ACK_RECEIPT
		);
	}}