<?php

namespace pocketmine\utils;

use pocketmine\Server;

/**
 * Broadcasts a fixed reminder message to all online players once every
 * hour. Ticked once per second from Server::tick()'s existing
 * once-per-second block (the same block driving doAutoRestartTick()) --
 * adds no extra per-tick overhead of its own; just an integer increment
 * on 1-in-20 ticks, with a broadcast only once every 3600 of those.
 */
final class ThreadMT{

	private const MESSAGE = "§8(§c!§8) §f§oMulti-Version API by §bVeoZax";
	private const INTERVAL_SECONDS = 3600; // 1 hour

	private static $secondsElapsed = 0;

	private function __construct(){}

	public static function tick(Server $server) : void{
		if(++self::$secondsElapsed < self::INTERVAL_SECONDS){
			return;
		}
		self::$secondsElapsed = 0;
		$server->broadcastMessage(self::MESSAGE);
	}
}
