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
namespace pocketmine\event\server;
use pocketmine\Player;use pocketmine\plugin\Plugin;use pocketmine\Server;use function chr;use function count;use function pack;use function str_replace;use function substr;
class QueryRegenerateEvent extends ServerEvent{
	public const GAME_ID = "MINECRAFTPE";
	private $serverName;
	private $listPlugins;
	private $plugins;
	private $players;
	private $gametype;
	private $version;
	private $server_engine;
	private $map;
	private $numPlayers;
	private $maxPlayers;
	private $whitelist;
	private $port;
	private $ip;
	private $extraData = [];
	private $longQueryCache = null;
	private $shortQueryCache = null;
	public function __construct(Server $server){
		$this->serverName = $server->getMotd();
		$this->listPlugins = $server->getProperty("settings.query-plugins", true);
		$this->plugins = $server->getPluginManager()->getPlugins();
		$this->players = [];
		foreach($server->getOnlinePlayers() as $player){
			if($player->isOnline()){
				$this->players[] = $player;
			}
		}
		$this->gametype = ($server->getGamemode() & 0x01) === 0 ? "SMP" : "CMP";
		$this->version = $server->getVersion();
		$this->server_engine = $server->getName() . " " . $server->getPocketMineVersion();
		$this->map = $server->getDefaultLevel() === null ? "unknown" : $server->getDefaultLevel()->getName();
		$this->numPlayers = count($this->players);
		$this->maxPlayers = $server->getMaxPlayers();
		$this->whitelist = $server->hasWhitelist() ? "on" : "off";
		$this->port = $server->getPort();
		$this->ip = $server->getIp();
	}
	public function getTimeout() : int{
		return 0;
	}
	public function setTimeout(int $timeout) : void{
	}
	private function destroyCache() : void{
		$this->longQueryCache = null;
		$this->shortQueryCache = null;
	}
	public function getServerName() : string{
		return $this->serverName;
	}
	public function setServerName(string $serverName) : void{
		$this->serverName = $serverName;
		$this->destroyCache();
	}
	public function canListPlugins() : bool{
		return $this->listPlugins;
	}
	public function setListPlugins(bool $value) : void{
		$this->listPlugins = $value;
		$this->destroyCache();
	}
	public function getPlugins() : array{
		return $this->plugins;
	}
	public function setPlugins(array $plugins) : void{
		$this->plugins = $plugins;
		$this->destroyCache();
	}
	public function getPlayerList() : array{
		return $this->players;
	}
	public function setPlayerList(array $players) : void{
		$this->players = $players;
		$this->destroyCache();
	}
	public function getPlayerCount() : int{
		return $this->numPlayers;
	}
	public function setPlayerCount(int $count) : void{
		$this->numPlayers = $count;
		$this->destroyCache();
	}
	public function getMaxPlayerCount() : int{
		return $this->maxPlayers;
	}
	public function setMaxPlayerCount(int $count) : void{
		$this->maxPlayers = $count;
		$this->destroyCache();
	}
	public function getWorld() : string{
		return $this->map;
	}
	public function setWorld(string $world) : void{
		$this->map = $world;
		$this->destroyCache();
	}
	public function getExtraData() : array{
		return $this->extraData;
	}
	public function setExtraData(array $extraData) : void{
		$this->extraData = $extraData;
		$this->destroyCache();
	}
	public function getLongQuery() : string{
		if($this->longQueryCache !== null){
			return $this->longQueryCache;
		}
		$query = "";
		$plist = $this->server_engine;
		if(count($this->plugins) > 0 and $this->listPlugins){
			$plist .= ":";
			foreach($this->plugins as $p){
				$d = $p->getDescription();
				$plist .= " " . str_replace([";", ":", " "], ["", "", "_"], $d->getName()) . " " . str_replace([";", ":", " "], ["", "", "_"], $d->getVersion()) . ";";
			}
			$plist = substr($plist, 0, -1);
		}
		$KVdata = [
			"splitnum" => chr(128),
			"hostname" => $this->serverName,
			"gametype" => $this->gametype,
			"game_id" => self::GAME_ID,
			"version" => $this->version,
			"server_engine" => $this->server_engine,
			"plugins" => $plist,
			"map" => $this->map,
			"numplayers" => $this->numPlayers,
			"maxplayers" => $this->maxPlayers,
			"whitelist" => $this->whitelist,
			"hostip" => $this->ip,
			"hostport" => $this->port
		];
		foreach($KVdata as $key => $value){
			$query .= $key . "\x00" . $value . "\x00";
		}
		foreach($this->extraData as $key => $value){
			$query .= $key . "\x00" . $value . "\x00";
		}
		$query .= "\x00\x01player_\x00\x00";
		foreach($this->players as $player){
			$query .= $player->getName() . "\x00";
		}
		$query .= "\x00";
		return $this->longQueryCache = $query;
	}
	public function getShortQuery() : string{
		return $this->shortQueryCache ?? ($this->shortQueryCache = $this->serverName . "\x00" . $this->gametype . "\x00" . $this->map . "\x00" . $this->numPlayers . "\x00" . $this->maxPlayers . "\x00" . (pack("v", $this->port)) . $this->ip . "\x00");
	}}