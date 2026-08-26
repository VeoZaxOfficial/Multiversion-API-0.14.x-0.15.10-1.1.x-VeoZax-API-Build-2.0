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

namespace pocketmine;

use pocketmine\block\BlockFactory;
use pocketmine\command\CommandReader;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\SimpleCommandMap;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\event\HandlerList;
use pocketmine\event\level\LevelInitEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\inventory\CraftingManager;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\lang\BaseLang;
use pocketmine\lang\TextContainer;
use pocketmine\level\biome\Biome;
use pocketmine\level\format\io\LevelProvider;
use pocketmine\level\format\io\LevelProviderManager;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\Level;
use pocketmine\level\LevelException;
use pocketmine\maps\MapManager;
use pocketmine\metadata\EntityMetadataStore;
use pocketmine\metadata\LevelMetadataStore;
use pocketmine\metadata\PlayerMetadataStore;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\AdvancedSourceInterface;
use pocketmine\network\CompressBatchedTask;
use pocketmine\network\mcpe\encryption\EncryptionContext;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\AnyVersionManager;
use pocketmine\network\mcpe\P84VersionManager;
use pocketmine\network\mcpe\protocol\legacy\p70\BatchPacket as LegacyP70Batch;
use pocketmine\network\mcpe\protocol\legacy\p84\BatchPacket as LegacyP84Batch;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\cache\CreativePacketCache;
use pocketmine\network\mcpe\RakLibInterface;
use pocketmine\network\Network;
use pocketmine\network\query\QueryHandler;
use pocketmine\network\rcon\RCON;
use pocketmine\network\upnp\UPnP;
use pocketmine\permission\BanList;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\FolderPluginLoader;
use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\plugin\PluginManager;
use pocketmine\plugin\ScriptPluginLoader;
use pocketmine\ResourcesAPI\ResourcePackManager;
use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\SendUsageTask;
use pocketmine\snooze\SleeperHandler;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\tile\Tile;
use pocketmine\timings\Timings;
use pocketmine\timings\TimingsHandler;
use pocketmine\updater\AutoUpdater;
use pocketmine\utils\Color;
use pocketmine\command\utils\TransferMCPE;
use pocketmine\utils\Config;
use pocketmine\utils\ThreadMT;
use pocketmine\utils\Filesystem;
use pocketmine\utils\Internet;
use pocketmine\utils\MainLogger;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use pocketmine\utils\UUID;
use pocketmine\thread\AutoKillThread;
use pocketmine\thread\ThreadSafeClassLoader;
use pocketmine\thread\log\ThreadSafeLogger;
use pmmp\thread\ThreadSafeArray;
use pmmp\thread\Thread as NativeThread;
use function array_filter;
use function array_key_exists;
use function array_map;
use function array_shift;
use function array_sum;
use function array_values;
use function in_array;
use function asort;
use function assert;
use function base64_encode;
use function class_exists;
use function count;
use function define;
use function explode;
use function extension_loaded;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function function_exists;
use function get_class;
use function getmypid;
use function getopt;
use function gettype;
use function implode;
use function ini_set;
use function is_array;
use function is_bool;
use function is_dir;
use function is_object;
use function is_string;
use function is_subclass_of;
use function json_decode;
use function max;
use function microtime;
use function min;
use function mkdir;
use function ob_end_flush;
use function pcntl_signal;
use function pcntl_signal_dispatch;
use function preg_replace;
use function random_bytes;
use function random_int;
use function realpath;
use function register_shutdown_function;
use function rename;
use function round;
use function scandir;
use function sleep;
use function spl_object_hash;
use function sprintf;
use function str_repeat;
use function str_replace;
use function stripos;
use function strlen;
use function strrpos;
use function strtolower;
use function substr;
use function time;
use function touch;
use function trim;
use const DIRECTORY_SEPARATOR;
use const INT32_MAX;
use const INT32_MIN;
use const PHP_EOL;
use const PHP_INT_MAX;
use const SCANDIR_SORT_NONE;
use const SIGHUP;
use const SIGINT;
use const SIGTERM;

class Server{
	public const BROADCAST_CHANNEL_ADMINISTRATIVE = "pocketmine.broadcast.admin";
	public const BROADCAST_CHANNEL_USERS = "pocketmine.broadcast.user";
	private static $instance = null;
	private static $sleeper = null;
	private $tickSleeper;
	private $banByName = null;
	private $banByIP = null;
	private $operators = null;
	private $whitelist = null;
	private $veoZaxConfig = null;
	private $autoRestartTicker = 0;
	private $autoRestartEnabled = false;
	private $autoRestartBroadcasted30 = false;
	private $autoGCTicker = 1800;
	private $isRunning = true;
	private $hasStopped = false;
	private $pluginManager = null;
	private $profilingTickRate = 20;
	private $updater = null;
	private $asyncPool;
	private $tickCounter = 0;
	private $nextTick = 0;
	private $tickAverage = [20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20];
	private $useAverage = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
	private $currentTPS = 20;
	private $currentUse = 0;
	private $doTitleTick = true;
	private $sendUsageTicker = 0;
	private $dispatchSignals = false;
	private $logger;
	private $memoryManager;
	private $console = null;
	private $commandMap = null;
	private $craftingManager;
	private $pw10ResourcePackManager;
	private $maxPlayers;
	private $onlineMode = true;
	private $autoSave;
	private $rcon;
	private $entityMetadata;
	private $playerMetadata;
	private $levelMetadata;
	private $network;
	private $networkCompressionAsync = true;
	public $networkCompressionLevel = 7;
	private $autoSaveTicker = 0;
	private $autoSaveTicks = 6000;
	private $baseLang;
	private $forceLanguage = false;
	private $serverID;
	private $autoloader;
	private $dataPath;
	private $pluginPath;
	private $uniquePlayers = [];
	private $queryHandler;
	private $queryRegenerateTask = null;
	private $properties;
	private $propertyCache = [];
	private $config;
	private $players = [];
	private $loggedInPlayers = [];
	private $playerList = [];
	private $levels = [];
	private $levelDefault = null;
	private $advancedConfig = null;
	private $autoKillThreadEnabled = true;
	private $autoKillTimeout = 10;
	private $autoKillThread;
	private $autoKillNotifier;
	public $weatherEnabled = true;
	public $weatherRandomDurationMin = 6000;
	public $weatherRandomDurationMax = 12000;
	public $lightningTime = 200;
	public $lightningFire = false;
	public $fireSpread = false;
	public $netherEnabled = false;
	public $netherName = "nether";
	public $netherLevel = null;
	public $enderEnabled = true;
	public $enderName = "ender";
	public $enderLevel = null;
	public $folderpluginloader = false;
	public $mobAiEnabled = false;
	public $redstoneEnabled = false;
	public $allowFrequencyPulse = false;
	public $pulseFrequency = 20;
	public function getName() : string{
		return \pocketmine\NAME;
	}
	public function isRunning() : bool{
		return $this->isRunning;
	}
	public function getPocketMineVersion() : string{
		return \pocketmine\VERSION;
	}
	public function getVersion() : string{
		return ProtocolInfo::MINECRAFT_VERSION;
	}
	public function getApiVersion() : string{
		return \pocketmine\BASE_VERSION;
	}
	public function getFilePath() : string{
		return \pocketmine\PATH;
	}
	public function getResourcePath() : string{
		return \pocketmine\RESOURCE_PATH;
	}
	public function getDataPath() : string{
		return $this->dataPath;
	}
	public function getPluginPath() : string{
		return $this->pluginPath;
	}
	public function getMaxPlayers() : int{
		return $this->maxPlayers;
	}
	public function getOnlineMode() : bool{
		return $this->onlineMode;
	}
	public function requiresAuthentication() : bool{
		return $this->getOnlineMode();
	}
	public function getVeoZaxConfig() : Config{
		return $this->veoZaxConfig;
	}
	public function getPort() : int{
		return $this->getConfigInt("server-port", 19132);
	}
	public function getViewDistance() : int{
		return max(2, $this->getConfigInt("view-distance", 8));
	}
	public function getAllowedViewDistance(int $distance) : int{
		return max(2, min($distance, $this->memoryManager->getViewDistance($this->getViewDistance())));
	}
	public function getIp() : string{
		$str = $this->getConfigString("server-ip");
		return $str !== "" ? $str : "0.0.0.0";
	}
	public function getServerUniqueId(){
		return $this->serverID;
	}
	public function getAutoSave() : bool{
		return $this->autoSave;
	}
	public function setAutoSave(bool $value){
		$this->autoSave = $value;
		foreach($this->getLevels() as $level){
			$level->setAutoSave($this->autoSave);
		}
	}
	public function getLevelType() : string{
		return $this->getConfigString("level-type", "DEFAULT");
	}
	public function getGenerateStructures() : bool{
		return $this->getConfigBool("generate-structures", true);
	}
	public function getGamemode() : int{
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}
	public function getForceGamemode() : bool{
		return $this->getConfigBool("force-gamemode", false);
	}
	public static function getGamemodeString(int $mode) : string{
		switch($mode){
			case Player::SURVIVAL:
				return "%gameMode.survival";
			case Player::CREATIVE:
				return "%gameMode.creative";
			case Player::ADVENTURE:
				return "%gameMode.adventure";
			case Player::SPECTATOR:
				return "%gameMode.spectator";
		}
		return "UNKNOWN";
	}
	public static function getGamemodeName(int $mode) : string{
		switch($mode){
			case Player::SURVIVAL:
				return "Survival";
			case Player::CREATIVE:
				return "Creative";
			case Player::ADVENTURE:
				return "Adventure";
			case Player::SPECTATOR:
				return "Spectator";
			default:
				throw new \InvalidArgumentException("Invalid gamemode $mode");
		}
	}
	public static function getGamemodeFromString(string $str) : int{
		switch(strtolower(trim($str))){
			case (string) Player::SURVIVAL:
			case "survival":
			case "s":
				return Player::SURVIVAL;
			case (string) Player::CREATIVE:
			case "creative":
			case "c":
				return Player::CREATIVE;
			case (string) Player::ADVENTURE:
			case "adventure":
			case "a":
				return Player::ADVENTURE;
			case (string) Player::SPECTATOR:
			case "spectator":
			case "view":
			case "v":
				return Player::SPECTATOR;
		}
		return -1;
	}
	public function getDifficulty() : int{
		return $this->getConfigInt("difficulty", Level::DIFFICULTY_NORMAL);
	}
	public function hasWhitelist() : bool{
		return $this->getConfigBool("white-list", false);
	}
	public function getSpawnRadius() : int{
		return $this->getConfigInt("spawn-protection", 16);
	}
	public function getAllowFlight() : bool{
		return true;
	}
	public function isHardcore() : bool{
		return $this->getConfigBool("hardcore", false);
	}
	public function getDefaultGamemode() : int{
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}
	public function getMotd() : string{
		return $this->getConfigString("motd", \pocketmine\NAME . " Server");
	}
	public function getLoader(){
		return $this->autoloader;
	}
	public function getLogger(){
		return $this->logger;
	}
	public function getEntityMetadata(){
		return $this->entityMetadata;
	}
	public function getPlayerMetadata(){
		return $this->playerMetadata;
	}
	public function getLevelMetadata(){
		return $this->levelMetadata;
	}
	public function getUpdater(){
		return $this->updater;
	}
	public function getPluginManager(){
		return $this->pluginManager;
	}
	public function getCraftingManager(){
		return $this->craftingManager;
	}
	public function getPw10ResourcePackManager() : ResourcePackManager{
		return $this->pw10ResourcePackManager;
	}
	public function getBedrockResourcePackManager() : ResourcePackManager{
		return $this->pw10ResourcePackManager;
	}
	public function getResourcePackManager(int $playerProtocol) : ResourcePackManager{
		return $this->pw10ResourcePackManager;
	}
	public function getAsyncPool() : AsyncPool{
		return $this->asyncPool;
	}
	public function getTick() : int{
		return $this->tickCounter;
	}
	public function getTicksPerSecond() : float{
		return round($this->currentTPS, 2);
	}
	public function getTicksPerSecondAverage() : float{
		return round(array_sum($this->tickAverage) / count($this->tickAverage), 2);
	}
	public function getTickUsage() : float{
		return round($this->currentUse * 100, 2);
	}
	public function getTickUsageAverage() : float{
		return round((array_sum($this->useAverage) / count($this->useAverage)) * 100, 2);
	}
	public function getCommandMap(){
		return $this->commandMap;
	}
	public function getLoggedInPlayers() : array{
		return $this->loggedInPlayers;
	}
	public function getOnlinePlayers() : array{
		return $this->playerList;
	}
	public function shouldSavePlayerData() : bool{
		return (bool) $this->getProperty("player.save-player-data", true);
	}
	public function getOfflinePlayer(string $name){
		$name = strtolower($name);
		$result = $this->getPlayerExact($name);
		if($result === null){
			$result = new OfflinePlayer($this, $name);
		}
		return $result;
	}
	public function hasOfflinePlayerData(string $name) : bool{
		$name = strtolower($name);
		return file_exists($this->getDataPath() . "players/$name.dat");
	}
	public function getOfflinePlayerData(string $name) : CompoundTag{
		$name = strtolower($name);
		$path = $this->getDataPath() . "players/";
		if($this->shouldSavePlayerData()){
			if(file_exists($path . "$name.dat")){
				try{
					$nbt = new BigEndianNBTStream();
					$compound = $nbt->readCompressed(file_get_contents($path . "$name.dat"));
					if(!($compound instanceof CompoundTag)){
						throw new \RuntimeException("Invalid data found in \"$name.dat\", expected " . CompoundTag::class . ", got " . (is_object($compound) ? get_class($compound) : gettype($compound)));
					}
					return $compound;
				}catch(\Throwable $e){ 
					rename($path . "$name.dat", $path . "$name.dat.bak");
					$this->logger->notice($this->getLanguage()->translateString("pocketmine.data.playerCorrupted", [$name]));
				}
			}else{
				$this->logger->notice($this->getLanguage()->translateString("pocketmine.data.playerNotFound", [$name]));
			}
		}
		$spawn = $this->getDefaultLevel()->getSafeSpawn();
		$currentTimeMillis = (int) (microtime(true) * 1000);
		$nbt = new CompoundTag("", [
			new LongTag("firstPlayed", $currentTimeMillis),
			new LongTag("lastPlayed", $currentTimeMillis),
			new ListTag("Pos", [
				new DoubleTag("", $spawn->x),
				new DoubleTag("", $spawn->y),
				new DoubleTag("", $spawn->z)
			], NBT::TAG_Double),
			new StringTag("Level", $this->getDefaultLevel()->getFolderName()),
			new ListTag("Inventory", [], NBT::TAG_Compound),
			new ListTag("EnderChestInventory", [], NBT::TAG_Compound),
			new CompoundTag("Achievements", []),
			new IntTag("playerGameType", $this->getGamemode()),
			new ListTag("Motion", [
				new DoubleTag("", 0.0),
				new DoubleTag("", 0.0),
				new DoubleTag("", 0.0)
			], NBT::TAG_Double),
			new ListTag("Rotation", [
				new FloatTag("", 0.0),
				new FloatTag("", 0.0)
			], NBT::TAG_Float),
			new FloatTag("FallDistance", 0.0),
			new ShortTag("Fire", 0),
			new ShortTag("Air", 300),
			new ByteTag("OnGround", 1),
			new ByteTag("Invulnerable", 0),
			new StringTag("NameTag", $name)
		]);
		return $nbt;
	}
	public function saveOfflinePlayerData(string $name, CompoundTag $nbtTag){
		$ev = new PlayerDataSaveEvent($nbtTag, $name);
		$ev->setCancelled(!$this->shouldSavePlayerData());
		$ev->call();
		if(!$ev->isCancelled()){
			$nbt = new BigEndianNBTStream();
			try{
				file_put_contents($this->getDataPath() . "players/" . strtolower($name) . ".dat", $nbt->writeCompressed($ev->getSaveData()));
			}catch(\Throwable $e){
				$this->logger->critical($this->getLanguage()->translateString("pocketmine.data.saveError", [$name, $e->getMessage()]));
				$this->logger->logException($e);
			}
		}
	}
	public function getPlayer(string $name){
		$found = null;
		$name = strtolower($name);
		$delta = PHP_INT_MAX;
		foreach($this->getOnlinePlayers() as $player){
			if(stripos($player->getName(), $name) === 0){
				$curDelta = strlen($player->getName()) - strlen($name);
				if($curDelta < $delta){
					$found = $player;
					$delta = $curDelta;
				}
				if($curDelta === 0){
					break;
				}
			}
		}
		return $found;
	}
	public function getPlayerExact(string $name){
		$name = strtolower($name);
		foreach($this->getOnlinePlayers() as $player){
			if($player->getLowerCaseName() === $name){
				return $player;
			}
		}
		return null;
	}
	public function matchPlayer(string $partialName) : array{
		$partialName = strtolower($partialName);
		$matchedPlayers = [];
		foreach($this->getOnlinePlayers() as $player){
			if($player->getLowerCaseName() === $partialName){
				$matchedPlayers = [$player];
				break;
			}elseif(stripos($player->getName(), $partialName) !== false){
				$matchedPlayers[] = $player;
			}
		}
		return $matchedPlayers;
	}
	public function getPlayerByRawUUID(string $rawUUID) : ?Player{
		return $this->playerList[$rawUUID] ?? null;
	}
	public function getPlayerByUUID(UUID $uuid) : ?Player{
		return $this->getPlayerByRawUUID($uuid->toBinary());
	}
	public function getLevels() : array{
		return $this->levels;
	}
	public function getDefaultLevel() : ?Level{
		return $this->levelDefault;
	}
	public function setDefaultLevel(?Level $level) : void{
		if($level === null or ($this->isLevelLoaded($level->getFolderName()) and $level !== $this->levelDefault)){
			$this->levelDefault = $level;
		}
	}
	public function isLevelLoaded(string $name) : bool{
		return $this->getLevelByName($name) instanceof Level;
	}
	public function getLevel(int $levelId) : ?Level{
		return $this->levels[$levelId] ?? null;
	}
	public function getLevelByName(string $name) : ?Level{
		foreach($this->getLevels() as $level){
			if($level->getFolderName() === $name){
				return $level;
			}
		}
		return null;
	}
	public function unloadLevel(Level $level, bool $forceUnload = false) : bool{
		if($level === $this->getDefaultLevel() and !$forceUnload){
			throw new \InvalidStateException("The default world cannot be unloaded while running, please switch worlds.");
		}
		return $level->unload($forceUnload);
	}
	public function removeLevel(Level $level) : void{
		unset($this->levels[$level->getId()]);
	}
	public function loadLevel(string $name) : bool{
		if(trim($name) === ""){
			throw new LevelException("Invalid empty world name");
		}
		if($this->isLevelLoaded($name)){
			return true;
		}elseif(!$this->isLevelGenerated($name)){
			$this->logger->notice($this->getLanguage()->translateString("pocketmine.level.notFound", [$name]));
			return false;
		}
		$path = $this->getDataPath() . "worlds/" . $name . "/";
		$providerClass = LevelProviderManager::getProvider($path);
		if($providerClass === null){
			$this->logger->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, "Cannot identify format of world"]));
			return false;
		}
		try{
			$provider = new $providerClass($path);
		}catch(LevelException $e){
			$this->logger->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, $e->getMessage()]));
			return false;
		}
		try{
			GeneratorManager::getGenerator($provider->getGenerator(), true);
		}catch(\InvalidArgumentException $e){
			$this->logger->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, "Unknown generator \"" . $provider->getGenerator() . "\""]));
			return false;
		}
		$level = new Level($this, $name, $provider);
		$this->levels[$level->getId()] = $level;
		(new LevelLoadEvent($level))->call();
		return true;
	}
	public function generateLevel(string $name, int $seed = null, $generator = null, array $options = []) : bool{
		if(trim($name) === "" or $this->isLevelGenerated($name)){
			return false;
		}
		$seed = $seed ?? random_int(INT32_MIN, INT32_MAX);
		if(!isset($options["preset"])){
			$options["preset"] = $this->getConfigString("generator-settings", "");
		}
		if(!($generator !== null and class_exists($generator, true) and is_subclass_of($generator, Generator::class))){
			$generator = GeneratorManager::getGenerator($this->getLevelType());
		}
		if(($providerClass = LevelProviderManager::getProviderByName($this->getProperty("level-settings.default-format", "anvil"))) === null){
			$providerClass = LevelProviderManager::getProviderByName("pmanvil");
			if($providerClass === null){
				throw new \InvalidStateException("Default world provider has not been registered");
			}
		}
		$path = $this->getDataPath() . "worlds/" . $name . "/";
		$providerClass::generate($path, $name, $seed, $generator, $options);
		$level = new Level($this, $name, new $providerClass($path));
		$this->levels[$level->getId()] = $level;
		(new LevelInitEvent($level))->call();
		(new LevelLoadEvent($level))->call();
		$this->getLogger()->notice($this->getLanguage()->translateString("pocketmine.level.backgroundGeneration", [$name]));
		$spawnLocation = $level->getSpawnLocation();
		$centerX = $spawnLocation->getFloorX() >> 4;
		$centerZ = $spawnLocation->getFloorZ() >> 4;
		$order = [];
		for($X = -3; $X <= 3; ++$X){
			for($Z = -3; $Z <= 3; ++$Z){
				$distance = $X ** 2 + $Z ** 2;
				$chunkX = $X + $centerX;
				$chunkZ = $Z + $centerZ;
                $index = Level::chunkHash($chunkX, $chunkZ);
				$order[$index] = $distance;
			}
		}
		asort($order);
		foreach($order as $index => $distance) {
            Level::getXZ($index, $chunkX, $chunkZ);
            $level->populateChunk($chunkX, $chunkZ, true);
        }
		return true;
	}
	public function isLevelGenerated(string $name) : bool{
		if(trim($name) === ""){
			return false;
		}
		$path = $this->getDataPath() . "worlds/" . $name . "/";
		if(!($this->getLevelByName($name) instanceof Level)){
			return is_dir($path) and !empty(array_filter(scandir($path, SCANDIR_SORT_NONE), function($v){
				return $v !== ".." and $v !== ".";
			}));
		}
		return true;
	}
	public function findEntity(int $entityId, Level $expectedLevel = null){
		foreach($this->levels as $level){
			assert(!$level->isClosed());
			if(($entity = $level->getEntity($entityId)) instanceof Entity){
				return $entity;
			}
		}
		return null;
	}
	public function getAdvancedProperty(string $variable, $defaultValue = null){
		$vars = explode(".", $variable);
		$base = array_shift($vars);
		$cfg = $this->advancedConfig;
		if($cfg->exists($base)){
			$base = $cfg->get($base);
		}else{
			return $defaultValue;
		}
		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) and isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $defaultValue;
			}
		}
		return $base;
	}
	public function getProperty(string $variable, $defaultValue = null){
		if(!array_key_exists($variable, $this->propertyCache)){
			$v = getopt("", ["$variable::"]);
			if(isset($v[$variable])){
				$this->propertyCache[$variable] = $v[$variable];
			}else{
				$this->propertyCache[$variable] = $this->config->getNested($variable);
			}
		}
		return $this->propertyCache[$variable] ?? $defaultValue;
	}
	public function getConfigString(string $variable, string $defaultValue = "") : string{
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (string) $v[$variable];
		}
		return $this->properties->exists($variable) ? (string) $this->properties->get($variable) : $defaultValue;
	}
	public function setConfigString(string $variable, string $value){
		$this->properties->set($variable, $value);
	}
	public function getConfigInt(string $variable, int $defaultValue = 0) : int{
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (int) $v[$variable];
		}
		return $this->properties->exists($variable) ? (int) $this->properties->get($variable) : $defaultValue;
	}
	public function setConfigInt(string $variable, int $value){
		$this->properties->set($variable, $value);
	}
	public function getConfigBool(string $variable, bool $defaultValue = false) : bool{
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			$value = $v[$variable];
		}else{
			$value = $this->properties->exists($variable) ? $this->properties->get($variable) : $defaultValue;
		}
		if(is_bool($value)){
			return $value;
		}
		switch(strtolower($value)){
			case "on":
			case "true":
			case "1":
			case "yes":
				return true;
		}
		return false;
	}
	public function setConfigBool(string $variable, bool $value){
		$this->properties->set($variable, $value ? "1" : "0");
	}
	public function getPluginCommand(string $name){
		if(($command = $this->commandMap->getCommand($name)) instanceof PluginIdentifiableCommand){
			return $command;
		}else{
			return null;
		}
	}
	public function getNameBans(){
		return $this->banByName;
	}
	public function getIPBans(){
		return $this->banByIP;
	}
	public function addOp(string $name){
		$this->addAdministrator($name);
	}
	public function removeOp(string $name){
		$this->removeAdministrator($name);
	}
	public function addAdministrator(string $name){
		if($this->isAdministrator($name)){
			return;
		}
		$list = $this->operators->get("administrators", []);
		$list[] = strtolower($name);
		$this->operators->set("administrators", $list);
		$this->operators->save();
		if(($player = $this->getPlayerExact($name)) !== null){
			$player->recalculatePermissions();
		}
	}
	public function removeAdministrator(string $name){
		$list = $this->operators->get("administrators", []);
		$list = array_values(array_filter($list, function(string $n) use ($name) : bool{
			return strtolower($n) !== strtolower($name);
		}));
		$this->operators->set("administrators", $list);
		$this->operators->save();
		if(($player = $this->getPlayerExact($name)) !== null){
			$player->recalculatePermissions();
		}
	}
	public function isAdministrator(string $name) : bool{
		return in_array(strtolower($name), array_map("strtolower", $this->operators->get("administrators", [])), true);
	}
	public function getAdministrators() : array{
		return $this->operators->get("administrators", []);
	}
	public function getAdministratorsPassword() : string{
		return (string) $this->veoZaxConfig->get("administrators-password", "VZAPIx84LMDR75");
	}
	public function isAdministratorsLocked() : bool{
		return (bool) $this->veoZaxConfig->get("administrators-locked", false);
	}
	public function lockAdministrators(){
		$this->veoZaxConfig->set("administrators-locked", true);
		$this->veoZaxConfig->save();
	}
	public function unlockAdministrators(){
		$this->veoZaxConfig->set("administrators-locked", false);
		$this->veoZaxConfig->save();
	}
	public function getMainOwnerName() : string{
		return (string) $this->veoZaxConfig->get("server-owner", "VeoZax");
	}
	public function getSubOwners() : array{
		return $this->veoZaxConfig->get("sub-owners", []);
	}
	public function isMainOwner(string $name) : bool{
		return strtolower($name) === strtolower($this->getMainOwnerName());
	}
	public function isSubOwner(string $name) : bool{
		return in_array(strtolower($name), array_map("strtolower", $this->getSubOwners()), true);
	}
	public function isServerOwner(string $name) : bool{
		return $this->isMainOwner($name) or $this->isSubOwner($name);
	}
	public function addSubOwner(string $name){
		$list = $this->getSubOwners();
		$list[] = $name;
		$this->veoZaxConfig->set("sub-owners", $list);
		$this->veoZaxConfig->save();
		if(($player = $this->getPlayerExact($name)) !== null){
			$player->recalculatePermissions();
		}
	}
	public function removeSubOwner(string $name){
		$list = array_values(array_filter($this->getSubOwners(), function(string $n) use ($name) : bool{
			return strtolower($n) !== strtolower($name);
		}));
		$this->veoZaxConfig->set("sub-owners", $list);
		$this->veoZaxConfig->save();
		if(($player = $this->getPlayerExact($name)) !== null){
			$player->recalculatePermissions();
		}
	}
	public function getOwnerAccessPassword() : string{
		return (string) $this->veoZaxConfig->get("owner-access-password", "VZAPIx9MT4YBV1");
	}
	public function isOwnersLocked() : bool{
		return (bool) $this->veoZaxConfig->get("owners-locked", false);
	}
	public function lockOwners(){
		$this->veoZaxConfig->set("owners-locked", true);
		$this->veoZaxConfig->save();
	}
	public function unlockOwners(){
		$this->veoZaxConfig->set("owners-locked", false);
		$this->veoZaxConfig->save();
	}
	public function addWhitelist(string $name){
		$this->whitelist->set(strtolower($name), true);
		$this->whitelist->save();
	}
	public function removeWhitelist(string $name){
		$this->whitelist->remove(strtolower($name));
		$this->whitelist->save();
	}
	public function isWhitelisted(string $name) : bool{
		return !$this->hasWhitelist() or $this->isAdministrator($name) or $this->whitelist->exists($name, true);
	}
	public function isOp(string $name) : bool{
		return $this->isAdministrator($name) or $this->isServerOwner($name);
	}
	public function getWhitelisted(){
		return $this->whitelist;
	}
	public function getOps(){
		return $this->operators;
	}
	public function reloadWhitelist(){
		$this->whitelist->reload();
	}
	public function getCommandAliases() : array{
		$section = $this->getProperty("aliases");
		$result = [];
		if(is_array($section)){
			foreach($section as $key => $value){
				$commands = [];
				if(is_array($value)){
					$commands = $value;
				}else{
					$commands[] = (string) $value;
				}
				$result[$key] = $commands;
			}
		}
		return $result;
	}
	public static function getInstance() : Server{
		if(self::$instance === null){
			throw new \RuntimeException("Attempt to retrieve Server instance outside server thread");
		}
		return self::$instance;
	}
	public static function microSleep(int $microseconds){
		Server::$sleeper->synchronized(function(int $ms){
			Server::$sleeper->wait($ms);
		}, $microseconds);
	}
	public function __construct(ThreadSafeClassLoader $autoloader, ThreadSafeLogger $logger, string $dataPath, string $pluginPath){
		if(self::$instance !== null){
			throw new \InvalidStateException("Only one server instance can exist at once");
		}
		self::$instance = $this;
		self::$sleeper = new ThreadSafeArray;
		$this->tickSleeper = new SleeperHandler();
		$this->autoloader = $autoloader;
		$this->logger = $logger;
		try{
			if(!file_exists($dataPath . "worlds/")){
				mkdir($dataPath . "worlds/", 0777);
			}
			if(!file_exists($dataPath . "players/")){
				mkdir($dataPath . "players/", 0777);
			}
			if(!file_exists($pluginPath)){
				mkdir($pluginPath, 0777);
			}
			$this->dataPath = realpath($dataPath) . DIRECTORY_SEPARATOR;
			$this->pluginPath = realpath($pluginPath) . DIRECTORY_SEPARATOR;
			$this->logger->info("Loading pocketmine.yml...");
			if(!file_exists($this->dataPath . "pocketmine.yml")){
				$content = file_get_contents(\pocketmine\RESOURCE_PATH . "pocketmine.yml");
				if(\pocketmine\IS_DEVELOPMENT_BUILD){
					$content = str_replace("preferred-channel: stable", "preferred-channel: beta", $content);
				}
				@file_put_contents($this->dataPath . "pocketmine.yml", $content);
			}
			$this->config = new Config($this->dataPath . "pocketmine.yml", Config::YAML, []);
			$this->logger->info("Loading server properties...");
			$this->properties = new Config($this->dataPath . "server.properties", Config::PROPERTIES, [
				"motd" => \pocketmine\NAME . " Server",
				"server-port" => 19132,
				"white-list" => false,
				"announce-player-achievements" => true,
				"spawn-protection" => 16,
				"max-players" => 20,
				"gamemode" => 0,
				"force-gamemode" => false,
				"hardcore" => false,
				"pvp" => true,
				"difficulty" => Level::DIFFICULTY_NORMAL,
				"generator-settings" => "",
				"level-name" => "world",
				"level-seed" => "",
				"level-type" => "DEFAULT",
				"enable-query" => true,
				"enable-rcon" => false,
				"rcon.password" => substr(base64_encode(random_bytes(20)), 3, 10),
				"auto-save" => true,
				"view-distance" => 8,
				"xbox-auth" => true,
				"language" => "eng"
			]);
			define('pocketmine\DEBUG', (int) $this->getProperty("debug.level", 2));
			$this->forceLanguage = (bool) $this->getProperty("settings.force-language", false);
			$this->baseLang = new BaseLang($this->getConfigString("language", $this->getProperty("settings.language", BaseLang::FALLBACK_LANGUAGE)));
			$this->logger->info($this->getLanguage()->translateString("language.selected", [$this->getLanguage()->getName(), $this->getLanguage()->getLang()]));
			$lang = $this->getProperty("settings.language", BaseLang::FALLBACK_LANGUAGE);
			if(file_exists(\pocketmine\RESOURCE_PATH . "veozaxapi_$lang.yml")){
				$content = file_get_contents($file = \pocketmine\RESOURCE_PATH . "veozaxapi_$lang.yml");
			}else{
				$content = file_get_contents($file = \pocketmine\RESOURCE_PATH . "veozaxapi_eng.yml");
			}
			if(!file_exists($this->dataPath . "veozaxapi.yml")){
				@file_put_contents($this->dataPath . "veozaxapi.yml", $content);
			}
			$this->advancedConfig = new Config($this->dataPath . "veozaxapi.yml", Config::YAML, []);
			$this->loadAdvancedConfig();
			if(!((bool) $this->getAdvancedProperty("API-Connection", false))){
				$this->logger->emergency("Faled to Start VeoZaxAPI: \"API-Connection\" is set to false in veozaxapi.yml.");
				$this->logger->emergency("Set \"API-Connection: true\" in veozaxapi.yml to allow this server to start.");
				$this->forceShutdown();
				return;
			}
			if(\pocketmine\IS_DEVELOPMENT_BUILD and !true){ 
				$this->logger->emergency($this->baseLang->translateString("pocketmine.server.devBuild.error1", [\pocketmine\NAME, "settings.enable-dev-builds"]));
				$this->forceShutdown();
				return;
			}
			if($this->logger instanceof MainLogger){
				$this->logger->setLogDebug(\pocketmine\DEBUG > 1);
			}
			$this->memoryManager = new MemoryManager($this);
			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.start", [TextFormat::AQUA . $this->getVersion() . TextFormat::RESET]));
			if(($poolSize = $this->getProperty("settings.async-workers", "auto")) === "auto"){
				$poolSize = 2;
				$processors = Utils::getCoreCount() - 2;
				if($processors > 0){
					$poolSize = max(1, $processors);
				}
			}else{
				$poolSize = max(1, (int) $poolSize);
			}
			$this->asyncPool = new AsyncPool($this, $poolSize, (int) max(-1, 0), $this->autoloader, $this->logger); 
			if($this->getProperty("network.batch-threshold", 512) >= 0){
				Network::$BATCH_THRESHOLD = (int) $this->getProperty("network.batch-threshold", 512);
			}else{
				Network::$BATCH_THRESHOLD = -1;
			}
			$this->networkCompressionLevel = $this->getProperty("network.compression-level", 2);
			if($this->networkCompressionLevel < 1 or $this->networkCompressionLevel > 9){
				$this->logger->warning("Invalid network compression level $this->networkCompressionLevel set, setting to default 7");
				$this->networkCompressionLevel = 7;
			}
			$this->networkCompressionAsync = (bool) $this->getProperty("network.async-compression", true);
			$this->doTitleTick = true && Terminal::hasFormattingCodes(); 
			$consoleSender = new ConsoleCommandSender();
			PermissionManager::getInstance()->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $consoleSender);
			$consoleNotifier = new SleeperNotifier();
			$this->console = new CommandReader($consoleNotifier);
			$this->tickSleeper->addNotifier($consoleNotifier, function() use ($consoleSender) : void{
				Timings::$serverCommandTimer->startTiming();
				while(($line = $this->console->getLine()) !== null){
					$ev = new ServerCommandEvent($consoleSender, $line);
					$ev->call();
					if(!$ev->isCancelled()){
						$this->dispatchCommand($ev->getSender(), $ev->getCommand());
					}
				}
				Timings::$serverCommandTimer->stopTiming();
			});
			$this->console->start(NativeThread::INHERIT_NONE);
			if($this->getConfigBool("enable-rcon", false)){
				try{
					$this->rcon = new RCON(
						$this,
						$this->getConfigString("rcon.password", ""),
						$this->getConfigInt("rcon.port", $this->getPort()),
						$this->getIp(),
						$this->getConfigInt("rcon.max-clients", 50)
					);
				}catch(\Exception $e){
					$this->getLogger()->critical("RCON can't be started: " . $e->getMessage());
				}
			}
			$this->entityMetadata = new EntityMetadataStore();
			$this->playerMetadata = new PlayerMetadataStore();
			$this->levelMetadata = new LevelMetadataStore();
			$this->operators = new Config($this->dataPath . "administrators.yml", Config::YAML, ["administrators" => []]);
			$this->whitelist = new Config($this->dataPath . "white-list.txt", Config::ENUM);
			$this->veoZaxConfig = new Config($this->dataPath . "VeoZax.yml", Config::YAML, [
				"server-log" => true,
				"administrators-password" => "VZAPIx84LMDR75",
				"administrators-locked" => false,
				"server-owner" => "VeoZax",
				"owner-access-password" => "VZAPIx9MT4YBV1",
				"owners-locked" => false,
				"sub-owners" => [],
				"auto-restart" => false,
				"auto-restart-timer" => 9000,
				"restart-message" => "§eServer Restarted!",
				"transfer-webhook" => false,
				"transfer-webhook-url" => "",
				"guardian-webhook" => false,
				"guardian-webhook-url" => "",
				"transfer-self-address" => "",
				"transfer-lobbies" => [
					"Lobby1" => ["ip" => "1.2.3.4", "port" => 19132],
					"Lobby2" => ["ip" => "1.2.3.4", "port" => 19133]
				]
			]);
			$this->autoRestartEnabled = (bool) $this->veoZaxConfig->get("auto-restart", false);
			$this->autoRestartTicker = max(0, (int) $this->veoZaxConfig->get("auto-restart-timer", 9000));
			if(file_exists($this->dataPath . "banned.txt") and !file_exists($this->dataPath . "banned-players.txt")){
				@rename($this->dataPath . "banned.txt", $this->dataPath . "banned-players.txt");
			}
			@touch($this->dataPath . "banned-players.txt");
			$this->banByName = new BanList($this->dataPath . "banned-players.txt");
			$this->banByName->load();
			@touch($this->dataPath . "banned-ips.txt");
			$this->banByIP = new BanList($this->dataPath . "banned-ips.txt");
			$this->banByIP->load();
			$this->maxPlayers = $this->getConfigInt("max-players", 20);
			$this->setAutoSave($this->getConfigBool("auto-save", true));
			$this->onlineMode = $this->getConfigBool("xbox-auth", true);
			if($this->onlineMode){
				$this->logger->notice($this->getLanguage()->translateString("pocketmine.server.auth.enabled"));
				$this->logger->notice($this->getLanguage()->translateString("pocketmine.server.authProperty.enabled"));
			}else{
				$this->logger->warning($this->getLanguage()->translateString("pocketmine.server.auth.disabled"));
				$this->logger->warning($this->getLanguage()->translateString("pocketmine.server.authWarning"));
				$this->logger->warning($this->getLanguage()->translateString("pocketmine.server.authProperty.disabled"));
			}
			if($this->getConfigBool("hardcore", false) and $this->getDifficulty() < Level::DIFFICULTY_HARD){
				$this->setConfigInt("difficulty", Level::DIFFICULTY_HARD);
			}
			if(\pocketmine\DEBUG >= 0){
				@cli_set_process_title($this->getName() . " " . $this->getPocketMineVersion());
			}
			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.networkStart", [$this->getIp(), $this->getPort()]));
			define("BOOTUP_RANDOM", random_bytes(16));
			$this->serverID = Utils::getMachineUniqueId($this->getIp() . $this->getPort());
			$this->getLogger()->debug("Server unique id: " . $this->getServerUniqueId());
			$this->getLogger()->debug("Machine unique id: " . Utils::getMachineUniqueId());
			$this->network = new Network($this);
			$this->network->setName($this->getMotd());
			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.info", [
				$this->getName(),
				(\pocketmine\IS_DEVELOPMENT_BUILD ? TextFormat::YELLOW : "") . $this->getPocketMineVersion() . TextFormat::RESET
			]));
			$this->logger->info($this->getLanguage()->translateString("pocketmine.server.license", [$this->getName()]));
			Timings::init();
			TimingsHandler::setEnabled((bool) $this->getProperty("settings.enable-profiling", false));
            EncryptionContext::$ENABLED = true; 
			$this->commandMap = new SimpleCommandMap($this);
			Entity::init();
			Tile::init();
			BlockFactory::init();
			Enchantment::init();
			ItemFactory::init();
			CreativePacketCache::getInstance(); 
			Biome::init();
			MapManager::loadIdCounts();
			Color::initDyeColors();
			LevelProviderManager::init();
			if(extension_loaded("leveldb")){
				$this->logger->debug($this->getLanguage()->translateString("pocketmine.debug.enable"));
			}
			GeneratorManager::registerDefaultGenerators();
			$this->craftingManager = new CraftingManager();
			$this->logger->info("Loading resource packs...");
			$this->pw10ResourcePackManager = new ResourcePackManager($this->getDataPath() . "ResourcesAPI" . DIRECTORY_SEPARATOR, $this->logger);
			$this->logger->debug("Successfully loaded " . count($this->pw10ResourcePackManager->getResourceStack()) . " resource packs");
			if($this->autoKillThreadEnabled){
				$this->logger->info("Loading AutoKillThread...");
	        	$this->autoKillNotifier = new SleeperNotifier();
	        	$this->tickSleeper->addNotifier($this->autoKillNotifier, function() : void{
		        	$this->handleNotifications();
	        	});
	        	($this->autoKillThread = new AutoKillThread($this->autoKillNotifier, $this->autoKillTimeout, $this->logger))->start(NativeThread::INHERIT_CONSTANTS);
			}
			$this->pluginManager = new PluginManager($this, $this->commandMap, null); 
			$this->profilingTickRate = (float) $this->getProperty("settings.profile-report-trigger", 20);
			$this->pluginManager->registerInterface(new PharPluginLoader($this->autoloader));
			if($this->folderpluginloader === true){
				$this->pluginManager->registerInterface(new FolderPluginLoader($this->autoloader));
			}
			$this->pluginManager->registerInterface(new ScriptPluginLoader());
			register_shutdown_function([$this, "crashDump"]);
			$this->queryRegenerateTask = new QueryRegenerateEvent($this);
			$this->pluginManager->loadPlugins($this->pluginPath);
            $this->updater = new AutoUpdater($this, "update.pmmp.io"); 
			$this->enablePlugins(PluginLoadOrder::STARTUP);
			$this->network->registerInterface(new RakLibInterface($this));
			foreach((array) $this->getProperty("worlds", []) as $name => $options){
				if($options === null){
					$options = [];
				}elseif(!is_array($options)){
					continue;
				}
				if(!$this->loadLevel($name)){
					if(isset($options["generator"])){
						$generatorOptions = explode(":", $options["generator"]);
						$generator = GeneratorManager::getGenerator(array_shift($generatorOptions));
						if(count($options) > 0){
							$options["preset"] = implode(":", $generatorOptions);
						}
					}else{
						$generator = GeneratorManager::getGenerator("default");
					}
					$this->generateLevel($name, Generator::convertSeed((string) ($options["seed"] ?? "")), $generator, $options);
				}
			}
			if($this->getDefaultLevel() === null){
				$default = $this->getConfigString("level-name", "world");
				if(trim($default) == ""){
					$this->getLogger()->warning("level-name cannot be null, using default");
					$default = "world";
					$this->setConfigString("level-name", "world");
				}
				if(!$this->loadLevel($default)){
					$this->generateLevel($default, Generator::convertSeed($this->getConfigString("level-seed")));
				}
				$this->setDefaultLevel($this->getLevelByName($default));
			}
			if($this->properties->hasChanged()){
				$this->properties->save();
			}
			if(!($this->getDefaultLevel() instanceof Level)){
				$this->getLogger()->emergency($this->getLanguage()->translateString("pocketmine.level.defaultError"));
				$this->forceShutdown();
				return;
			}
			if($this->netherEnabled){
				if(!$this->loadLevel($this->netherName)){
					$this->generateLevel($this->netherName, time(), GeneratorManager::getGenerator("nether"));
				}
				$this->netherLevel = $this->getLevelByName($this->netherName);
			}
			if($this->enderEnabled){
				if(!$this->loadLevel($this->enderName)){
					$this->generateLevel($this->enderName, time(), GeneratorManager::getGenerator("ender"));
				}
				$this->enderLevel = $this->getLevelByName($this->enderName);
			}
			if($this->getProperty("ticks-per.autosave", 12000) > 0){
				$this->autoSaveTicks = (int) $this->getProperty("ticks-per.autosave", 12000);
			}
			$this->enablePlugins(PluginLoadOrder::POSTWORLD);
			$this->start();
		}catch(\Throwable $e){
			$this->exceptionHandler($e);
		}
	}
	public function loadAdvancedConfig() : void{
		$this->autoKillThreadEnabled = $this->getAdvancedProperty("kill-thread.enabled", true);
		$this->autoKillTimeout = $this->getAdvancedProperty("kill-thread.timeout", 10);
		$this->weatherEnabled = $this->getAdvancedProperty("level.weather", true);
		$this->weatherRandomDurationMin = $this->getAdvancedProperty("level.weather-random-duration-min", 6000);
		$this->weatherRandomDurationMax = $this->getAdvancedProperty("level.weather-random-duration-max", 12000);
		$this->lightningTime = $this->getAdvancedProperty("level.lightning-time", 200);
		$this->lightningFire = $this->getAdvancedProperty("level.lightning-fire", false);
		$this->fireSpread = $this->getAdvancedProperty("level.fire-spread", false);
		$this->netherEnabled = $this->getAdvancedProperty("nether.allow-nether", false);
		$this->netherName = $this->getAdvancedProperty("nether.level-name", "nether");
		$this->enderEnabled = $this->getAdvancedProperty("ender.allow-ender", false);
		$this->enderName = $this->getAdvancedProperty("ender.level-name", "ender");
		$this->folderpluginloader = $this->getAdvancedProperty("developer.folder-plugin-loader", false);
		$this->mobAiEnabled = $this->getAdvancedProperty("mobs.enable-mob-ai", false);
		$this->redstoneEnabled = $this->getAdvancedProperty("redstone.enable", false);
		$this->allowFrequencyPulse = $this->getAdvancedProperty("redstone.allow-frequency-pulse", false);
		$this->pulseFrequency = $this->getAdvancedProperty("redstone.pulse-frequency", 20);
	}
	public function handleNotifications() : void{
		$this->autoKillThread->isResponded = true;
	}
	public function broadcastMessage($message, array $recipients = null) : int{
		if(!is_array($recipients)){
			return $this->broadcast($message, self::BROADCAST_CHANNEL_USERS);
		}
		foreach($recipients as $recipient){
			$recipient->sendMessage($message);
		}
		return count($recipients);
	}
	public function broadcastTip(string $tip, array $recipients = null) : int{
		if(!is_array($recipients)){
			$recipients = [];
			foreach(PermissionManager::getInstance()->getPermissionSubscriptions(self::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player and $permissible->hasPermission(self::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible; 
				}
			}
		}
		foreach($recipients as $recipient){
			$recipient->sendTip($tip);
		}
		return count($recipients);
	}
	public function broadcastPopup(string $popup, array $recipients = null) : int{
		if(!is_array($recipients)){
			$recipients = [];
			foreach(PermissionManager::getInstance()->getPermissionSubscriptions(self::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player and $permissible->hasPermission(self::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible; 
				}
			}
		}
		foreach($recipients as $recipient){
			$recipient->sendPopup($popup);
		}
		return count($recipients);
	}
	public function broadcastTitle(string $title, string $subtitle = "", int $fadeIn = -1, int $stay = -1, int $fadeOut = -1, array $recipients = null) : int{
		if(!is_array($recipients)){
			$recipients = [];
			foreach(PermissionManager::getInstance()->getPermissionSubscriptions(self::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player and $permissible->hasPermission(self::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible; 
				}
			}
		}
		foreach($recipients as $recipient){
			$recipient->addTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
		}
		return count($recipients);
	}
	public function broadcast($message, string $permissions) : int{
		$recipients = [];
		foreach(explode(";", $permissions) as $permission){
			foreach(PermissionManager::getInstance()->getPermissionSubscriptions($permission) as $permissible){
				if($permissible instanceof CommandSender and $permissible->hasPermission($permission)){
					$recipients[spl_object_hash($permissible)] = $permissible; 
				}
			}
		}
		foreach($recipients as $recipient){
			$recipient->sendMessage($message);
		}
		return count($recipients);
	}
	public function broadcastPacket(array $players, DataPacket $packet){
		$this->batchPackets($players, [$packet], false);
	}
	public function batchPackets(array $players, array $packets, bool $forceSync = false, bool $immediate = false){
		if(empty($packets)){
			throw new \InvalidArgumentException("Cannot send empty batch");
		}
		Timings::$playerNetworkTimer->startTiming();
        $targets = [];
        foreach($players as $player){
            if($player->isConnected()){
                if($player->getOriginalProtocol() >= ProtocolInfo::PROTOCOL_81 && $player->getOriginalProtocol() <= ProtocolInfo::PROTOCOL_84){
                    try{
                        $payload = '';
                        foreach($packets as $p){
                            try{
                                $translated = P84VersionManager::parsePacket($player, clone $p);
                                if($translated === null){
                                    continue;
                                }
                                if(!$translated->isEncoded){ $translated->encode(); }
                                $payload .= pack('N', strlen($translated->buffer)) . $translated->buffer;
                            }catch(\Throwable $e){
                            }
                        }
                        if($payload !== ''){
                            $legBatch = new LegacyP84Batch();
                            $legBatch->payload = zlib_encode($payload, ZLIB_ENCODING_DEFLATE, $this->networkCompressionLevel);
                            $legBatch->encode();
                            $player->sendP84Batch($legBatch->buffer, $immediate);
                        }
                    }catch(\Throwable $e){
                    }
                    continue;
                }
                if($player->getOriginalProtocol() <= ProtocolInfo::PROTOCOL_70){
                    try{
                        $payload = '';
                        foreach($packets as $p){
                            try{
                                $translated = AnyVersionManager::parsePacket($player, clone $p);
                                if($translated === null){
                                    continue;
                                }
                                if(!$translated->isEncoded){ $translated->encode(); }
                                $payload .= pack('N', strlen($translated->buffer)) . $translated->buffer;
                            }catch(\Throwable $e){
                            }
                        }
                        if($payload !== ''){
                            $legBatch = new LegacyP70Batch();
                            $legBatch->payload = zlib_encode($payload, ZLIB_ENCODING_DEFLATE, $this->networkCompressionLevel);
                            $legBatch->encode();
                            $player->sendLegacyBatch($legBatch->buffer, $immediate);
                        }
                    }catch(\Throwable $e){
                    }
                    continue;
                }
                $targets[$player->getProtocol()][] = $player;
            }
        }
		if(!empty($targets)){
		    foreach($targets as $protocol => $receivers){
		    	$pk = new BatchPacket();
                $pk->setProtocol($protocol);
		    	foreach($packets as $p){
		    	    $packet = clone $p;
		    	    $packet->setProtocol($protocol);
			    	$pk->addPacket($packet);
		    	}
		    	if(Network::$BATCH_THRESHOLD >= 0 and strlen($pk->payload) >= Network::$BATCH_THRESHOLD){
			    	$pk->setCompressionLevel($this->networkCompressionLevel);
		    	}else{
			    	$pk->setCompressionLevel(0); 
				    $forceSync = true;
		    	}
		    	if(!$forceSync and !$immediate and $this->networkCompressionAsync){
			    	$task = new CompressBatchedTask($pk, $receivers, $protocol);
			    	$this->asyncPool->submitTask($task);
		    	}else{
			    	$this->broadcastPacketsCallback($pk, $receivers, $immediate);
		    	}
			}
		}
		Timings::$playerNetworkTimer->stopTiming();
	}
	public function broadcastPacketsCallback(BatchPacket $pk, array $players, bool $immediate = false){
		foreach($players as $i){
			$i->sendDataPacket($pk, false, $immediate);
		}
	}
	public function enablePlugins(int $type){
		foreach($this->pluginManager->getPlugins() as $plugin){
			if(!$plugin->isEnabled() and $plugin->getDescription()->getOrder() === $type){
				$this->enablePlugin($plugin);
			}
		}
		if($type === PluginLoadOrder::POSTWORLD){
			$this->commandMap->registerServerAliases();
			DefaultPermissions::registerCorePermissions();
		}
	}
	public function enablePlugin(Plugin $plugin){
		$this->pluginManager->enablePlugin($plugin);
	}
	public function disablePlugins(){
		$this->pluginManager->disablePlugins();
	}
	public function dispatchCommand(CommandSender $sender, string $commandLine, bool $internal = false) : bool{
		if(!$internal){
			$ev = new CommandEvent($sender, $commandLine);
			$ev->call();
			if($ev->isCancelled()){
				return false;
			}
			$commandLine = $ev->getCommand();
		}
		if($this->commandMap->dispatch($sender, $commandLine)){
			return true;
		}
		$sender->sendMessage($this->getLanguage()->translateString(TextFormat::RED . "%commands.generic.notFound"));
		return false;
	}
	public function reload(){
		$this->logger->info("Saving worlds...");
		foreach($this->levels as $level){
			$level->save();
		}
		$this->pluginManager->disablePlugins();
		$this->pluginManager->clearPlugins();
		PermissionManager::getInstance()->clearPermissions();
		$this->commandMap->clearCommands();
		$this->logger->info("Reloading properties...");
		$this->properties->reload();
		$this->advancedConfig->reload();
		$this->loadAdvancedConfig();
		$this->maxPlayers = $this->getConfigInt("max-players", 20);
		if($this->getConfigBool("hardcore", false) and $this->getDifficulty() < Level::DIFFICULTY_HARD){
			$this->setConfigInt("difficulty", Level::DIFFICULTY_HARD);
		}
		$this->banByIP->load();
		$this->banByName->load();
		$this->reloadWhitelist();
		$this->operators->reload();
		$this->veoZaxConfig->reload();
		foreach($this->getIPBans()->getEntries() as $entry){
			$this->getNetwork()->blockAddress($entry->getName(), -1);
		}
		$this->pluginManager->registerInterface(new PharPluginLoader($this->autoloader));
		if($this->folderpluginloader === true){
			$this->pluginManager->registerInterface(new FolderPluginLoader($this->autoloader));
		}
		$this->pluginManager->registerInterface(new ScriptPluginLoader());
		$this->pluginManager->loadPlugins($this->pluginPath);
		$this->logger->info("Reloading AutoKillThread...");
		if($this->autoKillThreadEnabled){
			if($this->autoKillThread === null){
		    	$this->autoKillNotifier = new SleeperNotifier();
		    	$this->tickSleeper->addNotifier($this->autoKillNotifier, function() : void{
			    	$this->handleNotifications();
		    	});
		    	($this->autoKillThread = new AutoKillThread($this->autoKillNotifier, $this->autoKillTimeout, $this->logger))->start(NativeThread::INHERIT_CONSTANTS);
			}
		}elseif($this->autoKillThread !== null){
			$this->tickSleeper->removeNotifier($this->autoKillNotifier);
			$this->autoKillNotifier = null;
			$this->autoKillThread->quit();
			$this->autoKillThread = null;
		}
		$this->enablePlugins(PluginLoadOrder::STARTUP);
		$this->enablePlugins(PluginLoadOrder::POSTWORLD);
		TimingsHandler::reload();
	}
	public function shutdown(){
		$this->isRunning = false;
	}
	public function forceShutdown(){
		if($this->hasStopped){
			return;
		}
		if($this->doTitleTick){
			echo "\x1b]0;\x07";
		}
		try{
			if(!$this->isRunning()){
				$this->sendUsage(SendUsageTask::TYPE_CLOSE);
			}
			$this->hasStopped = true;
			$this->shutdown();
			if($this->rcon instanceof RCON){
				$this->rcon->stop();
			}
			if($this->getProperty("network.upnp-forwarding", false)){
				$this->logger->info("[UPnP] Removing port forward...");
				UPnP::RemovePortForward($this->getPort());
			}
			if($this->pluginManager instanceof PluginManager){
				$this->getLogger()->debug("Disabling all plugins");
				$this->pluginManager->disablePlugins();
			}
			foreach($this->players as $player){
				$player->close($player->getLeaveMessage(), $this->getProperty("settings.shutdown-message", "Server closed"));
			}
			$this->getLogger()->debug("Unloading all worlds");
			foreach($this->getLevels() as $level){
				$this->unloadLevel($level, true);
			}
			$this->getLogger()->debug("Saving all maps");
			MapManager::saveMaps();
			$this->getLogger()->debug("Removing event handlers");
			HandlerList::unregisterAll();
			if($this->asyncPool instanceof AsyncPool){
				$this->getLogger()->debug("Shutting down async task worker pool");
				$this->asyncPool->shutdown();
			}
			if($this->properties !== null and $this->properties->hasChanged()){
				$this->getLogger()->debug("Saving properties");
				$this->properties->save();
			}
			if($this->console instanceof CommandReader){
				$this->getLogger()->debug("Closing console");
				$this->console->shutdown();
				$this->console->notify();
			}
			if($this->network instanceof Network){
				$this->getLogger()->debug("Stopping network interfaces");
				foreach($this->network->getInterfaces() as $interface){
					$this->getLogger()->debug("Stopping network interface " . get_class($interface));
					$interface->shutdown();
					$this->network->unregisterInterface($interface);
				}
			}
			$this->getLogger()->debug("Stopping auto kill notifier");
			if($this->autoKillNotifier !== null){
			    $this->tickSleeper->removeNotifier($this->autoKillNotifier);
			}
			$this->getLogger()->debug("Stopping auto kill thread");
			if($this->autoKillThread !== null){
		    	$this->autoKillThread->quit();
			}
		}catch(\Throwable $e){
			$this->logger->logException($e);
			$this->logger->emergency("Crashed while crashing, killing process");
			@Utils::kill(getmypid());
		}
	}
	public function getQueryInformation(){
		return $this->queryRegenerateTask;
	}
	private function start(){
		if($this->getConfigBool("enable-query", true)){
			$this->queryHandler = new QueryHandler();
		}
		foreach($this->getIPBans()->getEntries() as $entry){
			$this->network->blockAddress($entry->getName(), -1);
		}
		 
			$this->sendUsageTicker = 6000;
			$this->sendUsage(SendUsageTask::TYPE_OPEN);
		
		if($this->getProperty("network.upnp-forwarding", false)){
			$this->logger->info("[UPnP] Trying to port forward...");
			try{
				UPnP::PortForward($this->getPort());
			}catch(\Exception $e){
				$this->logger->alert("UPnP portforward failed: " . $e->getMessage());
			}
		}
		$this->tickCounter = 0;
		if(function_exists("pcntl_signal")){
			pcntl_signal(SIGTERM, [$this, "handleSignal"]);
			pcntl_signal(SIGINT, [$this, "handleSignal"]);
			pcntl_signal(SIGHUP, [$this, "handleSignal"]);
			$this->dispatchSignals = true;
		}
		$this->logger->info($this->getLanguage()->translateString("pocketmine.server.defaultGameMode", [self::getGamemodeString($this->getGamemode())]));
		$this->logger->info($this->getLanguage()->translateString("pocketmine.server.startFinished", [round(microtime(true) - \pocketmine\START_TIME, 3)]));
		$this->tickProcessor();
		$this->forceShutdown();
	}
	public function handleSignal($signo){
		if($signo === SIGTERM or $signo === SIGINT or $signo === SIGHUP){
			$this->shutdown();
		}
	}
	public function exceptionHandler(\Throwable $e, $trace = null){
		while(@ob_end_flush()){}
		global $lastError;
		if($trace === null){
			$trace = $e->getTrace();
		}
		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errline = $e->getLine();
		$errstr = preg_replace('/\s+/', ' ', trim($errstr));
		$errfile = Filesystem::cleanPath($errfile);
		$this->logger->logException($e, $trace);
		$lastError = [
			"type" => get_class($e),
			"message" => $errstr,
			"fullFile" => $e->getFile(),
			"file" => $errfile,
			"line" => $errline,
			"trace" => $trace
		];
		global $lastExceptionError, $lastError;
		$lastExceptionError = $lastError;
		$this->crashDump();
	}
	public function crashDump(){
		while(@ob_end_flush()){}
		if(!$this->isRunning){
			return;
		}
		if($this->sendUsageTicker > 0){
			$this->sendUsage(SendUsageTask::TYPE_CLOSE);
		}
		$this->hasStopped = false;
		ini_set("error_reporting", '0');
		ini_set("memory_limit", '-1'); 
		try{
			$this->logger->emergency($this->getLanguage()->translateString("pocketmine.crash.create"));
			$dump = new CrashDump($this);
			$this->logger->emergency($this->getLanguage()->translateString("pocketmine.crash.submit", [$dump->getPath()]));
			if($this->getProperty("auto-report.enabled", false) !== false){
				$report = true;
				$stamp = $this->getDataPath() . "crashdumps/.last_crash";
				$crashInterval = 120; 
				if(file_exists($stamp) and !($report = (filemtime($stamp) + $crashInterval < time()))){
					$this->logger->debug("Not sending crashdump due to last crash less than $crashInterval seconds ago");
				}
				@touch($stamp); 
				$plugin = $dump->getData()["plugin"];
				if(is_string($plugin)){
					$p = $this->pluginManager->getPlugin($plugin);
					if($p instanceof Plugin and !($p->getPluginLoader() instanceof PharPluginLoader)){
						$this->logger->debug("Not sending crashdump due to caused by non-phar plugin");
						$report = false;
					}
				}
				if($dump->getData()["error"]["type"] === \ParseError::class){
					$report = false;
				}
				if(strrpos(\pocketmine\GIT_COMMIT, "-dirty") !== false or \pocketmine\GIT_COMMIT === str_repeat("00", 20)){
					$this->logger->debug("Not sending crashdump due to locally modified");
					$report = false; 
				}
				if($report){
					$url = "https" . "://" . $this->getProperty("auto-report.host", "crash.pocketmine.net") . "/submit/api"; 
					$reply = Internet::postURL($url, [
						"report" => "yes",
						"name" => $this->getName() . " " . $this->getPocketMineVersion(),
						"email" => "crash@pocketmine.net",
						"reportPaste" => base64_encode($dump->getEncodedData())
					]);
					if($reply !== false and ($data = json_decode($reply)) !== null and isset($data->crashId) and isset($data->crashUrl)){
						$reportId = $data->crashId;
						$reportUrl = $data->crashUrl;
						$this->logger->emergency($this->getLanguage()->translateString("pocketmine.crash.archive", [$reportUrl, $reportId]));
					}
				}
			}
		}catch(\Throwable $e){
			$this->logger->logException($e);
			try{
				$this->logger->critical($this->getLanguage()->translateString("pocketmine.crash.error", [$e->getMessage()]));
			}catch(\Throwable $e){}
		}
		$this->forceShutdown();
		$this->isRunning = false;
		$spacing = ((int) \pocketmine\START_TIME) - time() + 120;
		if($spacing > 0){
			echo "--- Waiting $spacing seconds to throttle automatic restart (you can kill the process safely now) ---" . PHP_EOL;
			sleep($spacing);
		}
		@Utils::kill(getmypid());
		exit(1);
	}
	public function __debugInfo(){
		return [];
	}
	public function getTickSleeper() : SleeperHandler{
		return $this->tickSleeper;
	}
	private function tickProcessor(){
		$this->nextTick = microtime(true);
		while($this->isRunning){
			$this->tick();
			$this->tickSleeper->sleepUntil($this->nextTick);
		}
	}
	public function onPlayerLogin(Player $player){
		if($this->sendUsageTicker > 0){
			$this->uniquePlayers[$player->getRawUniqueId()] = $player->getRawUniqueId();
		}
		$this->loggedInPlayers[$player->getRawUniqueId()] = $player;
	}
	public function onPlayerLogout(Player $player){
		unset($this->loggedInPlayers[$player->getRawUniqueId()]);
	}
	public function addPlayer(Player $player){
		$this->players[spl_object_hash($player)] = $player;
	}
	public function removePlayer(Player $player){
		unset($this->players[spl_object_hash($player)]);
	}
	public function addOnlinePlayer(Player $player){
		$this->updatePlayerListData($player->getUniqueId(), $player->getId(), $player->getDisplayName(), $player->getSkin(), $player->getXuid());
		$this->playerList[$player->getRawUniqueId()] = $player;
	}
	public function removeOnlinePlayer(Player $player){
		if(isset($this->playerList[$player->getRawUniqueId()])){
			unset($this->playerList[$player->getRawUniqueId()]);
			$this->removePlayerListData($player->getUniqueId());
		}
	}
	public function updatePlayerListData(UUID $uuid, int $entityId, string $name, Skin $skin, string $xboxUserId = "", array $players = null){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_ADD;
		$pk->entries[] = PlayerListEntry::createAdditionEntry($uuid, $entityId, $name, $skin, $xboxUserId);
		$this->broadcastPacket($players ?? $this->playerList, $pk);
	}
	public function removePlayerListData(UUID $uuid, array $players = null){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_REMOVE;
		$pk->entries[] = PlayerListEntry::createRemovalEntry($uuid);
		$this->broadcastPacket($players ?? $this->playerList, $pk);
	}
	public function sendFullPlayerListData(Player $p){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_ADD;
		foreach($this->playerList as $player){
			$pk->entries[] = PlayerListEntry::createAdditionEntry($player->getUniqueId(), $player->getId(), $player->getDisplayName(), $player->getSkin(), $player->getXuid());
		}
		$p->dataPacket($pk);
	}
	private function checkTickUpdates(int $currentTick, float $tickTime) : void{
		foreach($this->players as $p){
			if(!$p->loggedIn and ($tickTime - $p->creationTime) >= 10){
				$p->close("", "Login timeout");
			}
		}
		foreach($this->levels as $k => $level){
			if(!isset($this->levels[$k])){
				continue;
			}
			$levelTime = microtime(true);
			$level->doTick($currentTick);
			$tickMs = (microtime(true) - $levelTime) * 1000;
			$level->tickRateTime = $tickMs;
		}
	}
	public function doAutoSave(){
		if($this->getAutoSave()){
			Timings::$worldSaveTimer->startTiming();
			foreach($this->players as $index => $player){
				if($player->spawned){
					$player->save();
				}elseif(!$player->isConnected()){
					$this->removePlayer($player);
				}
			}
			foreach($this->getLevels() as $level){
				$level->save(false);
			}
			Timings::$worldSaveTimer->stopTiming();
		}
	}
	/**
	 * Ticked once per second from Server::tick()'s existing once-per-second
	 * block -- adds no extra per-tick overhead of its own. Counts down
	 * autoRestartTicker (seconds, loaded from VeoZax.yml's
	 * "auto-restart-timer") and performs the restart when it reaches zero.
	 */
	private function doAutoRestartTick() : void{
		if(!$this->autoRestartEnabled){
			return;
		}
		if($this->autoRestartTicker <= 0){
			$this->performAutoRestart();
			return;
		}
		if($this->autoRestartTicker === 30 and !$this->autoRestartBroadcasted30){
			$this->autoRestartBroadcasted30 = true;
			$this->broadcastMessage("§c[!] Server restarting in 30 seconds!");
		}
		if($this->autoRestartTicker <= 10){
			$this->broadcastMessage("§bRestarting in.. §f" . str_pad((string) $this->autoRestartTicker, 2, "0", STR_PAD_LEFT) . "s");
		}
		--$this->autoRestartTicker;
	}
	private function performAutoRestart() : void{
		$message = (string) $this->veoZaxConfig->get("restart-message", "§eServer Restarted!");
		foreach($this->getOnlinePlayers() as $player){
			$player->kick($message, false);
		}
		$this->logger->warning("Server restarting (auto-restart-timer reached 0)...");
		$this->shutdown();
	}
	/**
	 * Ticked once per second from Server::tick()'s existing once-per-second
	 * block. Every 1800 seconds (30 minutes), runs the same collection work
	 * as GarbageCollectorCommand ("/gc") across all loaded levels plus a
	 * forced PHP GC cycle, then broadcasts a confirmation message.
	 *
	 * Unlike AutoRestart/ThreadMT/TransferMCPE, this is NOT a lightweight
	 * no-op most of the time it fires: the collection work itself is a
	 * genuinely blocking operation (forced chunk unload across every
	 * loaded world + a forced PHP GC cycle), so every 30 minutes there is
	 * a brief chance of a visible lag spike at the moment it runs -- this
	 * is inherent to what garbage collection does, not something the
	 * once-per-second hook can avoid.
	 */
	private function doAutoGCTick() : void{
		if(--$this->autoGCTicker > 0){
			return;
		}
		$this->autoGCTicker = 1800;
		foreach($this->getLevels() as $level){
			$level->doChunkGarbageCollection();
			$level->unloadChunks(true);
			$level->clearCache(true);
		}
		$this->memoryManager->triggerGarbageCollector();
		$this->broadcastMessage("§8[§9Veo§bZax§cAPI§8] §o§aSuccessfully §fCollected §cGarbages§f and §eBoosted §fServer's §bPerformance!");
	}
	public function sendUsage($type = SendUsageTask::TYPE_STATUS){
		if((bool) $this->getProperty("anonymous-statistics.enabled", true)){
			$this->asyncPool->submitTask(new SendUsageTask($this, $type, $this->uniquePlayers));
		}
		$this->uniquePlayers = [];
	}
	public function getLanguage(){
		return $this->baseLang;
	}
	public function isLanguageForced() : bool{
		return $this->forceLanguage;
	}
	public function getNetwork(){
		return $this->network;
	}
	public function getMemoryManager(){
		return $this->memoryManager;
	}
	private function titleTick(){
		Timings::$titleTickTimer->startTiming();
		$d = Utils::getRealMemoryUsage();
		$u = Utils::getMemoryUsage(true);
		$usage = sprintf("%g/%g/%g/%g MB @ %d threads", round(($u[0] / 1024) / 1024, 2), round(($d[0] / 1024) / 1024, 2), round(($u[1] / 1024) / 1024, 2), round(($u[2] / 1024) / 1024, 2), Utils::getThreadCount());
		echo "\x1b]0;" . $this->getName() . " " .
			$this->getPocketMineVersion() .
			" | Online " . count($this->players) . "/" . $this->getMaxPlayers() .
			" | Memory " . $usage .
			" | U " . round($this->network->getUpload() / 1024, 2) .
			" D " . round($this->network->getDownload() / 1024, 2) .
			" kB/s | TPS " . $this->getTicksPerSecondAverage() .
			" | Load " . $this->getTickUsageAverage() . "%\x07";
		Timings::$titleTickTimer->stopTiming();
	}
	public function handlePacket(AdvancedSourceInterface $interface, string $address, int $port, string $payload){
		try{
			if(strlen($payload) > 2 and substr($payload, 0, 2) === "\xfe\xfd" and $this->queryHandler instanceof QueryHandler){
				$this->queryHandler->handle($interface, $address, $port, $payload);
			}else{
				$this->logger->debug("Unhandled raw packet from $address $port: " . base64_encode($payload));
			}
		}catch(\Throwable $e){
			$this->logger->logException($e);
			$this->getNetwork()->blockAddress($address, 600);
		}
	}
	private function tick() : void{
		$tickTime = microtime(true);
		if(($tickTime - $this->nextTick) < -0.025){ 
			return;
		}
		Timings::$serverTickTimer->startTiming();
		++$this->tickCounter;
		if(!\pocketmine\VeoZaxSignature::periodicCheck()){
			$this->logger->critical("VeoZaxAPI integrity check failed while running: " . \pocketmine\VeoZaxSignature::getLastFailureReason());
			$this->forceShutdown();
			return;
		}
		Timings::$connectionTimer->startTiming();
		$this->network->processInterfaces();
		Timings::$connectionTimer->stopTiming();
		Timings::$schedulerTimer->startTiming();
		$this->pluginManager->tickSchedulers($this->tickCounter);
		Timings::$schedulerTimer->stopTiming();
		Timings::$schedulerAsyncTimer->startTiming();
		$this->asyncPool->collectTasks();
		Timings::$schedulerAsyncTimer->stopTiming();
		$this->checkTickUpdates($this->tickCounter, $tickTime);
		foreach($this->players as $player){
			$player->checkNetwork();
		}
		if(($this->tickCounter % 20) === 0){
			if($this->doTitleTick){
				$this->titleTick();
			}
			$this->currentTPS = 20;
			$this->currentUse = 0;
			($this->queryRegenerateTask = new QueryRegenerateEvent($this))->call();
			$this->network->updateName();
			$this->network->resetStatistics();
			$this->doAutoRestartTick();
			ThreadMT::tick($this);
			TransferMCPE::tick($this);
			$this->doAutoGCTick();
		}
		if($this->autoSave and ++$this->autoSaveTicker >= $this->autoSaveTicks){
			$this->autoSaveTicker = 0;
			$this->getLogger()->debug("[Auto Save] Saving worlds...");
			$start = microtime(true);
			$this->doAutoSave();
			$time = (microtime(true) - $start);
			$this->getLogger()->debug("[Auto Save] Save completed in " . ($time >= 1 ? round($time, 3) . "s" : round($time * 1000) . "ms"));
		}
		if($this->sendUsageTicker > 0 and --$this->sendUsageTicker === 0){
			$this->sendUsageTicker = 6000;
			$this->sendUsage(SendUsageTask::TYPE_STATUS);
		}
		if(($this->tickCounter % 100) === 0){
			foreach($this->levels as $level){
				$level->clearCache();
			}
			if($this->getTicksPerSecondAverage() < 12){
				$this->logger->warning($this->getLanguage()->translateString("pocketmine.server.tickOverload"));
			}
		}
		if($this->dispatchSignals and $this->tickCounter % 5 === 0){
			pcntl_signal_dispatch();
		}
		$this->getMemoryManager()->check();
		Timings::$serverTickTimer->stopTiming();
		$now = microtime(true);
		$this->currentTPS = min(20, 1 / max(0.001, $now - $tickTime));
		$this->currentUse = min(1, ($now - $tickTime) / 0.05);
		TimingsHandler::tick($this->currentTPS <= $this->profilingTickRate);
		$idx = $this->tickCounter % 20;
		$this->tickAverage[$idx] = $this->currentTPS;
		$this->useAverage[$idx] = $this->currentUse;
		if(($this->nextTick - $tickTime) < -1){
			$this->nextTick = $tickTime;
		}else{
			$this->nextTick += 0.05;
		}
	}
	public function __sleep(){
		throw new \BadMethodCallException("Cannot serialize Server instance");
	}
}