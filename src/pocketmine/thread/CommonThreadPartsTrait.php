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
namespace pocketmine\thread;
use pocketmine\errorhandler\ErrorToExceptionHandler;use pocketmine\Server;use function error_get_last;use function error_reporting;use function implode;use function register_shutdown_function;use function set_exception_handler;use Throwable;use ReflectionClass;use GlobalLogger;
trait CommonThreadPartsTrait{
	private ?ThreadSafeClassLoader $classLoader = null;
	protected ?ThreadCrashInfo $crashInfo = null;
	protected ?string $composerAutoloaderPath = null;
	protected $isKilled = false;
	public function getCrashInfo() : ?ThreadCrashInfo{
		return $this->crashInfo;
	}
	public function getClassLoader() : ?ThreadSafeClassLoader{
		return $this->classLoader;
	}
	public function setClassLoader(ThreadSafeClassLoader $loader = null){
		$this->composerAutoloaderPath = \pocketmine\COMPOSER_AUTOLOADER_PATH;
		if($loader === null){
			$loader = Server::getInstance()->getLoader();
		}
		$this->classLoader = $loader;
	}
	public function registerClassLoader() : void{
		if($this->classLoader !== null){
			$this->classLoader->register(true);
		}
		if ($this->composerAutoloaderPath !== null) {
			require $this->composerAutoloaderPath;
		}
	}
	final public function run() : void{
		error_reporting(-1);
		$this->registerClassLoader();
		ErrorToExceptionHandler::set();
		set_exception_handler($this->onUncaughtException(...));
		register_shutdown_function($this->onShutdown(...));
		$this->onRun();
		$this->isKilled = true;
	}
	protected function onUncaughtException(Throwable $e) : void{
		$this->synchronized(function() use ($e) : void{
			$this->crashInfo = ThreadCrashInfo::fromThrowable($e, $this->getThreadName());
			GlobalLogger::get()->logException($e);
		});
	}
	protected function onShutdown() : void{
		$this->synchronized(function() : void{
			if(!$this->isTerminated() && $this->crashInfo === null){
				$last = error_get_last();
				if($last !== null){
					$crashInfo = ThreadCrashInfo::fromLastErrorInfo($last, $this->getThreadName());
				}else{
					return;
				}
				$this->crashInfo = $crashInfo;
				$lines = [];
				$lines[] = "Fatal error: " . $crashInfo->makePrettyMessage();
				$lines[] = "--- Stack trace ---";
				foreach($crashInfo->getTrace() as $frame){
					$lines[] = "  " . $frame->getPrintableFrame();
				}
				$lines[] = "--- End of fatal error information ---";
				GlobalLogger::get()->critical(implode("\n", $lines));
			}
		});
	}
	abstract protected function onRun() : void;
	public function getThreadName() : string{
		return (new ReflectionClass($this))->getShortName();
	}}