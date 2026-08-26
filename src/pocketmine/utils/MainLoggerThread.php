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
namespace pocketmine\utils;
use pmmp\thread\Thread;use pmmp\thread\ThreadSafeArray;use function fclose;use function fopen;use function fwrite;use function is_resource;use function touch;use RuntimeException;
final class MainLoggerThread extends Thread{
	private $buffer;
	private $syncFlush = false;
	private $shutdown = false;
	public function __construct(
		private string $logFile
	){
		$this->buffer = new ThreadSafeArray();
		touch($this->logFile);
	}
	public function write(string $line) : void{
		$this->synchronized(function() use ($line) : void{
			$this->buffer[] = $line;
			$this->notify();
		});
	}
	public function syncFlushBuffer() : void{
		$this->synchronized(function() : void{
			$this->syncFlush = true;
			$this->notify(); 
		});
		$this->synchronized(function() : void{
			while($this->syncFlush){
				$this->wait(); 
			}
		});
	}
	public function shutdown() : void{
		$this->synchronized(function() : void{
			$this->shutdown = true;
			$this->notify();
		});
		$this->join();
	}
	private function writeLogStream($logResource) : void{
		while(($chunk = $this->buffer->shift()) !== null){
			fwrite($logResource, $chunk);
		}
		$this->synchronized(function() : void{
			if($this->syncFlush){
				$this->syncFlush = false;
				$this->notify(); 
			}
		});
	}
	public function run() : void{
		$logResource = fopen($this->logFile, "ab");
		if(!is_resource($logResource)){
			throw new RuntimeException("Couldn't open log file");
		}
		while(!$this->shutdown){
			$this->writeLogStream($logResource);
			$this->synchronized(function() : void{
				if(!$this->shutdown && !$this->syncFlush){
					$this->wait();
				}
			});
		}
		$this->writeLogStream($logResource);
		fclose($logResource);
	}}