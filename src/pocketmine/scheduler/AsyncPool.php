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
use pmmp\thread\Thread as NativeThread;use pocketmine\Server;use pocketmine\thread\log\ThreadSafeLogger;use pocketmine\thread\ThreadCrashException;use pocketmine\thread\ThreadSafeClassLoader;use pocketmine\utils\AssumptionFailedError;use pocketmine\utils\Utils;use function array_keys;use function assert;use function count;use function time;use const PHP_INT_MAX;
class AsyncPool{
    private const WORKER_START_OPTIONS = NativeThread::INHERIT_INI | NativeThread::INHERIT_COMMENTS | NativeThread::INHERIT_CONSTANTS;
    private array $workers = [];
    private array $workerStartHooks = [];
    public function __construct(
        protected Server $server,
        protected int $size,
        private int $workerMemoryLimit,
        private ThreadSafeClassLoader $classLoader,
        private ThreadSafeLogger $logger,
    ){}
    public function getSize() : int{
        return $this->size;
    }
    public function increaseSize(int $newSize) : void{
        if($newSize > $this->size){
            $this->size = $newSize;
        }
    }
    public function addWorkerStartHook(\Closure $hook) : void{
        Utils::validateCallableSignature(function(int $worker) : void{}, $hook);
        $this->workerStartHooks[spl_object_id($hook)] = $hook;
        foreach($this->workers as $i => $worker){
            $hook($i);
        }
    }
    public function removeWorkerStartHook(\Closure $hook) : void{
        unset($this->workerStartHooks[spl_object_id($hook)]);
    }
    public function getRunningWorkers() : array{
        return array_keys($this->workers);
    }
    private function getWorker(int $workerId) : AsyncPoolWorkerEntry{
        if(!isset($this->workers[$workerId])){
            $this->workers[$workerId] = new AsyncPoolWorkerEntry(new AsyncWorker($this->logger, $workerId, $this->workerMemoryLimit));
            $this->workers[$workerId]->worker->setClassLoader($this->classLoader);
            $this->workers[$workerId]->worker->start(self::WORKER_START_OPTIONS);
            foreach($this->workerStartHooks as $hook){
                $hook($workerId);
            }
        }else{
            $this->checkCrashedWorker($workerId, null);
        }
        return $this->workers[$workerId];
	}
    public function submitTaskToWorker(AsyncTask $task, int $worker) : void{
        if($worker < 0 || $worker >= $this->size){
            throw new \InvalidArgumentException("Invalid worker $worker");
        }
        if($task->isSubmitted()){
            throw new \InvalidArgumentException("Cannot submit the same AsyncTask instance more than once");
        }
        $task->setSubmitted();
        $task->workerId = $worker;
        $this->getWorker($worker)->submit($task);
	}
    public function selectWorker() : int{
        $worker = null;
        $minUsage = PHP_INT_MAX;
        foreach($this->workers as $i => $entry){
            if(($usage = $entry->tasks->count()) < $minUsage){
                $worker = $i;
                $minUsage = $usage;
                if($usage === 0){
                    break;
                }
            }
        }
        if($worker === null || ($minUsage > 0 && count($this->workers) < $this->size)){
            for($i = 0; $i < $this->size; ++$i){
                if(!isset($this->workers[$i])){
                    $worker = $i;
                    break;
                }
            }
        }
        assert($worker !== null);
        return $worker;
    }
    public function submitTask(AsyncTask $task) : int{
        if($task->isSubmitted()){
            throw new \InvalidArgumentException("Cannot submit the same AsyncTask instance more than once");
        }
        $worker = $this->selectWorker();
        $this->submitTaskToWorker($task, $worker);
        return $worker;
    }
    private function checkCrashedWorker(int $workerId, ?AsyncTask $crashedTask) : void{
        $entry = $this->workers[$workerId];
        if($entry->worker->isTerminated()){
            if($crashedTask === null){
                foreach($entry->tasks as $task){
                    if($task->isTerminated()){
                        $crashedTask = $task;
                        break;
                    }elseif(!$task->isFinished()){
                        break;
                    }
                }
            }
            $info = $entry->worker->getCrashInfo();
            if($info !== null){
                if($crashedTask !== null){
                    $message = "Worker $workerId crashed while running task " . get_class($crashedTask) . "#" . spl_object_id($crashedTask);
                }else{
                    $message = "Worker $workerId crashed while doing unknown work";
                }
                throw new ThreadCrashException($message, $info);
            }else{
                throw new \RuntimeException("Worker $workerId crashed for unknown reason");
            }
        }
    }
    public function collectTasks() : bool{
        foreach($this->workers as $workerId => $entry){
            $this->collectTasksFromWorker($workerId);
        }
        foreach($this->workers as $entry){
            if(!$entry->tasks->isEmpty()){
                return true;
            }
        }
        return false;
    }
    public function collectTasksFromWorker(int $worker) : bool{
        if(!isset($this->workers[$worker])){
            throw new \InvalidArgumentException("No such worker $worker");
        }
        $queue = $this->workers[$worker]->tasks;
        $more = false;
        while(!$queue->isEmpty()){
            $task = $queue->bottom();
            if($task->isFinished()){ 
                $queue->dequeue();
                if($task->isTerminated()){
                    $this->checkCrashedWorker($worker, $task);
                    throw new AssumptionFailedError("checkCrashedWorker() should have thrown an exception, making this unreachable");
                }else{
                    if(!$task->hasCancelledRun()){
                        $this->checkTaskProgressUpdates($task);
                        $task->onCompletion($this->server);
                    }
                }
            }else{
                $this->checkTaskProgressUpdates($task);
                $more = true;
                break; 
            }
        }
        $this->workers[$worker]->worker->collect();
        return $more;
    }
    public function getTaskQueueSizes() : array{
        return array_map(function(AsyncPoolWorkerEntry $entry) : int{ return $entry->tasks->count(); }, $this->workers);
    }
    public function shutdownUnusedWorkers() : int{
        $ret = 0;
        $time = time();
        foreach($this->workers as $i => $entry){
            if($entry->lastUsed + 300 < $time && $entry->tasks->isEmpty()){
                $entry->worker->quit();
                unset($this->workers[$i]);
                $ret++;
            }
        }
        return $ret;
    }
    public function shutdown() : void{
        while($this->collectTasks()){
        }
        foreach($this->workers as $worker){
            $worker->worker->quit();
        }
        $this->workers = [];
    }
    private function checkTaskProgressUpdates(AsyncTask $task) : void{
        $task->checkProgressUpdates($this->server);
    }}