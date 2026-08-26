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
use pocketmine\command\Command;use pocketmine\command\CommandSender;use pocketmine\level\Level;use pocketmine\scheduler\ClosureTask;use pocketmine\Server;use pocketmine\utils\TextFormat;use function count;use function floor;use function microtime;use function sprintf;
class PregenCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Pre-generates and populates chunks around a world's spawn point",
			"/pregen [radius] [world]"
		);
		$this->setPermission("pocketmine.command.pregen");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		$radius = 8;
		if(isset($args[0])){
			if(!is_numeric($args[0]) or (int) $args[0] <= 0){
				$sender->sendMessage(TextFormat::RED . "Radius must be a positive number.");
				return true;
			}
			$radius = (int) $args[0];
		}
		$server = $sender->getServer();
		$level = isset($args[1]) ? $server->getLevelByName($args[1]) : $server->getDefaultLevel();
		if($level === null){
			$sender->sendMessage(TextFormat::RED . "Unknown world" . (isset($args[1]) ? ": " . $args[1] : "."));
			return true;
		}
		$spawn = $level->getSpawnLocation();
		$centerX = $spawn->getFloorX() >> 4;
		$centerZ = $spawn->getFloorZ() >> 4;
		$targets = [];
		for($dx = -$radius; $dx <= $radius; ++$dx){
			for($dz = -$radius; $dz <= $radius; ++$dz){
				if(($dx * $dx + $dz * $dz) <= $radius * $radius){
					$targets[Level::chunkHash($centerX + $dx, $centerZ + $dz)] = [$centerX + $dx, $centerZ + $dz];
				}
			}
		}
		$total = count($targets);
		$sender->sendMessage(TextFormat::YELLOW . "Pre-generating $total chunks around " . $level->getName() . " spawn (radius $radius)...");
		$startTime = microtime(true);
		$lastReported = -1;
		$handler = null;
		$handler = $server->getScheduler()->scheduleRepeatingTask(new ClosureTask(
			function(int $currentTick) use ($sender, $level, &$targets, $total, &$lastReported, $startTime, &$handler) : void{
				if($level->isClosed()){
					$sender->sendMessage(TextFormat::RED . "Pregeneration aborted: world was unloaded.");
					$handler->cancel();
					return;
				}
				foreach($targets as $index => [$x, $z]){
					if($level->isChunkPopulated($x, $z)){
						unset($targets[$index]);
						continue;
					}
					$level->populateChunk($x, $z, true);
				}
				$remaining = count($targets);
				$done = $total - $remaining;
				$percent = $total > 0 ? (int) floor(($done / $total) * 100) : 100;
				if($percent >= $lastReported + 10 or $remaining === 0){
					$lastReported = $percent;
					$sender->sendMessage(TextFormat::YELLOW . "Pregen: $done/$total chunks ($percent%)");
				}
				if($remaining === 0){
					$elapsed = microtime(true) - $startTime;
					$sender->sendMessage(TextFormat::GREEN . sprintf("Pregeneration complete: %d chunks in %.1fs", $total, $elapsed));
					$handler->cancel();
				}
			}
		), 20); 
		return true;
	}}