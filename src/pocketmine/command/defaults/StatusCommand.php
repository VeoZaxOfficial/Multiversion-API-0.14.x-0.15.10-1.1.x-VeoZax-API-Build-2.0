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
use pocketmine\command\CommandSender;use pocketmine\utils\TextFormat;use pocketmine\utils\Utils;use function count;use function floor;use function microtime;use function number_format;use function round;use const pocketmine\START_TIME;
class StatusCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.status.description",
			"%pocketmine.command.status.usage"
		);
		$this->setPermission("pocketmine.command.status");
	}
	private function formatMemory(float $mb) : string{
		if($mb >= 1024){
			return number_format(round($mb / 1024, 1), 1) . "GB";
		}
		return number_format(round($mb, 2), 2) . "MB";
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		$rUsage = Utils::getRealMemoryUsage();
		$mUsage = Utils::getMemoryUsage(true);
		$server = $sender->getServer();
		$sender->sendMessage(TextFormat::DARK_GRAY . "-----[" . TextFormat::BLUE . "Veo" . TextFormat::AQUA . "Zax" . TextFormat::RED . "API " . TextFormat::WHITE . "Server Health Status" . TextFormat::DARK_GRAY . "]-----");
		$time = (int) (microtime(true) - START_TIME);
		$seconds = $time % 60;
		$minutes = null;
		$hours = null;
		$days = null;
		if($time >= 60){
			$minutes = floor(($time % 3600) / 60);
			if($time >= 3600){
				$hours = floor(($time % (3600 * 24)) / 3600);
				if($time >= 3600 * 24){
					$days = floor($time / (3600 * 24));
				}
			}
		}
		$uptime = ($minutes !== null ?
				($hours !== null ?
					($days !== null ?
						"$days days "
					: "") . "$hours hours "
					: "") . "$minutes minutes "
			: "") . "$seconds seconds";
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Server Uptime: " . TextFormat::RED . $uptime);
		$tpsColor = TextFormat::GREEN;
		if($server->getTicksPerSecond() < 17){
			$tpsColor = TextFormat::GOLD;
		}elseif($server->getTicksPerSecond() < 12){
			$tpsColor = TextFormat::RED;
		}
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Current TPS: {$tpsColor}{$server->getTicksPerSecond()} ({$server->getTickUsage()}%)");
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Average TPS: {$tpsColor}{$server->getTicksPerSecondAverage()} ({$server->getTickUsageAverage()}%)");
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Online: " . TextFormat::GREEN . count($server->getOnlinePlayers()) . "/" . $server->getMaxPlayers());
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Network Upload: " . TextFormat::RED . round($server->getNetwork()->getUpload() / 1024, 2) . " kB/s");
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Network Download: " . TextFormat::RED . round($server->getNetwork()->getDownload() / 1024, 2) . " kB/s");
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Thread Count: " . TextFormat::RED . Utils::getThreadCount());
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Main Thread Memory: " . TextFormat::RED . $this->formatMemory(($mUsage[0] / 1024) / 1024));
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Total Memory: " . TextFormat::RED . $this->formatMemory(($mUsage[1] / 1024) / 1024));
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Total Virtual Memory: " . TextFormat::RED . $this->formatMemory(($mUsage[2] / 1024) / 1024));
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Heap Memory: " . TextFormat::RED . $this->formatMemory(($rUsage[0] / 1024) / 1024));
		$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Maximum Memory (System): " . TextFormat::RED . $this->formatMemory(($mUsage[2] / 1024) / 1024));
		if($server->getProperty("memory.global-limit") > 0){
			$sender->sendMessage(TextFormat::DARK_GRAY . "-> " . TextFormat::WHITE . "Maximum Memory (Manager): " . TextFormat::RED . $this->formatMemory((float) $server->getProperty("memory.global-limit")));
		}
		$index = 1;
		foreach($server->getLevels() as $level){
			$sender->sendMessage(TextFormat::DARK_GRAY . "[{$index}] " . TextFormat::WHITE . "World " . TextFormat::DARK_GRAY . "| " . TextFormat::YELLOW . $level->getFolderName() . " " .
				TextFormat::DARK_GRAY . "| " . TextFormat::RED . number_format(count($level->getChunks())) . " " . TextFormat::GRAY . "Chunks " .
				TextFormat::DARK_GRAY . "| " . TextFormat::RED . number_format(count($level->getEntities())) . " " . TextFormat::GRAY . "Entities " .
				TextFormat::DARK_GRAY . "| " . TextFormat::AQUA . round($level->getTickRateTime(), 2) . "ms " . TextFormat::GRAY . "Time Taken"
			);
			$index++;
		}
		return true;
	}}