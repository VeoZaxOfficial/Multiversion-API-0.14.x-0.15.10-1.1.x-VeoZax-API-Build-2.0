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
namespace pocketmine\command;
use pmmp\thread\ThreadSafeArray;use pocketmine\snooze\SleeperNotifier;use pocketmine\thread\Thread;use pocketmine\thread\ThreadException;use pocketmine\utils\OS;use pocketmine\utils\Utils;
class CommandReader extends Thread{
	const TYPE_READLINE = 0;
	const TYPE_STREAM = 1;
	const TYPE_PIPED = 2;
	protected ThreadSafeArray $buffer;
	private bool $shutdown = false;
	private int $type = self::TYPE_STREAM;
	private SleeperNotifier $notifier;
	public function __construct(?SleeperNotifier $notifier = null) {
		$this->buffer = new ThreadSafeArray;
		$this->notifier = $notifier;
		$opts = getopt("", ["disable-readline", "enable-readline"]);
		if(extension_loaded("readline") and (OS::from(Utils::getOS()) === OS::WINDOWS ? isset($opts["enable-readline"]) : !isset($opts["disable-readline"])) and !$this->isPipe(STDIN)){
			$this->type = self::TYPE_READLINE;
		}
		$this->setClassLoader();
    }
	public function shutdown(){
		$this->shutdown = true;
	}
	public function quit() : void{
		$wait = microtime(true) + 0.5;
		while(microtime(true) < $wait){
			if($this->isRunning()){
				usleep(100000);
			}else{
				parent::quit();
				return;
			}
		}
		$message = "Thread blocked for unknown reason";
		if($this->type === self::TYPE_PIPED){
			$message = "STDIN is being piped from another location and the pipe is blocked, cannot stop safely";
		}
		throw new ThreadException($message);
	}
	private function initStdin(){
		global $stdin;
		if(is_resource($stdin)){
			fclose($stdin);
		}
		$stdin = fopen("php://stdin", "r");
		if($this->isPipe($stdin)){
			$this->type = self::TYPE_PIPED;
		}else{
			$this->type = self::TYPE_STREAM;
		}
	}
	private function isPipe($stream) : bool{
		return is_resource($stream) and ((function_exists("posix_isatty") and !posix_isatty($stream)) or ((fstat($stream)["mode"] & 0170000) === 0010000));
	}
	private function readLine() : bool{
		$line = "";
		if($this->type === self::TYPE_READLINE){
			if(($raw = readline("> ")) !== false and ($line = trim($raw)) !== ""){
				readline_add_history($line);
			}else{
				return true;
			}
		}else{
			global $stdin;
			if(!is_resource($stdin)){
				$this->initStdin();
			}
			switch($this->type){
				case self::TYPE_STREAM:
					$r = [$stdin];
					$w = $e = null;
					if(($count = stream_select($r, $w, $e, 0, 200000)) === 0){ 
						return true;
					}elseif($count === false){ 
						$this->initStdin();
					}
				case self::TYPE_PIPED:
					if(($raw = fgets($stdin)) === false){ 
						$this->initStdin();
						$this->synchronized(function(){
							$this->wait(200000);
						}); 
						return true; 
					}
					$line = trim($raw);
					break;
			}
		}
		if($line !== ""){
			$this->buffer[] = preg_replace("#\\x1b\\x5b([^\\x1b]*\\x7e|[\\x40-\\x50])#", "", $line);
			if($this->notifier !== null){
			    $this->notifier->wakeupSleeper();
            }
		}
		return true;
	}
	public function getLine(){
		if($this->buffer->count() !== 0){
			return $this->buffer->shift();
		}
		return null;
	}
	public function onRun() : void{
		$this->registerClassLoader();
		if($this->type !== self::TYPE_READLINE){
			$this->initStdin();
		}
		while(!$this->shutdown and $this->readLine()) ;
		if($this->type !== self::TYPE_READLINE){
			global $stdin;
			fclose($stdin);
		}
	}
	public function getThreadName() : string{
		return "Console";
	}}