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
namespace pocketmine\command\defaults;
use pocketmine\command\CommandSender;use pocketmine\utils\TextFormat;use function count;use function memory_get_usage;use function number_format;use function round;
class GarbageCollectorCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.gc.description",
			"%pocketmine.command.gc.usage"
		);
		$this->setPermission("pocketmine.command.gc");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		$chunksCollected = 0;
		$entitiesCollected = 0;
		$tilesCollected = 0;
		$memory = memory_get_usage();
		foreach($sender->getServer()->getLevels() as $level){
			$diff = [count($level->getChunks()), count($level->getEntities()), count($level->getTiles())];
			$level->doChunkGarbageCollection();
			$level->unloadChunks(true);
			$chunksCollected += $diff[0] - count($level->getChunks());
			$entitiesCollected += $diff[1] - count($level->getEntities());
			$tilesCollected += $diff[2] - count($level->getTiles());
			$level->clearCache(true);
		}
		$cyclesCollected = $sender->getServer()->getMemoryManager()->triggerGarbageCollector();
		$sender->sendMessage(TextFormat::GREEN . "---- " . TextFormat::WHITE . "Garbage collection result" . TextFormat::GREEN . " ----");
		$sender->sendMessage(TextFormat::GOLD . "Chunks: " . TextFormat::RED . number_format($chunksCollected));
		$sender->sendMessage(TextFormat::GOLD . "Entities: " . TextFormat::RED . number_format($entitiesCollected));
		$sender->sendMessage(TextFormat::GOLD . "Tiles: " . TextFormat::RED . number_format($tilesCollected));
		$sender->sendMessage(TextFormat::GOLD . "Cycles: " . TextFormat::RED . number_format($cyclesCollected));
		$sender->sendMessage(TextFormat::GOLD . "Memory freed: " . TextFormat::RED . number_format(round((($memory - memory_get_usage()) / 1024) / 1024, 2), 2) . " MB");
		return true;
	}}