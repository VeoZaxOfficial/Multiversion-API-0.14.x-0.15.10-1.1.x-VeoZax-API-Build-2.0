<?php

declare(strict_types=1);

namespace pocketmine\command\utils;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;
use pocketmine\Server;
use function array_filter;
use function count;
use function implode;
use function is_dir;
use function scandir;
use function stripos;
use function strtolower;

/**
 * Core-native port of the MultiVerse-Core plugin's /mv command
 * (list / load / tp). Ported as-is: no permission node is set, matching
 * the original plugin's fully-open access -- any player could list, load,
 * and teleport (including teleporting other players) with this command.
 */
class MultiVerseCore extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Manage and teleport between loaded worlds",
			"§8[§9Veo§bZax§cAPI§8] §fUsage§8:§e /mv <list|load|tp>",
			["multiverse"]
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!isset($args[0])){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §fUsage§8:§e /mv <list|load|tp>");
			return true;
		}

		switch(strtolower($args[0])){
			case "list":
				$this->listWorlds($sender);
				return true;

			case "load":
				$this->loadWorld($sender, $args);
				return true;

			case "tp":
				$this->teleportWorld($sender, $args);
				return true;

			default:
				$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §fUsage§8:§e /mv <list|load|tp>");
				return true;
		}
	}

	private function listWorlds(CommandSender $sender) : void{
		$worldsPath = $sender->getServer()->getDataPath() . "worlds/";

		if(!is_dir($worldsPath)){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cWorlds folder not found.");
			return;
		}

		$worldFolders = array_filter(scandir($worldsPath), function($item) use ($worldsPath){
			return $item !== "." && $item !== ".." && is_dir($worldsPath . $item);
		});

		if(empty($worldFolders)){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cNo worlds found.");
			return;
		}

		$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §aWorld folders: §f" . implode(", ", $worldFolders));
	}

	private function loadWorld(CommandSender $sender, array $args) : void{
		if(!isset($args[1])){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cUsage: /mv load <world>");
			return;
		}

		$worldName = $args[1];
		$server = $sender->getServer();

		if($server->isLevelLoaded($worldName)){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §eWorld §f{$worldName} §eis already loaded.");
			return;
		}

		$worldPath = $server->getDataPath() . "worlds/" . $worldName;

		if(!is_dir($worldPath)){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cWorld folder not found: §f{$worldName}");
			return;
		}

		if(!$server->loadLevel($worldName)){
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cFailed to load world: §f{$worldName}");
			return;
		}

		$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §aWorld §f{$worldName} §aloaded successfully!");
	}

	private function teleportWorld(CommandSender $sender, array $args) : void{
		$server = $sender->getServer();

		if(count($args) === 2){
			if(!$sender instanceof Player){
				$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cConsole must use: /mv tp <player> <world>");
				return;
			}

			$worldName = $args[1];

			if(!$server->isLevelLoaded($worldName)){
				if(!$server->loadLevel($worldName)){
					$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cWorld not found or failed to load.");
					return;
				}
			}

			$world = $server->getLevelByName($worldName);

			if($world === null){
				$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cFailed to get world object.");
				return;
			}

			$sender->teleport($world->getSpawnLocation());
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §aTeleported to §f{$worldName}");
			return;
		}

		if(count($args) === 3){
			$targetName = strtolower($args[1]);
			$target = null;

			foreach($server->getOnlinePlayers() as $player){
				if(strtolower($player->getName()) === $targetName || stripos($player->getName(), $targetName) === 0){
					$target = $player;
					break;
				}
			}

			if($target === null){
				$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cPlayer not found.");
				return;
			}

			$worldName = $args[2];

			if(!$server->isLevelLoaded($worldName)){
				if(!$server->loadLevel($worldName)){
					$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cWorld not found or failed to load.");
					return;
				}
			}

			$world = $server->getLevelByName($worldName);

			if($world === null){
				$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §cFailed to get world object.");
				return;
			}

			$target->teleport($world->getSpawnLocation());

			$target->sendMessage("§8[§9Veo§bZax§cAPI§8] §aYou have been Teleported to §f{$worldName}");
			$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §aTeleported §f{$target->getName()} §ato §f{$worldName}");
			return;
		}

		$sender->sendMessage("§cUsage:");
		$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §c/mv tp <world>");
		$sender->sendMessage("§8[§9Veo§bZax§cAPI§8] §c/mv tp <player> <world>");
	}
}
