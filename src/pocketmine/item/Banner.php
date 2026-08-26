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
namespace pocketmine\item;
use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\StringTag;use pocketmine\tile\Banner as TileBanner;use pocketmine\network\mcpe\protocol\ProtocolInfo;use function assert;
class Banner extends Item{
	public const TAG_BASE = TileBanner::TAG_BASE;
	public const TAG_PATTERNS = TileBanner::TAG_PATTERNS;
	public const TAG_PATTERN_COLOR = TileBanner::TAG_PATTERN_COLOR;
	public const TAG_PATTERN_NAME = TileBanner::TAG_PATTERN_NAME;
	public function __construct(int $meta = 0){
		parent::__construct(self::BANNER, $meta, "Banner");
	}
	public function getBlock() : Block{
		return BlockFactory::get(Block::STANDING_BANNER);
	}
	public function getMaxStackSize() : int{
		return 16;
	}
	public function getBaseColor() : int{
		return $this->getNamedTag()->getInt(self::TAG_BASE, 0);
	}
	public function setBaseColor(int $color) : void{
		$namedTag = $this->getNamedTag();
		$namedTag->setInt(self::TAG_BASE, $color & 0x0f);
		$this->setNamedTag($namedTag);
	}
	public function addPattern(string $pattern, int $color) : int{
		$patternsTag = $this->getNamedTag()->getListTag(self::TAG_PATTERNS);
		assert($patternsTag !== null);
		$patternsTag->push(new CompoundTag("", [
			new IntTag(self::TAG_PATTERN_COLOR, $color & 0x0f),
			new StringTag(self::TAG_PATTERN_NAME, $pattern)
		]));
		$this->setNamedTagEntry($patternsTag);
		return $patternsTag->count() - 1;
	}
	public function patternExists(int $patternId) : bool{
		$this->correctNBT();
		return $this->getNamedTag()->getListTag(self::TAG_PATTERNS)->isset($patternId);
	}
	public function getPatternData(int $patternId) : array{
		if(!$this->patternExists($patternId)){
			return [];
		}
		$patternsTag = $this->getNamedTag()->getListTag(self::TAG_PATTERNS);
		assert($patternsTag !== null);
		$pattern = $patternsTag->get($patternId);
		assert($pattern instanceof CompoundTag);
		return [
			self::TAG_PATTERN_COLOR => $pattern->getInt(self::TAG_PATTERN_COLOR),
			self::TAG_PATTERN_NAME => $pattern->getString(self::TAG_PATTERN_NAME)
		];
	}
	public function changePattern(int $patternId, string $pattern, int $color) : bool{
		if(!$this->patternExists($patternId)){
			return false;
		}
		$patternsTag = $this->getNamedTag()->getListTag(self::TAG_PATTERNS);
		assert($patternsTag !== null);
		$patternsTag->set($patternId, new CompoundTag("", [
			new IntTag(self::TAG_PATTERN_COLOR, $color & 0x0f),
			new StringTag(self::TAG_PATTERN_NAME, $pattern)
		]));
		$this->setNamedTagEntry($patternsTag);
		return true;
	}
	public function deletePattern(int $patternId) : bool{
		if(!$this->patternExists($patternId)){
			return false;
		}
		$patternsTag = $this->getNamedTag()->getListTag(self::TAG_PATTERNS);
		if($patternsTag instanceof ListTag){
			$patternsTag->remove($patternId);
			$this->setNamedTagEntry($patternsTag);
		}
		return true;
	}
	public function deleteTopPattern() : bool{
		return $this->deletePattern($this->getPatternCount() - 1);
	}
	public function deleteBottomPattern() : bool{
		return $this->deletePattern(0);
	}
	public function getPatternCount() : int{
		return $this->getNamedTag()->getListTag(self::TAG_PATTERNS)->count();
	}
	public function correctNBT() : void{
		$tag = $this->getNamedTag();
		if(!$tag->hasTag(self::TAG_BASE, IntTag::class)){
			$tag->setInt(self::TAG_BASE, 0);
		}
		if(!$tag->hasTag(self::TAG_PATTERNS, ListTag::class)){
			$tag->setTag(new ListTag(self::TAG_PATTERNS));
		}
		$this->setNamedTag($tag);
	}
    public function getItemProtocol(int $protocol) : ?Item{
        
            return ItemFactory::get(ItemIds::INFO_UPDATE, 0);
        
        return parent::getItemProtocol($protocol);
    }
	public function getFuelTime() : int{
		return 300;
	}}