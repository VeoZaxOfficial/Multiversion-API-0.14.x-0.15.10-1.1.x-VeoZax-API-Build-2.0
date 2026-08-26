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
use pocketmine\command\CommandSender;use pocketmine\lang\TranslationContainer;use pocketmine\plugin\Plugin;use pocketmine\utils\TextFormat;use pocketmine\VeoZaxBrand;use function count;use function implode;use function round;use function stripos;use function strtolower;
class VersionCommand extends VanillaCommand{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"%pocketmine.command.version.description",
			"%pocketmine.command.version.usage",
			["ver", "about"]
		);
		$this->setPermission("pocketmine.command.version");
	}
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) === 0){
			$server = $sender->getServer();
			$pluginManager = $server->getPluginManager();
			$totalPlugins = count($pluginManager->getPlugins());
			$onlineCount = count($server->getOnlinePlayers());
			$maxCount = $server->getMaxPlayers();
			$levels = $server->getLevels();
			$tps = round($server->getTicksPerSecond(), 2);
			if($tps >= 20.0){
				$stageColor = TextFormat::GREEN;
				$stageText = "Exellent";
			}elseif($tps >= 18.0){
				$stageColor = TextFormat::GREEN;
				$stageText = "Good";
			}elseif($tps >= 14.0){
				$stageColor = TextFormat::YELLOW;
				$stageText = "Barely Good";
			}elseif($tps >= 10.0){
				$stageColor = TextFormat::GOLD;
				$stageText = "Average";
			}else{
				$stageColor = TextFormat::RED;
				$stageText = "Poor";
			}
			$sender->sendMessage(TextFormat::DARK_GRAY . "-----[" . TextFormat::GRAY . "Server Software Information" . TextFormat::DARK_GRAY . "]-----");
			$sender->sendMessage(TextFormat::ITALIC . TextFormat::GRAY . "This is a Custom API that Developed by VeoZax for let Legacy McPE Clients to Connect and play on a single server.");
			$sender->sendMessage(TextFormat::ITALIC . TextFormat::GRAY . "This API Currently Supports " . VeoZaxBrand::SUPPORTED_VERSIONS);
			$sender->sendMessage(TextFormat::WHITE . "Software Identity: " . TextFormat::BLUE . "Veo" . TextFormat::AQUA . "Zax" . TextFormat::RED . "API");
			$sender->sendMessage(TextFormat::WHITE . "Build Date: " . TextFormat::GRAY . VeoZaxBrand::CREATED_DATE_DISPLAY);
			$sender->sendMessage(TextFormat::AQUA . "Total Plugins Loaded: " . TextFormat::DARK_AQUA . $totalPlugins);
			$sender->sendMessage(TextFormat::GREEN . "Players: " . TextFormat::DARK_GREEN . $onlineCount . "/" . $maxCount);
			$sender->sendMessage(TextFormat::YELLOW . "Total Worlds Loaded: " . TextFormat::GOLD . count($levels));
			$sender->sendMessage(TextFormat::GRAY . "TPS: " . $stageColor . $tps . " " . TextFormat::DARK_GRAY . "(" . $stageColor . $stageText . TextFormat::DARK_GRAY . ")");
			$sender->sendMessage(TextFormat::DARK_GRAY . "--------[" . TextFormat::RED . "WARNING" . TextFormat::DARK_GRAY . "]--------");
			$sender->sendMessage(TextFormat::ITALIC . TextFormat::RED . "Do Not Re-Distribute this API without VeoZax Permission.");
			$sender->sendMessage(TextFormat::ITALIC . TextFormat::RED . "This API Still in Development, Expect Bugs, Crashes, Incompatibility.");
			$sender->sendMessage(TextFormat::GREEN . "For Latest Build, Checkout our Discord Server from " . TextFormat::DARK_GREEN . VeoZaxBrand::LINK_DISCORD);
		}else{
			$pluginName = implode(" ", $args);
			$exactPlugin = $sender->getServer()->getPluginManager()->getPlugin($pluginName);
			if($exactPlugin instanceof Plugin){
				$this->describeToSender($exactPlugin, $sender);
				return true;
			}
			$found = false;
			$pluginName = strtolower($pluginName);
			foreach($sender->getServer()->getPluginManager()->getPlugins() as $plugin){
				if(stripos($plugin->getName(), $pluginName) !== false){
					$this->describeToSender($plugin, $sender);
					$found = true;
				}
			}
			if(!$found){
				$sender->sendMessage(new TranslationContainer("pocketmine.command.version.noSuchPlugin"));
			}
		}
		return true;
	}
	private function describeToSender(Plugin $plugin, CommandSender $sender){
		$desc = $plugin->getDescription();
		$sender->sendMessage(TextFormat::DARK_GREEN . $desc->getName() . TextFormat::WHITE . " version " . TextFormat::DARK_GREEN . $desc->getVersion());
		if($desc->getDescription() !== ""){
			$sender->sendMessage($desc->getDescription());
		}
		if($desc->getWebsite() !== ""){
			$sender->sendMessage("Website: " . $desc->getWebsite());
		}
		if(count($authors = $desc->getAuthors()) > 0){
			if(count($authors) === 1){
				$sender->sendMessage("Author: " . implode(", ", $authors));
			}else{
				$sender->sendMessage("Authors: " . implode(", ", $authors));
			}
		}
	}}