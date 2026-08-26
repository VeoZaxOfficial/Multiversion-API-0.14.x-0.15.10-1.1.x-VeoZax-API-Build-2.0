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
use BadMethodCallException;use pmmp\thread\Runnable;use pmmp\thread\ThreadSafe;use pmmp\thread\ThreadSafeArray;use pocketmine\Server;use pocketmine\thread\NonThreadSafeValue;use Throwable;use function is_scalar;
abstract class AsyncTask extends Runnable{
    private static array $threadLocalStorage = [];
    private ?ThreadSafeArray $progressUpdates = null;
    private ThreadSafe|string|int|bool|null|float $result = null;
    private bool $submitted = false;
    private bool $finished = false;
    private bool $cancelRun = false;
    private bool $crashed = false;
    private bool $isGarbage = false;
    public int $workerId = 0;
    public function run() : void{
		$this->result = null;
		if(!$this->cancelRun){
			try{
				$this->onRun();
			}catch(Throwable $e){
				$this->crashed = true;
                \GlobalLogger::get()->logException($e);
			}
		}
        $this->finished = true;
        $this->setGarbage();
	}
	public function isCrashed() : bool{
		return $this->crashed or $this->isTerminated();
	}
    public function isFinished() : bool{
        return $this->finished || $this->isTerminated();
    }
    public function hasResult() : bool{
        return $this->result !== null;
    }
    public function getResult(){
        if($this->result instanceof NonThreadSafeValue){
            return $this->result->deserialize();
        }
        return $this->result;
    }
    public function setResult(mixed $result) : void{
        $this->result = is_scalar($result) || is_null($result) || $result instanceof ThreadSafe ? $result : new NonThreadSafeValue($result);
    }
	public function cancelRun() : void{
		$this->cancelRun = true;
	}
	public function hasCancelledRun() : bool{
		return $this->cancelRun;
	}
    public function setSubmitted() : void{
        $this->submitted = true;
    }
    public function isSubmitted() : bool{
        return $this->submitted;
    }
    abstract public function onRun() : void;
    public function onCompletion(Server $server){
    }
    public function publishProgress(mixed $progress) : void{
        $progressUpdates = $this->progressUpdates;
        if($progressUpdates === null){
            $progressUpdates = $this->progressUpdates = new ThreadSafeArray();
        }
        $progressUpdates[] = igbinary_serialize($progress) ?? throw new \InvalidArgumentException("Progress must be serializable");
    }
    public function checkProgressUpdates(Server $server) : void{
        $progressUpdates = $this->progressUpdates;
        if($progressUpdates !== null){
            while(($progress = $progressUpdates->shift()) !== null){
                $this->onProgressUpdate(igbinary_unserialize($progress));
            }
        }
    }
    public function onProgressUpdate(Server $server, $progress) : void{
    }
    public function onError() : void{
    }
    protected function storeLocal(mixed $complexData) : void{
        self::$threadLocalStorage[spl_object_id($this)] = $complexData;
    }
    protected function fetchLocal(){
        $id = spl_object_id($this);
        if(!isset(self::$threadLocalStorage[$id])){
            throw new \InvalidArgumentException("No matching thread-local data found on this thread");
        }
        return self::$threadLocalStorage[$id];
    }
    final public function __destruct(){
        $this->reallyDestruct();
        unset(self::$threadLocalStorage[spl_object_id($this)]);
    }
    protected function reallyDestruct() : void{
    }
    public function isGarbage() : bool{
        return $this->isGarbage;
    }
    public function setGarbage() : void{
        $this->isGarbage = true;
    }}