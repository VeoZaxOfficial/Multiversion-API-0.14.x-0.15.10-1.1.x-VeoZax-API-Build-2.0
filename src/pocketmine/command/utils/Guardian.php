<?php

declare(strict_types=1);

namespace pocketmine\command\utils;

use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use function array_filter;
use function array_map;
use function array_shift;
use function array_values;
use function count;
use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function function_exists;
use function implode;
use function in_array;
use function is_array;
use function json_encode;
use function ltrim;
use function spl_object_hash;
use function stripos;
use function strtolower;
use function trim;
use function ucfirst;

/**
 * Anti Exploit Guard.
 *
 * Locks Administrators and Members (i.e. every player who is not a Main
 * Owner or Sub Owner) down to a per-rank allow list of commands. Any
 * command not on a rank's allow list is silently rejected before it ever
 * reaches its handler. Main Owners and Sub Owners always bypass this
 * check entirely.
 *
 * Enforcement is wired into SimpleCommandMap::dispatch(), which calls
 * Guardian::isAllowed() for every command a Player sends and blocks it
 * with Guardian::block() if it isn't allow-listed. This command itself
 * (registered as "/anti") is exempt from that enforcement -- access to
 * it is controlled separately below, restricted to Main/Sub Owners only.
 *
 * Enforcement only applies when Player::isDirectClientInput() is true,
 * i.e. the command genuinely came from that player's own connected
 * client sending a real packet (TextPacket/CommandRequestPacket/
 * CommandStepPacket, handled in PlayerNetworkSessionAdapter and
 * Player::handleCommandStep()). A command dispatched on a player's
 * behalf by another plugin -- for example an NPC plugin running a
 * command when its NPC is clicked, via chat() or
 * Server::dispatchCommand() called directly -- is NOT flagged as direct
 * client input and therefore bypasses this check entirely, regardless
 * of rank. This is intentional: Guardian exists to stop a player from
 * typing a restricted command themselves, not to block server-side
 * systems (NPCs, other plugins) from running commands through a player.
 *
 * Allow lists are persisted to Guardian.yml in the server's data folder:
 *   members:
 *     allowed: [...]
 *   administrators:
 *     allowed: [...]
 *
 * Optionally posts a Discord webhook whenever a command is blocked or an
 * Owner changes an allow list, controlled by VeoZax.yml:
 *   guardian-webhook: true|false (default false)
 *   guardian-webhook-url: "https://discord.com/api/webhooks/..."
 */
class Guardian extends VanillaCommand{

	private const NOT_ALLOWED_MESSAGE = "§eThat Command Is not on the Allowed List.";
	private const OWNER_ONLY_MESSAGE = "§o§cOnly Server Owners can Run this command.";

	public const RANK_MEMBERS = "members";
	public const RANK_ADMINISTRATORS = "administrators";

	/** @var array<string, Config> */
	private static $configs = [];

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Manage the Anti Exploit Guard's per-rank command allow list",
			"§fUsage§8:§e /anti <members|administrators> <allow|deny> <command>\n" .
			"§fUsage§8:§e /anti seeallowed <members|administrators>"
		);
		$this->setPermission("pocketmine.command.anti");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		$server = $sender->getServer();
		if(!$server->isMainOwner($sender->getName()) and !$server->isSubOwner($sender->getName())){
			$sender->sendMessage(self::OWNER_ONLY_MESSAGE);
			return true;
		}

		if(count($args) < 1){
			throw new InvalidCommandSyntaxException();
		}

		$action = strtolower(array_shift($args));
		switch($action){
			case self::RANK_MEMBERS:
			case self::RANK_ADMINISTRATORS:
				$this->handleAllowDeny($sender, $server, $action, $args);
				return true;

			case "seeallowed":
				$this->handleSeeAllowed($sender, $server, $args);
				return true;

			default:
				throw new InvalidCommandSyntaxException();
		}
	}

	private function handleAllowDeny(CommandSender $sender, Server $server, string $rank, array $args) : void{
		if(count($args) < 2){
			throw new InvalidCommandSyntaxException();
		}

		$mode = strtolower(array_shift($args));
		if($mode !== "allow" and $mode !== "deny"){
			throw new InvalidCommandSyntaxException();
		}

		$command = strtolower(ltrim(implode(" ", $args), "/"));
		if($command === ""){
			throw new InvalidCommandSyntaxException();
		}

		$config = self::getConfig($server);
		$key = $rank . ".allowed";
		$list = $config->getNested($key, []);
		if(!is_array($list)){
			$list = [];
		}
		$lower = array_map("strtolower", $list);
		$label = ucfirst($rank);

		if($mode === "allow"){
			if(in_array($command, $lower, true)){
				$sender->sendMessage("§7That command is already on the §e" . $label . "§7 allowed list.");
				return;
			}
			$list[] = $command;
			$config->setNested($key, $list);
			$config->save();
			$sender->sendMessage("§aAllowed §e/" . $command . "§a for §e" . $label . "§a.");
			self::sendWebhook($server, "**[Anti Exploit Guard]** `" . $sender->getName() . "` allowed `/" . $command . "` for **" . $label . "**.");
		}else{
			if(!in_array($command, $lower, true)){
				$sender->sendMessage(self::NOT_ALLOWED_MESSAGE);
				return;
			}
			$filtered = array_values(array_filter($list, function(string $c) use ($command) : bool{
				return strtolower($c) !== $command;
			}));
			$config->setNested($key, $filtered);
			$config->save();
			$sender->sendMessage("§cDenied §e/" . $command . "§c for §e" . $label . "§c. It has been removed from the allowed list.");
			self::sendWebhook($server, "**[Anti Exploit Guard]** `" . $sender->getName() . "` denied `/" . $command . "` for **" . $label . "**.");
		}
	}

	private function handleSeeAllowed(CommandSender $sender, Server $server, array $args) : void{
		if(count($args) < 1){
			throw new InvalidCommandSyntaxException();
		}

		$rank = strtolower(array_shift($args));
		if($rank !== self::RANK_MEMBERS and $rank !== self::RANK_ADMINISTRATORS){
			throw new InvalidCommandSyntaxException();
		}

		$list = self::getAllowedList($server, $rank);
		$label = ucfirst($rank);

		if(count($list) === 0){
			$sender->sendMessage("§7There are currently no allowed commands for §e" . $label . "§7.");
			return;
		}

		$formatted = implode("§6, ", array_map(function(string $c) : string{
			return "§e/" . $c;
		}, $list));
		$sender->sendMessage("§7Allowed commands for §e" . $label . "§7: " . $formatted);
	}

	/**
	 * Called from SimpleCommandMap::dispatch() for every command a Player
	 * sends. Returns true if the command may proceed, false if it must be
	 * blocked. Main Owners and Sub Owners always return true.
	 */
	public static function isAllowed(Server $server, Player $player, string $commandLabel) : bool{
		$name = $player->getName();
		if($server->isMainOwner($name) or $server->isSubOwner($name)){
			return true;
		}
		$rank = $server->isAdministrator($name) ? self::RANK_ADMINISTRATORS : self::RANK_MEMBERS;
		return in_array(strtolower($commandLabel), self::getAllowedList($server, $rank), true);
	}

	/**
	 * Sends the standard "not on the Allowed List" rejection message.
	 */
	public static function block(CommandSender $sender) : void{
		$sender->sendMessage(self::NOT_ALLOWED_MESSAGE);
	}

	/**
	 * Posts a Discord webhook (if enabled) noting that a Player was
	 * blocked from running a command that wasn't on their rank's allow
	 * list. Called from SimpleCommandMap::dispatch() alongside block().
	 */
	public static function notifyBlocked(Server $server, Player $player, string $commandLabel) : void{
		self::sendWebhook($server, "**[Anti Exploit Guard]** `" . $player->getName() . "` _attempted to run_ `/" . $commandLabel . "` _which is not on their allowed list._");
	}

	/**
	 * Posts a message to the Discord webhook configured in VeoZax.yml via
	 * guardian-webhook / guardian-webhook-url. No-op if disabled, if the
	 * URL is missing/invalid, or if cURL isn't available.
	 */
	private static function sendWebhook(Server $server, string $message) : void{
		$config = $server->getVeoZaxConfig();
		if(!(bool) $config->get("guardian-webhook", false)){
			return;
		}
		$url = trim((string) $config->get("guardian-webhook-url", ""));
		if($url === "" or stripos($url, "http") !== 0){
			return;
		}
		if(!function_exists("curl_init")){
			return;
		}
		$data = json_encode([
			"content" => $message,
			"username" => "Anti Exploit Guard"
		]);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_exec($ch);
		curl_close($ch);
	}

	/**
	 * @return string[] lower-cased command names allow-listed for $rank
	 */
	private static function getAllowedList(Server $server, string $rank) : array{
		$list = self::getConfig($server)->getNested($rank . ".allowed", []);
		if(!is_array($list)){
			return [];
		}
		return array_map("strtolower", $list);
	}

	private static function getConfig(Server $server) : Config{
		$key = spl_object_hash($server);
		if(!isset(self::$configs[$key])){
			self::$configs[$key] = new Config($server->getDataPath() . "Guardian.yml", Config::YAML, [
				self::RANK_MEMBERS => ["allowed" => []],
				self::RANK_ADMINISTRATORS => ["allowed" => []],
			]);
		}
		return self::$configs[$key];
	}
}