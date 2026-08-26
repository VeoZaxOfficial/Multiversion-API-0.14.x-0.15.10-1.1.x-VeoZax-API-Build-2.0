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
use PHPUnit\Framework\TestCase;use pocketmine\nbt\tag\IntTag;use pocketmine\nbt\tag\ListTag;
class JsonNbtParserTest extends TestCase{
	public function testIncompleteCompound() : void{
		$this->expectExceptionMessage("unexpected end of stream");
		JsonNbtParser::parseJson("{SomeTag:[]");
	}
	public function testIncompleteList() : void{
		$this->expectExceptionMessage("unexpected end of stream");
		JsonNbtParser::parseJson("{SomeTag:[");
	}
	public function testTrailingBraces() : void{
		$this->expectExceptionMessage("unexpected trailing characters");
		JsonNbtParser::parseJson("{SomeTag:[]}}");
	}
	public function testWrongOuterTagType() : void{
		$this->expectExceptionMessage("expected compound start");
		JsonNbtParser::parseJson("[1,2,3]");
	}
	public function testParseGarbage() : void{
		$this->expectExceptionMessage("expected compound start");
		JsonNbtParser::parseJson("dsfhjfughfuy{string:string} ");
	}
	public function testEmptyCompound() : void{
		$tag = JsonNbtParser::parseJson("{}");
		self::assertNotNull($tag);
		self::assertCount(0, $tag);
	}
	public function testEmptyList() : void{
		$tag = JsonNbtParser::parseJson("{TestList:[]}");
		self::assertNotNull($tag);
		$list = $tag->getTag("TestList");
		self::assertTrue($list instanceof ListTag);
		self::assertCount(0, $list);
	}
	public function testMixedList() : void{
		$this->expectExceptionMessage("lists can only contain one type of value");
		JsonNbtParser::parseJson("{TestList:[1f, string2, 3b]}");
	}
	public function testQuotedKeys() : void{
		$tag = JsonNbtParser::parseJson("{\"String With Spaces\": 1}");
		self::assertTrue($tag->getTag("String With Spaces") instanceof IntTag);
	}
	public function testQuotedValues() : void{
		$tag = JsonNbtParser::parseJson("{TestString:\"  TEST  minecraft:stone  \"}");
		self::assertSame("  TEST  minecraft:stone  ", $tag->getString("TestString"));
	}
	public function testDuplicateCompoundKeysSameType() : void{
		$this->expectExceptionMessage("duplicate compound leaf node");
		JsonNbtParser::parseJson("{Test:hi,Test:hi}");
	}
	public function testDuplicateCompoundKeysDifferentType() : void{
		$this->expectExceptionMessage("duplicate compound leaf node");
		JsonNbtParser::parseJson("{Test:hi,Test:1}");
	}}