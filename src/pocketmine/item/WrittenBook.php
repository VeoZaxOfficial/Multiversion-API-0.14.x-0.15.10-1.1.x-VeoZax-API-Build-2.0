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
use InvalidArgumentException;
class WrittenBook extends WritableBook{
	public const GENERATION_ORIGINAL = 0;
	public const GENERATION_COPY = 1;
	public const GENERATION_COPY_OF_COPY = 2;
	public const GENERATION_TATTERED = 3;
	public const TAG_GENERATION = "generation"; 
	public const TAG_AUTHOR = "author"; 
	public const TAG_TITLE = "title"; 
	public function __construct(int $meta = 0){
		Item::__construct(self::WRITTEN_BOOK, $meta, "Written Book");
	}
	public function getMaxStackSize() : int{
		return 16;
	}
	public function getGeneration() : int{
		return $this->getNamedTag()->getInt(self::TAG_GENERATION, -1);
	}
	public function setGeneration(int $generation) : void{
		if($generation < 0 or $generation > 3){
			throw new InvalidArgumentException("Generation \"$generation\" is out of range");
		}
		$namedTag = $this->getNamedTag();
		$namedTag->setInt(self::TAG_GENERATION, $generation);
		$this->setNamedTag($namedTag);
	}
	public function getAuthor() : string{
		return $this->getNamedTag()->getString(self::TAG_AUTHOR, "");
	}
	public function setAuthor(string $authorName) : void{
		$namedTag = $this->getNamedTag();
		$namedTag->setString(self::TAG_AUTHOR, $authorName);
		$this->setNamedTag($namedTag);
	}
	public function getTitle() : string{
		return $this->getNamedTag()->getString(self::TAG_TITLE, "");
	}
	public function setTitle(string $title) : void{
		$namedTag = $this->getNamedTag();
		$namedTag->setString(self::TAG_TITLE, $title);
		$this->setNamedTag($namedTag);
	}}