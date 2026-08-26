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
namespace pocketmine\tile;
use InvalidArgumentException;use pocketmine\event\block\SignChangeEvent;use pocketmine\nbt\tag\ByteTag;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\StringTag;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use pocketmine\utils\Binary;use pocketmine\utils\TextFormat;use UnexpectedValueException;use function array_map;use function array_pad;use function array_slice;use function explode;use function implode;use function mb_check_encoding;use function mb_scrub;use function sprintf;use function strlen;
class Sign extends Spawnable{
	public const TAG_TEXT_BLOB = "Text";
	public const TAG_TEXT_LINE = "Text%d"; 
	public const TAG_TEXT_COLOR = "SignTextColor";
	public const TAG_GLOWING_TEXT = "IgnoreLighting";
	public const TAG_PERSIST_FORMATTING = "PersistFormatting"; 
	public const TAG_LEGACY_BUG_RESOLVE = "TextIgnoreLegacyBugResolved";
	public const TAG_FRONT_TEXT = "FrontText"; 
	public const TAG_BACK_TEXT = "BackText"; 
	public const TAG_WAXED = "IsWaxed"; 
	public const TAG_LOCKED_FOR_EDITING_BY = "LockedForEditingBy"; 
	private static function fixTextBlob(string $blob) : array{
		return array_slice(array_pad(explode("\n", $blob), 4, ""), 0, 4);
	}
	protected $text = ["", "", "", ""];
	protected $waxed = false;
	protected $editorEntityRuntimeId = null;
	protected function readSaveData(CompoundTag $nbt) : void{
		if($nbt->hasTag(self::TAG_TEXT_BLOB, StringTag::class)){ 
			$this->text = self::fixTextBlob($nbt->getString(self::TAG_TEXT_BLOB));
		}else{
			for($i = 1; $i <= 4; ++$i){
				$textKey = sprintf(self::TAG_TEXT_LINE, $i);
				if($nbt->hasTag($textKey, StringTag::class)){
					$this->text[$i - 1] = $nbt->getString($textKey);
				}
			}
		}
		$this->text = array_map(function(string $line) : string{
			return mb_scrub($line, 'UTF-8');
		}, $this->text);
		if($nbt->hasTag(self::TAG_WAXED, ByteTag::class)){
	    	$this->waxed = $nbt->getByte(self::TAG_WAXED, 0) !== 0;
		}
	}
	protected function writeSaveData(CompoundTag $nbt) : void{
		$nbt->setString(self::TAG_TEXT_BLOB, implode("\n", $this->text));
		for($i = 1; $i <= 4; ++$i){ 
			$textKey = sprintf(self::TAG_TEXT_LINE, $i);
			$nbt->setString($textKey, $this->getLine($i - 1));
		}
	}
	public function setText(?string $line1 = "", ?string $line2 = "", ?string $line3 = "", ?string $line4 = "") : void{
		if($line1 !== null){
			$this->setLine(0, $line1, false);
		}
		if($line2 !== null){
			$this->setLine(1, $line2, false);
		}
		if($line3 !== null){
			$this->setLine(2, $line3, false);
		}
		if($line4 !== null){
			$this->setLine(3, $line4, false);
		}
		$this->onChanged();
	}
	public function setLine(int $index, string $line, bool $update = true) : void{
		if($index < 0 or $index > 3){
			throw new InvalidArgumentException("Index must be in the range 0-3!");
		}
		if(!mb_check_encoding($line, 'UTF-8')){
			throw new InvalidArgumentException("Text must be valid UTF-8");
		}
		$this->text[$index] = $line;
		if($update){
			$this->onChanged();
		}
	}
	public function getLine(int $index) : string{
		if($index < 0 or $index > 3){
			throw new InvalidArgumentException("Index must be in the range 0-3!");
		}
		return $this->text[$index];
	}
	public function getText() : array{
		return $this->text;
	}
	public function getEditorEntityRuntimeId() : ?int{ return $this->editorEntityRuntimeId; }
	public function setEditorEntityRuntimeId(?int $editorEntityRuntimeId) : void{
		$this->editorEntityRuntimeId = $editorEntityRuntimeId;
	}
	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		$nbt->setString(self::TAG_TEXT_BLOB, implode("\n", $this->text));
		for($i = 1; $i <= 4; ++$i){ 
			$textKey = sprintf(self::TAG_TEXT_LINE, $i);
			$nbt->setString($textKey, $this->getLine($i - 1));
		}
		$textPolyfill = function(CompoundTag $textTag) : CompoundTag{
			$textTag->setInt(self::TAG_TEXT_COLOR, Binary::signInt(0xff_00_00_00));
			$textTag->setByte(self::TAG_GLOWING_TEXT, 0);
			$textTag->setByte(self::TAG_PERSIST_FORMATTING, 1); 
			return $textTag;
		};
		$front_text_tag = $textPolyfill(new CompoundTag(self::TAG_FRONT_TEXT));
		$front_text_tag->setString(self::TAG_TEXT_BLOB, implode("\n", $this->text));
		$nbt->setTag(
			$front_text_tag
		);
		$back_text_tag = $textPolyfill(new CompoundTag(self::TAG_BACK_TEXT));
		$back_text_tag->setString(self::TAG_TEXT_BLOB, "");
		$nbt->setTag(
			$back_text_tag
		);
		$nbt->setByte(self::TAG_WAXED, 0);
		$nbt->setLong(self::TAG_LOCKED_FOR_EDITING_BY, $this->editorEntityRuntimeId ?? -1);
	}
	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool{
		if($nbt->getString("id") !== Tile::SIGN){
			return false;
		}
		
        $lines = [];
		if($nbt->hasTag(self::TAG_TEXT_BLOB, StringTag::class)){
			$lines = self::fixTextBlob($nbt->getString(self::TAG_TEXT_BLOB));
		}else{
			$lines = [
				$nbt->getString("Text1"),
				$nbt->getString("Text2"),
				$nbt->getString("Text3"),
				$nbt->getString("Text4")
			];
		}
		$size = 0;
		foreach($lines as $line){
			$size += strlen($line);
		}
		if($size > 1000){
			throw new UnexpectedValueException($player->getName() . " tried to write $size bytes of text onto a sign (bigger than max 1000)");
		}
		$removeFormat = $player->getRemoveFormat();
		$ev = new SignChangeEvent($this->getBlock(), $player, array_map(function(string $line) use ($removeFormat){ return TextFormat::clean($line, $removeFormat); }, $lines));
		if($this->editorEntityRuntimeId !== $player->getId()){
			$ev->setCancelled();
		}
		$ev->call();
		if(!$ev->isCancelled()){
			$this->setText(...$ev->getLines());
			return true;
		}else{
			return false;
		}
	}}