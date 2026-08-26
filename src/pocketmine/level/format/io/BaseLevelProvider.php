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
namespace pocketmine\level\format\io;
use InvalidStateException;use pocketmine\level\format\Chunk;use pocketmine\level\format\SubChunk;use pocketmine\level\format\io\exception\CorruptedChunkException;use pocketmine\level\format\io\exception\UnsupportedChunkFormatException;use pocketmine\world\format\PalettedBlockArray;use pocketmine\level\LevelException;use pocketmine\math\Vector3;use pocketmine\nbt\BigEndianNBTStream;use pocketmine\nbt\tag\ByteArrayTag;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\StringTag;use pocketmine\utils\BinaryStream;use function file_exists;use function file_get_contents;use function file_put_contents;use function mkdir;use function strlen;use function pack;use function unpack;use function array_values;use function count;
abstract class BaseLevelProvider implements LevelProvider{
	protected $path;
	protected $levelData;
	public function __construct(string $path){
		$this->path = $path;
		if(!file_exists($this->path)){
			mkdir($this->path, 0777, true);
		}
		$this->loadLevelData();
		$this->fixLevelData();
	}
	protected function loadLevelData() : void{
		$nbt = new BigEndianNBTStream();
		$levelData = $nbt->readCompressed(file_get_contents($this->getPath() . "level.dat"));
		if(!($levelData instanceof CompoundTag) or !$levelData->hasTag("Data", CompoundTag::class)){
			throw new LevelException("Invalid level.dat");
		}
		$this->levelData = $levelData->getCompoundTag("Data");
	}
	protected function fixLevelData() : void{
		if(!$this->levelData->hasTag("generatorName", StringTag::class)){
			$this->levelData->setString("generatorName", "default", true);
		}elseif(($generatorName = self::hackyFixForGeneratorClasspathInLevelDat($this->levelData->getString("generatorName"))) !== null){
			$this->levelData->setString("generatorName", $generatorName);
		}
		if(!$this->levelData->hasTag("generatorOptions", StringTag::class)){
			$this->levelData->setString("generatorOptions", "");
		}
	}
	protected static function hackyFixForGeneratorClasspathInLevelDat(string $className) : ?string{
		switch($className){
			case 'pocketmine\level\generator\normal\Normal':
				return "normal";
			case 'pocketmine\level\generator\Flat':
				return "flat";
		}
		return null;
	}
	public function getPath() : string{
		return $this->path;
	}
	public function getName() : string{
		return $this->levelData->getString("LevelName");
	}
	public function getTime() : int{
		return $this->levelData->getLong("Time", 0, true);
	}
	public function setTime(int $value){
		$this->levelData->setLong("Time", $value, true); 
	}
	public function getSeed() : int{
		return $this->levelData->getTag("RandomSeed")->getValue();
	}
	public function setSeed(int $value){
		$this->levelData->setLong("RandomSeed", $value);
	}
	public function getSpawn() : Vector3{
		return new Vector3($this->levelData->getInt("SpawnX"), $this->levelData->getInt("SpawnY"), $this->levelData->getInt("SpawnZ"));
	}
	public function setSpawn(Vector3 $pos){
		$this->levelData->setInt("SpawnX", $pos->getFloorX());
		$this->levelData->setInt("SpawnY", $pos->getFloorY());
		$this->levelData->setInt("SpawnZ", $pos->getFloorZ());
	}
	public function doGarbageCollection(){
	}
	public function getLevelData() : CompoundTag{
		return $this->levelData;
	}
	public function saveLevelData(){
		$nbt = new BigEndianNBTStream();
		$buffer = $nbt->writeCompressed(new CompoundTag("", [
			$this->levelData
		]));
		file_put_contents($this->getPath() . "level.dat", $buffer);
	}
	public function loadChunk(int $chunkX, int $chunkZ) : ?Chunk{
		return $this->readChunk($chunkX, $chunkZ);
	}
	public function saveChunk(Chunk $chunk) : void{
		if(!$chunk->isGenerated()){
			throw new InvalidStateException("Cannot save un-generated chunk");
		}
		$this->writeChunk($chunk);
	}
	abstract protected function readChunk(int $chunkX, int $chunkZ) : ?Chunk;
	abstract protected function writeChunk(Chunk $chunk) : void;
    protected static function readFixedSizeByteArray(CompoundTag $chunk, string $tagName, int $length) : string{
        $tag = $chunk->getTag($tagName);
        if(!($tag instanceof ByteArrayTag)){
            if($tag === null){
                throw new CorruptedChunkException("'$tagName' key is missing from chunk NBT");
            }
            throw new CorruptedChunkException("Expected TAG_ByteArray for '$tagName'");
        }
        $data = $tag->getValue();
        if(strlen($data) !== $length){
            throw new CorruptedChunkException("Expected '$tagName' payload to have exactly $length bytes, but have " . strlen($data));
        }
        return $data;
    }
	public function serializeBlockLayers(SubChunk $subChunk) : string{
        $stream = new BinaryStream();
        $stream->putInt($subChunk->getEmptyBlockId());
        $layers = $subChunk->getBlockLayers();
        $stream->putByte(count($layers));
        foreach($layers as $blocks){
            $wordArray = $blocks->getWordArray();
            $palette = $blocks->getPalette();
            $stream->putByte($blocks->getBitsPerBlock());
            $stream->put($wordArray);
            $serialPalette = pack("L*", ...$palette);
            $stream->putInt(strlen($serialPalette));
            $stream->put($serialPalette);
        }
		return $stream->getBuffer();
	}
	public static function deserializeBlockLayers(BinaryStream $stream) : array{
        $airBlockId = $stream->getInt();
        $layers = [];
        for($i = 0, $layerCount = $stream->getByte(); $i < $layerCount; ++$i){
            $bitsPerBlock = $stream->getByte();
            $words = $stream->get(PalettedBlockArray::getExpectedWordArraySize($bitsPerBlock));
            $unpackedPalette = unpack("L*", $stream->get($stream->getInt())); 
            $palette = array_values($unpackedPalette);
            $layers[] = PalettedBlockArray::fromData($bitsPerBlock, $words, $palette);
        }
		return [$airBlockId, $layers];
	}}