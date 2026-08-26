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
use InvalidArgumentException;use pocketmine\nbt\NBT;use pocketmine\nbt\tag\CompoundTag;use pocketmine\nbt\tag\ListTag;use pocketmine\nbt\tag\StringTag;
class WritableBook extends Item{
	public const TAG_PAGES = "pages"; 
	public const TAG_PAGE_TEXT = "text"; 
	public const TAG_PAGE_PHOTONAME = "photoname"; 
	public function __construct(int $meta = 0){
		parent::__construct(self::WRITABLE_BOOK, $meta, "Book & Quill");
	}
	public function pageExists(int $pageId) : bool{
		return $this->getPagesTag()->isset($pageId);
	}
	public function getPageText(int $pageId) : ?string{
		$pages = $this->getNamedTag()->getListTag(self::TAG_PAGES);
		if($pages === null){
			return null;
		}
		$page = $pages->get($pageId);
		if($page instanceof CompoundTag){
			return $page->getString(self::TAG_PAGE_TEXT, "");
		}
		return null;
	}
	public function setPageText(int $pageId, string $pageText) : bool{
		$created = false;
		if(!$this->pageExists($pageId)){
			$this->addPage($pageId);
			$created = true;
		}
		$pagesTag = $this->getPagesTag();
		$page = $pagesTag->get($pageId);
		$page->setString(self::TAG_PAGE_TEXT, $pageText);
		$this->setNamedTagEntry($pagesTag);
		return $created;
	}
	public function addPage(int $pageId) : void{
		if($pageId < 0){
			throw new InvalidArgumentException("Page number \"$pageId\" is out of range");
		}
		$pagesTag = $this->getPagesTag();
		for($current = $pagesTag->count(); $current <= $pageId; $current++){
			$pagesTag->push(new CompoundTag("", [
				new StringTag(self::TAG_PAGE_TEXT, ""),
				new StringTag(self::TAG_PAGE_PHOTONAME, "")
			]));
		}
		$this->setNamedTagEntry($pagesTag);
	}
	public function deletePage(int $pageId) : bool{
		$pagesTag = $this->getPagesTag();
		$pagesTag->remove($pageId);
		$this->setNamedTagEntry($pagesTag);
		return true;
	}
	public function insertPage(int $pageId, string $pageText = "") : bool{
		$pagesTag = $this->getPagesTag();
		$pagesTag->insert($pageId, new CompoundTag("", [
			new StringTag(self::TAG_PAGE_TEXT, $pageText),
			new StringTag(self::TAG_PAGE_PHOTONAME, "")
		]));
		$this->setNamedTagEntry($pagesTag);
		return true;
	}
	public function swapPages(int $pageId1, int $pageId2) : bool{
		if(!$this->pageExists($pageId1) or !$this->pageExists($pageId2)){
			return false;
		}
		$pageContents1 = $this->getPageText($pageId1);
		$pageContents2 = $this->getPageText($pageId2);
		$this->setPageText($pageId1, $pageContents2);
		$this->setPageText($pageId2, $pageContents1);
		return true;
	}
	public function getMaxStackSize() : int{
		return 1;
	}
	public function getPages() : array{
		$pages = $this->getPagesTag()->getValue();
		return $pages;
	}
	protected function getPagesTag() : ListTag{
		$pagesTag = $this->getNamedTag()->getListTag(self::TAG_PAGES);
		if($pagesTag !== null and $pagesTag->getTagType() === NBT::TAG_Compound){
			return $pagesTag;
		}
		return new ListTag(self::TAG_PAGES, [], NBT::TAG_Compound);
	}
	public function setPages(array $pages) : void{
		$nbt = $this->getNamedTag();
		$nbt->setTag(new ListTag(self::TAG_PAGES, $pages, NBT::TAG_Compound));
		$this->setNamedTag($nbt);
	}}