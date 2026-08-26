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
use pocketmine\level\format\Chunk;use pocketmine\level\format\io\exception\CorruptedChunkException;use pocketmine\level\format\io\exception\UnsupportedChunkFormatException;use pocketmine\math\Vector3;
interface LevelProvider{
	public function __construct(string $path);
	public static function getProviderName() : string;
	public function getWorldHeight() : int;
	public function getPath() : string;
	public static function isValid(string $path) : bool;
	public static function generate(string $path, string $name, int $seed, string $generator, array $options = []);
	public function getGenerator() : string;
	public function getGeneratorOptions() : array;
	public function saveChunk(Chunk $chunk) : void;
	public function loadChunk(int $chunkX, int $chunkZ) : ?Chunk;
	public function getName() : string;
	public function getTime() : int;
	public function setTime(int $value);
	public function getSeed() : int;
	public function setSeed(int $value);
	public function getSpawn() : Vector3;
	public function setSpawn(Vector3 $pos);
	public function getDifficulty() : int;
	public function setDifficulty(int $difficulty);
	public function doGarbageCollection();
	public function close();
}