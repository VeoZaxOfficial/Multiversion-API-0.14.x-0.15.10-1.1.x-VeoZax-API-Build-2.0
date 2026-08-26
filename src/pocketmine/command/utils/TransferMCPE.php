<?php

declare(strict_types=1);

namespace pocketmine\command\utils;

use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\lang\TranslationContainer;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use function array_keys;
use function count;
use function function_exists;
use function gethostbyname;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function json_encode;
use function preg_match;
use function spl_object_hash;
use function stripos;
use function strrpos;
use function strtolower;
use function substr;
use function trim;

/**
 * Core-native port of the MCPETransfer plugin's /transfer, /rejoin and
 * /transfer-lobby commands. Registered three times in SimpleCommandMap
 * under different names, each instance dispatching by $this->getName().
 *
 * Replaces the core's stock TransferServerCommand ("/transferserver"),
 * which has been removed.
 *
 * The actual transfer (event firing, TransferPacket, closing the
 * connection) is delegated to Player::transfer(), which already exists
 * in core and already fires the core's PlayerTransferEvent -- this file
 * does not duplicate that.
 *
 * The 5-second countdown/title sequence is ticked from Server::tick()'s
 * existing once-per-second block via self::tick(), the same zero-extra-
 * overhead pattern used for AutoRestart and ThreadMT: no scheduler, no
 * Task object, just a static array checked once a second.
 *
 * No permission node is set, matching the original plugin's fully-open
 * access -- any player could run these commands, including transferring
 * other players.
 */
class TransferMCPE extends VanillaCommand{

	private const COUNTDOWN_LINES = [
		"§fYou are being Transferred within §o§a5 §7seconds..",
		"§fYou are being Transferred within §o§e4 §7seconds..",
		"§fYou are being Transferred within §o§e3 §7seconds..",
		"§fYou are being Transferred within §o§c2 §7seconds..",
		"§fYou are being Transferred within §o§c1§7seconds..",
		"§fYou are being Transferred within §o§c0 §7seconds..",
	];
	private const PROCESSING_LOG_INDEX = 1;
	private const TITLE_LINE = "§l§bMCPE §3Transfer§r";
	private const FINAL_LINE = "§o§7Transferring..";
	private const PREFIX = "§8[§9Veo§bZax§cAPI§8] ";
	private const LEGACY_BLOCK_MESSAGE = self::PREFIX . "§cYou cannot do Transfer due to Connected with Legacy Client. Switch to 1.1.x or higher to run this Command.";

	/** @var array<string, array{player:Player, ip:string, port:int, label:string, step:int}> */
	private static $pending = [];

	/** @var array<string, string> */
	private static $lookupCache = [];

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Transfer players between servers/lobbies",
			self::PREFIX . "§fUsage§8:§e /" . $name
		);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player && $sender->getOriginalProtocol() <= ProtocolInfo::PROTOCOL_84){
			$sender->sendMessage(self::LEGACY_BLOCK_MESSAGE);
			return true;
		}

		switch($this->getName()){
			case "transfer":
				return $this->handleTransfer($sender, $args);
			case "rejoin":
				return $this->handleRejoin($sender, $args);
			case "transfer-lobby":
				return $this->handleTransferLobby($sender, $args);
		}
		return false;
	}

	/**
	 * Ticked once per second from Server::tick()'s existing once-per-second
	 * block. Advances every pending transfer's countdown by one step, and
	 * performs the actual transfer once a countdown finishes.
	 */
	public static function tick(Server $server) : void{
		if(empty(self::$pending)){
			return;
		}
		foreach(self::$pending as $key => $data){
			$player = $data["player"];
			if(!$player->isOnline()){
				unset(self::$pending[$key]);
				continue;
			}
			$step = $data["step"];
			if($step < count(self::COUNTDOWN_LINES)){
				$player->addTitle(self::TITLE_LINE, self::COUNTDOWN_LINES[$step], 0, 25, 0);
				if($step === self::PROCESSING_LOG_INDEX){
					self::sendDiscordLog($server, "**[MCPE Transfer]** Processing `" . $player->getName() . "`'s Connection with " . $data["label"]);
				}
				self::$pending[$key]["step"] = $step + 1;
			}else{
				$player->addTitle(self::TITLE_LINE, self::FINAL_LINE, 0, 25, 0);
				$player->transfer($data["ip"], $data["port"]);
				self::sendDiscordLog($server, "**[MCPE Transfer]** Successfully Transferred `" . $player->getName() . "` to " . $data["label"]);
				unset(self::$pending[$key]);
			}
		}
	}

	private function handleTransfer(CommandSender $sender, array $args) : bool{
		if(count($args) < 2 or count($args) > 3 or (count($args) === 2 and !($sender instanceof Player))){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->getUsage()]));
			return true;
		}
		$target = $sender;
		if(count($args) === 3){
			$target = $sender->getServer()->getPlayer($args[0]);
			$address = $args[1];
			$port = (int) $args[2];
		}else{
			$address = $args[0];
			$port = (int) $args[1];
		}
		if($target === null){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
			return true;
		}
		$sender->sendMessage("Transferring player " . $target->getDisplayName() . " to $address:$port");
		$result = $this->beginTransfer($target, $address, $port);
		if($result !== true){
			if($result === "already-connected"){
				if($sender !== $target){
					$sender->sendMessage(TextFormat::RED . "That player is already connected to $address:$port.");
				}
			}else{
				$sender->sendMessage(TextFormat::RED . (is_string($result) ? $result : "An error occurred during the transfer"));
			}
		}
		return true;
	}

	private function handleRejoin(CommandSender $sender, array $args) : bool{
		if(count($args) > 1){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->getUsage()]));
			return true;
		}
		$server = $sender->getServer();
		if(count($args) === 1){
			$target = $server->getPlayer($args[0]);
		}elseif($sender instanceof Player){
			$target = $sender;
		}else{
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->getUsage()]));
			return true;
		}
		if($target === null){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
			return true;
		}
		$sender->sendMessage("Reconnecting player " . $target->getDisplayName() . " to this server");
		$result = $this->beginRejoin($target);
		if($result !== true){
			$sender->sendMessage(TextFormat::RED . (is_string($result) ? $result : "An error occurred during the transfer"));
		}
		return true;
	}

	private function handleTransferLobby(CommandSender $sender, array $args) : bool{
		$server = $sender->getServer();
		if(count($args) < 1 or count($args) > 2 or (count($args) === 1 and !($sender instanceof Player))){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->getUsage()]));
			$available = array_keys($this->getLobbies($server));
			if(!empty($available)){
				$sender->sendMessage("Available lobbies: " . implode(", ", $available));
			}
			return true;
		}
		$target = $sender;
		if(count($args) === 2){
			$target = $server->getPlayer($args[0]);
			$lobbyName = $args[1];
		}else{
			$lobbyName = $args[0];
		}
		if($target === null){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
			return true;
		}
		$sender->sendMessage("Transferring player " . $target->getDisplayName() . " to lobby \"" . $lobbyName . "\"");
		$result = $this->beginTransferToLobby($target, $lobbyName);
		if($result !== true){
			if($result === "already-connected"){
				if($sender !== $target){
					$sender->sendMessage(TextFormat::RED . "That player is already connected to lobby \"" . $lobbyName . "\".");
				}
			}else{
				$sender->sendMessage(TextFormat::RED . (is_string($result) ? $result : "An error occurred during the transfer"));
			}
		}
		return true;
	}

	private function beginTransfer(Player $player, string $address, int $port){
		$server = $player->getServer();
		$ip = $this->lookupAddress($address);
		if($ip === null){
			return "Could not resolve the address \"" . $address . "\".";
		}
		if($this->isSameServer($server, $ip, $port)){
			$player->sendMessage(TextFormat::RED . self::PREFIX . "You are already in " . $address . ":" . $port);
			return "already-connected";
		}
		$logLabel = "`" . $address . ":" . $port . "`";
		$this->beginTransferSequence($player, $ip, $port, $logLabel);
		return true;
	}

	private function beginRejoin(Player $player){
		$server = $player->getServer();
		[$ip, $port] = $this->getSelfAddress($server);
		$wildcards = ["0.0.0.0", "", "127.0.0.1", "localhost"];
		if($ip === "" || $port <= 0 || in_array($ip, $wildcards, true)){
			return "This server's address isn't a public address the client can reconnect to. Set transfer-self-address in VeoZax.yml (e.g. \"1.2.3.4:19132\") to use /rejoin.";
		}
		$resolvedIp = $this->lookupAddress($ip);
		if($resolvedIp === null){
			return "Could not resolve this server's own address (" . $ip . ").";
		}
		$logLabel = "`" . $ip . ":" . $port . "` _(rejoin)_";
		$this->beginTransferSequence($player, $resolvedIp, $port, $logLabel);
		return true;
	}

	private function beginTransferToLobby(Player $player, string $lobbyName){
		$server = $player->getServer();
		$lobby = $this->getLobby($server, $lobbyName);
		if($lobby === null){
			$available = array_keys($this->getLobbies($server));
			if(empty($available)){
				return "Lobby \"" . $lobbyName . "\" was not found, and no lobbies are configured in VeoZax.yml yet.";
			}
			return "Lobby \"" . $lobbyName . "\" was not found. Available lobbies: " . implode(", ", $available);
		}
		if($this->isSameServer($server, $lobby["ip"], $lobby["port"])){
			$player->sendMessage(TextFormat::RED . self::PREFIX . "You are already in \"" . $lobby["name"] . "\"");
			return "already-connected";
		}
		$ip = $this->lookupAddress($lobby["ip"]);
		if($ip === null){
			return "Could not resolve the address for lobby \"" . $lobby["name"] . "\".";
		}
		$logLabel = "Lobby: `" . $lobby["name"] . "`";
		$this->beginTransferSequence($player, $ip, $lobby["port"], $logLabel);
		return true;
	}

	private function beginTransferSequence(Player $player, string $ip, int $port, string $logLabel) : void{
		$server = $player->getServer();
		self::sendDiscordLog($server, "**[MCPE Transfer]** `" . $player->getName() . "` _tried to Connect with_ " . $logLabel);
		$player->addTitle(self::TITLE_LINE, self::COUNTDOWN_LINES[0], 0, 25, 0);
		self::$pending[spl_object_hash($player)] = [
			"player" => $player,
			"ip" => $ip,
			"port" => $port,
			"label" => $logLabel,
			"step" => 0
		];
	}

	private static function sendDiscordLog(Server $server, string $message) : void{
		$config = $server->getVeoZaxConfig();
		if(!(bool) $config->get("transfer-webhook", false)){
			return;
		}
		$url = trim((string) $config->get("transfer-webhook-url", ""));
		if($url === "" || stripos($url, "http") !== 0){
			return;
		}
		if(!function_exists("curl_init")){
			return;
		}
		$data = json_encode([
			"content" => $message,
			"username" => "MCPE Transfer"
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

	private function getLobbies(Server $server) : array{
		$raw = $server->getVeoZaxConfig()->get("transfer-lobbies", []);
		$lobbies = [];
		if(is_array($raw)){
			foreach($raw as $key => $value){
				if(is_array($value) && isset($value["ip"]) && isset($value["port"])){
					$lobbies[$key] = [
						"name" => (string) $key,
						"ip" => (string) $value["ip"],
						"port" => (int) $value["port"]
					];
				}
			}
		}
		return $lobbies;
	}

	private function getLobby(Server $server, string $name) : ?array{
		$lobbies = $this->getLobbies($server);
		if(isset($lobbies[$name])){
			return $lobbies[$name];
		}
		foreach($lobbies as $key => $lobby){
			if(strtolower($key) === strtolower($name)){
				return $lobby;
			}
		}
		return null;
	}

	private function getSelfAddress(Server $server) : array{
		$override = trim((string) $server->getVeoZaxConfig()->get("transfer-self-address", ""));
		if($override !== ""){
			$lastColon = strrpos($override, ":");
			if($lastColon !== false){
				$ip = substr($override, 0, $lastColon);
				$port = (int) substr($override, $lastColon + 1);
				if($ip !== "" && $port > 0){
					return [$ip, $port];
				}
			}
		}
		return [$server->getIp(), $server->getPort()];
	}

	private function isSameServer(Server $server, string $lobbyIp, int $lobbyPort) : bool{
		[$selfIp, $selfPort] = $this->getSelfAddress($server);
		if($lobbyPort !== $selfPort){
			return false;
		}
		$wildcards = ["0.0.0.0", "", "127.0.0.1", "localhost"];
		if(in_array($selfIp, $wildcards, true) || in_array($lobbyIp, $wildcards, true)){
			return true;
		}
		return $lobbyIp === $selfIp;
	}

	private function lookupAddress(string $address) : ?string{
		if(preg_match("/^[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}$/", $address) > 0){
			return $address;
		}
		$key = strtolower($address);
		if(isset(self::$lookupCache[$key])){
			return self::$lookupCache[$key];
		}
		$host = gethostbyname($address);
		if($host === $address){
			return null;
		}
		self::$lookupCache[$key] = $host;
		return $host;
	}
}
