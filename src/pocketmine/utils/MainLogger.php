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
use DateTime;use DateTimeZone;use LogLevel;use pmmp\thread\Thread as NativeThread;use pocketmine\thread\log\AttachableThreadSafeLogger;use pocketmine\thread\log\ThreadSafeLoggerAttachment;use pocketmine\thread\Thread;use pocketmine\thread\Worker;use pocketmine\utils\TextFormat;use pocketmine\utils\Terminal;use pocketmine\utils\Utils;
class MainLogger extends AttachableThreadSafeLogger{
    private $format = TextFormat::AQUA . "[%s] " . TextFormat::RESET . "%s[%s/%s]: %s" . TextFormat::RESET;
	protected $shutdown;
	protected $logDebug;
	protected $logToFile = true;
	protected $timezone;
	private $logWriterThread;
	public function __construct(string $logFile, DateTimeZone $timezone, bool $logDebug = false, bool $logToFile = true) {
        parent::__construct();
			$brandTag = TextFormat::DARK_GRAY . "[" . TextFormat::BLUE . "Veo" . TextFormat::AQUA . "Zax" . TextFormat::RED . "API" . TextFormat::DARK_GRAY . "]" . TextFormat::RESET;
			$this->format = $brandTag . " \xC2\xBB " . TextFormat::RESET .
				TextFormat::AQUA . "[%s] - " . TextFormat::RESET . "%s[%s/%s]: %s" . TextFormat::RESET;
			$this->logDebug = $logDebug;
			$this->timezone = $timezone->getName();
			$this->logToFile = $logToFile;
			$this->logWriterThread = $logToFile ? new MainLoggerThread($logFile) : null;
			if($this->logWriterThread !== null){
				$this->logWriterThread->start(NativeThread::INHERIT_NONE);
			}
		}
	public function emergency(mixed $message) : void{
		$this->send($message, LogLevel::EMERGENCY, "EMERGENCY", TextFormat::RED);
	}
	public function alert(mixed $message) : void{
		$this->send($message, LogLevel::ALERT, "ALERT", TextFormat::RED);
	}
	public function critical(mixed $message) : void{
		$this->send($message, LogLevel::CRITICAL, "CRITICAL", TextFormat::RED);
	}
	public function error(mixed $message) : void{
		$this->send($message, LogLevel::ERROR, "ERROR", TextFormat::DARK_RED);
	}
	public function warning(mixed $message) : void{
		$this->send($message, LogLevel::WARNING, "WARNING", TextFormat::YELLOW);
	}
	public function notice(mixed $message) : void{
		$this->send($message, LogLevel::NOTICE, "NOTICE", TextFormat::AQUA);
	}
	public function info(mixed $message) : void{
		$this->send($message, LogLevel::INFO, "INFO", TextFormat::WHITE);
	}
	public function debug(mixed $message) : void{
		if($this->logDebug === false){
			return;
		}
		$this->send($message, LogLevel::DEBUG, "DEBUG", TextFormat::GRAY);
	}
	public function setLogDebug(bool $logDebug) : void{
		$this->logDebug = $logDebug;
	}
	public function setLogToFile(bool $logToFile) : void{
		$this->logToFile = $logToFile;
	}
	public function logException(\Throwable $e, $trace = null) : void{
		$this->critical(implode("\n", Utils::printableExceptionInfo($e, $trace)));
	}
	public function shutdownLogWriterThread() : void{
		if($this->logWriterThread === null){
			return;
		}
		if(NativeThread::getCurrentThreadId() === $this->logWriterThread->getCreatorId()){
			$this->logWriterThread->shutdown();
		}else{
			throw new \LogicException("Only the creator thread can shutdown the logger thread");
		}
	}
	public function log($level, $message) : void{
		switch($level){
			case LogLevel::EMERGENCY:
				$this->emergency($message);
				break;
			case LogLevel::ALERT:
				$this->alert($message);
				break;
			case LogLevel::CRITICAL:
				$this->critical($message);
				break;
			case LogLevel::ERROR:
				$this->error($message);
				break;
			case LogLevel::WARNING:
				$this->warning($message);
				break;
			case LogLevel::NOTICE:
				$this->notice($message);
				break;
			case LogLevel::INFO:
				$this->info($message);
				break;
			case LogLevel::DEBUG:
				$this->debug($message);
				break;
		}
	}
	public function shutdown() : void{
		$this->shutdownLogWriterThread();
		$this->shutdown = true;
		$this->notify();
	}
	protected function send($message, $level, $prefix, $color) : void{
		$time = new DateTime('now', new DateTimeZone($this->timezone));
		$thread = NativeThread::getCurrentThread();
		if($thread === null){
			$threadName = "Server thread";
		}elseif($thread instanceof Thread or $thread instanceof Worker){
			$threadName = $thread->getThreadName() . " thread";
		}else{
			$threadName = (new \ReflectionClass($thread))->getShortName() . " thread";
		}
		$message = sprintf($this->format, $time->format("H:i:s"), $color, $threadName, $prefix, TextFormat::addBase($color, TextFormat::clean($message, false)));
		$this->synchronized(function() use ($message, $level, $time) : void{
			Terminal::writeLine($message);
			if ($this->logToFile && $this->logWriterThread !== null) {
				$this->logWriterThread->write($time->format("Y-m-d") . " " . TextFormat::clean($message) . PHP_EOL);
			}
			if ($this->attachments !== NULL) {
				foreach($this->attachments as $attachment){
					$attachment->log($level, $message);
				}
			}
		});
	}
	public function syncFlushBuffer() : void{
		if($this->logWriterThread !== null){
			$this->logWriterThread->syncFlushBuffer();
		}
	}
	public function __destruct(){
		if($this->logWriterThread !== null
			&& !$this->logWriterThread->isJoined()
			&& NativeThread::getCurrentThreadId() === $this->logWriterThread->getCreatorId()){
			$this->shutdownLogWriterThread();
		}
	}}