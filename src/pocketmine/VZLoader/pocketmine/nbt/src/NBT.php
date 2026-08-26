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
namespace pocketmine\nbt;
use InvalidArgumentException;use pocketmine\nbt\tag\ByteArrayTag;use pocketmine\nbt\tag\ByteTag;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\DoubleTag;use pocketmine\nbt\tag\FloatTag;use pocketmine\nbt\tag\IntArrayTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\LongTag;use pocketmine\nbt\tag\NamedTag;use pocketmine\nbt\tag\ShortTag;use pocketmine\nbt\tag\StringTag;
abstract class NBT{
	public const TAG_End = 0;
	public const TAG_Byte = 1;
	public const TAG_Short = 2;
	public const TAG_Int = 3;
	public const TAG_Long = 4;
	public const TAG_Float = 5;
	public const TAG_Double = 6;
	public const TAG_ByteArray = 7;
	public const TAG_String = 8;
	public const TAG_List = 9;
	public const TAG_Compound = 10;
	public const TAG_IntArray = 11;
	public static function createTag(int $type) : NamedTag{
		switch($type){
			case self::TAG_Byte:
				return new ByteTag();
			case self::TAG_Short:
				return new ShortTag();
			case self::TAG_Int:
				return new IntTag();
			case self::TAG_Long:
				return new LongTag();
			case self::TAG_Float:
				return new FloatTag();
			case self::TAG_Double:
				return new DoubleTag();
			case self::TAG_ByteArray:
				return new ByteArrayTag();
			case self::TAG_String:
				return new StringTag();
			case self::TAG_List:
				return new ListTag();
			case self::TAG_Compound:
				return new CompoundTag();
			case self::TAG_IntArray:
				return new IntArrayTag();
			default:
				throw new InvalidArgumentException("Unknown NBT tag type $type");
		}
	}}