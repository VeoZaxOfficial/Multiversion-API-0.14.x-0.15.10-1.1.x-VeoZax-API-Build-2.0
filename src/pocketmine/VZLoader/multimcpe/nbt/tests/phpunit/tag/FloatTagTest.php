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
namespace pocketmine\nbt\tag;
use PHPUnit\Framework\TestCase;use pocketmine\nbt\LittleEndianNbtSerializer;use pocketmine\nbt\TreeRoot;use const PHP_FLOAT_EPSILON;use const PHP_FLOAT_MAX;use const PHP_FLOAT_MIN;
class FloatTagTest extends TestCase{
	public function testValue() : void{
		$value = mt_rand() / mt_getrandmax();
		$tag = new FloatTag($value);
		self::assertSame($value, $tag->getValue());
	}
	public function testTooManyConstructorArgs() : void{
		$this->expectException(\ArgumentCountError::class);
		new FloatTag(1, "world");
	}
	public function equalityAfterDecodeProvider() : \Generator{
		yield [0.3];
		yield [PHP_FLOAT_EPSILON];
		yield [PHP_FLOAT_MAX];
		yield [PHP_FLOAT_MIN];
	}
	public function testEqualityAfterDecode(float $value) : void{
		$tag = new FloatTag($value);
		$serializer = new LittleEndianNbtSerializer();
		$tag2 = $serializer->read($serializer->write(new TreeRoot($tag)));
		self::assertTrue($tag->equals($tag2->getTag()));
	}}