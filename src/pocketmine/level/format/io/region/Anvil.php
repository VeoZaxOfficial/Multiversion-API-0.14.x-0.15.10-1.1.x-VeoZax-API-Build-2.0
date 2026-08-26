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
use pocketmine\block\Block;use pocketmine\level\format\Chunk;use pocketmine\level\format\io\ChunkUtils;use pocketmine\level\format\io\exception\CorruptedChunkException;use pocketmine\level\format\SubChunk;use pocketmine\nbt\BigEndianNBTStream;use pocketmine\nbt\NBT;use pocketmine\nbt\tag\ByteArrayTag;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\IntArrayTag;use pocketmine\nbt\tag\ListTag;use pocketmine\world\format\io\SubChunkConverter;use pocketmine\utils\BinaryStream;
class Anvil extends McRegion{
	public const REGION_FILE_EXTENSION = "mca";
	protected function nbtSerialize(Chunk $chunk) : string{
		$nbt = new CompoundTag("Level", []);
		$nbt->setInt("xPos", $chunk->getX());
		$nbt->setInt("zPos", $chunk->getZ());
		$nbt->setByte("V", 1);
		$nbt->setLong("LastUpdate", 0); 
		$nbt->setLong("InhabitedTime", 0); 
		$nbt->setByte("TerrainPopulated", $chunk->isPopulated() ? 1 : 0);
		$nbt->setByte("LightPopulated", $chunk->isLightPopulated() ? 1 : 0);
		$subChunks = [];
		foreach($chunk->getSubChunks() as $y => $subChunk){
			if($subChunk->isEmpty()){
				continue;
			}
			$tag = $this->serializeSubChunk($subChunk);
			$tag->setByte("Y", $y);
			$subChunks[] = $tag;
		}
		$nbt->setTag(new ListTag("Sections", $subChunks, NBT::TAG_Compound));
		$nbt->setByteArray("Biomes", $chunk->getBiomeIdArray());
		$nbt->setIntArray("HeightMap", $chunk->getHeightMapArray());
		$entities = [];
		foreach($chunk->getSavableEntities() as $entity){
			$entity->saveNBT();
			$entities[] = $entity->namedtag;
		}
		$nbt->setTag(new ListTag("Entities", $entities, NBT::TAG_Compound));
		$tiles = [];
		foreach($chunk->getTiles() as $tile){
			$tiles[] = $tile->saveNBT();
		}
		$nbt->setTag(new ListTag("TileEntities", $tiles, NBT::TAG_Compound));
		$writer = new BigEndianNBTStream();
		return $writer->writeCompressed(new CompoundTag("", [$nbt]), ZLIB_ENCODING_DEFLATE, RegionLoader::$COMPRESSION_LEVEL);
	}
	protected function serializeSubChunk(SubChunk $subChunk) : CompoundTag{
		return new CompoundTag("", [
			new ByteArrayTag("Blocks", self::serializeBlockLayers($subChunk))
		]);
	}
	protected function nbtDeserialize(string $data) : Chunk{
		$nbt = new BigEndianNBTStream();
		$chunk = $nbt->readCompressed($data);
		if(!($chunk instanceof CompoundTag) or !$chunk->hasTag("Level")){
			throw new CorruptedChunkException("'Level' key is missing from chunk NBT");
		}
		$chunk = $chunk->getCompoundTag("Level");
		$subChunks = [];
		$subChunksTag = $chunk->getListTag("Sections") ?? [];
		foreach($subChunksTag as $subChunk){
			if($subChunk instanceof CompoundTag){
				$subChunks[$subChunk->getByte("Y")] = $this->deserializeSubChunk($subChunk);
			}
		}
		if($chunk->hasTag("BiomeColors", IntArrayTag::class)){
			$biomeIds = ChunkUtils::convertBiomeColors($chunk->getIntArray("BiomeColors")); 
		}else{
			$biomeIds = $chunk->getByteArray("Biomes", "", true);
		}
		$result = new Chunk(
			$chunk->getInt("xPos"),
			$chunk->getInt("zPos"),
			$subChunks,
			$chunk->hasTag("Entities", ListTag::class) ? $chunk->getListTag("Entities")->getValue() : [],
			$chunk->hasTag("TileEntities", ListTag::class) ? $chunk->getListTag("TileEntities")->getValue() : [],
			$biomeIds,
			$chunk->getIntArray("HeightMap", [])
		);
		$result->setLightPopulated($chunk->getByte("LightPopulated", 0) !== 0);
		$result->setPopulated($chunk->getByte("TerrainPopulated", 0) !== 0);
		$result->setGenerated();
		return $result;
	}
	protected function deserializeSubChunk(CompoundTag $subChunk) : SubChunk{
		if($subChunk->hasTag("Data")){
			$emptyBlockId = Block::AIR << Block::INTERNAL_METADATA_BITS;
			$blockLayers = [SubChunkConverter::convertSubChunkYZX(
				self::readFixedSizeByteArray($subChunk, "Blocks", 4096),
				self::readFixedSizeByteArray($subChunk, "Data", 2048)
			)];
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
		return "anvil";
	}
	public static function getPcWorldFormatVersion() : int{
		return 19133; 
	}
	public function getWorldHeight() : int{
		return 256;
	}}