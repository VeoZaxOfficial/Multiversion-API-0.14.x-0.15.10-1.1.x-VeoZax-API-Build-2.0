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
namespace pocketmine\scheduler;
use pmmp\thread\Thread as NativeThread;use pocketmine\thread\log\ThreadSafeLogger;use pocketmine\thread\Worker;use pocketmine\utils\MainLogger;use pocketmine\utils\Utils;use Throwable;use function gc_enable;use function ini_set;
class AsyncWorker extends Worker{
    private static array $store = [];
    public function __construct(
        private ThreadSafeLogger $logger,
        private int $id,
        private int $memoryLimit
    ){}
    protected function onRun() : void{
        $this->registerClassLoader();
        \GlobalLogger::set($this->logger);
        set_error_handler([Utils::class, 'errorExceptionHandler']);
        gc_enable();
        if($this->memoryLimit > 0){
            ini_set('memory_limit', $this->memoryLimit . 'M');
            $this->logger->debug("Set memory limit to " . $this->memoryLimit . " MB");
        }else{
            ini_set('memory_limit', '-1');
        }
	}
    public function getLogger() : ThreadSafeLogger{
        return $this->logger;
    }
    public function handleException(Throwable $e) : void{
        $this->logger->logException($e);
    }
    public function getThreadName() : string{
        return "AsyncWorker#" . $this->id;
    }
    public function getAsyncWorkerId() : int{
        return $this->id;
    }
    public function saveToThreadStore(string $identifier, mixed $value) : void{
        if(NativeThread::getCurrentThread() !== $this){
            throw new \LogicException("Thread-local data can only be stored in the thread context");
        }
        self::$store[$identifier] = $value;
    }
    public function getFromThreadStore(string $identifier) : mixed{
        if(NativeThread::getCurrentThread() !== $this){
            throw new \LogicException("Thread-local data can only be fetched in the thread context");
        }
        return self::$store[$identifier] ?? null;
    }
    public function removeFromThreadStore(string $identifier) : void{
        if(NativeThread::getCurrentThread() !== $this){
            throw new \LogicException("Thread-local data can only be removed in the thread context");
        }
        unset(self::$store[$identifier]);
    }}