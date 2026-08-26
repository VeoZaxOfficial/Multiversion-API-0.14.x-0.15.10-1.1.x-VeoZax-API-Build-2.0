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
class SimpleLogger implements \Logger{
	public function emergency(mixed $message) : void{
		$this->log(LogLevel::EMERGENCY, $message);
	}
	public function alert(mixed $message) : void{
		$this->log(LogLevel::ALERT, $message);
	}
	public function critical(mixed $message) : void{
		$this->log(LogLevel::CRITICAL, $message);
	}
	public function error(mixed $message) : void{
		$this->log(LogLevel::ERROR, $message);
	}
	public function warning(mixed $message) : void{
		$this->log(LogLevel::WARNING, $message);
	}
	public function notice(mixed $message) : void{
		$this->log(LogLevel::NOTICE, $message);
	}
	public function info(mixed $message) : void{
		$this->log(LogLevel::INFO, $message);
	}
	public function debug(mixed $message) : void{
		$this->log(LogLevel::DEBUG, $message);
	}
	public function log(mixed $level, mixed $message) : void{
		echo "[" . strtoupper($level) . "] " . $message . PHP_EOL;
	}
	public function logException(\Throwable $e, $trace = null){
		$this->critical($e->getMessage());
		echo $e->getTraceAsString();
	}}