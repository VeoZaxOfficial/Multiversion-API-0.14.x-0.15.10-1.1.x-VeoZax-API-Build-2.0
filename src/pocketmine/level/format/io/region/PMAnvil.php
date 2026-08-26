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
namespace pocketmine\level\format\io\region;
use pocketmine\block\Block;use pocketmine\utils\BinaryStream;use pocketmine\world\format\io\SubChunkConverter;use pocketmine\level\format\SubChunk;use pocketmine\nbt\tag\ByteArrayTag;use pocketmine\nbt\tag\CompoundTag;
class PMAnvil extends Anvil{
	public const REGION_FILE_EXTENSION = "mcapm";
	protected function serializeSubChunk(SubChunk $subChunk) : CompoundTag{
		return new CompoundTag("", [
			new ByteArrayTag("Blocks",     self::serializeBlockLayers($subChunk))
		]);
	}
	protected function deserializeSubChunk(CompoundTag $subChunk) : SubChunk{
		if($subChunk->hasTag("Data")){
			$blockLayers = [SubChunkConverter::convertSubChunkXZY(
			    self::readFixedSizeByteArray($subChunk, "Blocks", 4096),
                            self::readFixedSizeByteArray($subChunk, "Data", 2048)
			)];
			$emptyBlockId = Block::AIR << Block::INTERNAL_METADATA_BITS;
		}else{
			$stream = new BinaryStream($subChunk->getByteArray("Blocks"));
			[$emptyBlockId, $blockLayers] = self::deserializeBlockLayers($stream);
		}
		return new SubChunk(
			$emptyBlockId,
			$blockLayers
		);
	}
	public static function getProviderName() : string{
		return "pmanvil";
	}
	public static function getPcWorldFormatVersion() : int{
		return -1; 
	}}