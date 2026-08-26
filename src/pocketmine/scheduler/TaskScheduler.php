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
use InvalidStateException;use pocketmine\utils\ReversePriorityQueue;
class TaskScheduler{
	private $owner;
	private $enabled = true;
	protected $queue;
	protected $tasks = [];
	private $ids = 1;
	protected $currentTick = 0;
	public function __construct(?string $owner = null){
		$this->owner = $owner;
		$this->queue = new ReversePriorityQueue();
	}
	public function scheduleTask(Task $task){
		return $this->addTask($task, -1, -1);
	}
	public function scheduleDelayedTask(Task $task, int $delay){
		return $this->addTask($task, $delay, -1);
	}
	public function scheduleRepeatingTask(Task $task, int $period){
		return $this->addTask($task, -1, $period);
	}
	public function scheduleDelayedRepeatingTask(Task $task, int $delay, int $period){
		return $this->addTask($task, $delay, $period);
	}
	public function cancelTask(int $taskId){
		if(isset($this->tasks[$taskId])){
			try{
				$this->tasks[$taskId]->cancel();
			}finally{
				unset($this->tasks[$taskId]);
			}
		}
	}
	public function cancelAllTasks(){
		foreach($this->tasks as $id => $task){
			$this->cancelTask($id);
		}
		$this->tasks = [];
		while(!$this->queue->isEmpty()){
			$this->queue->extract();
		}
		$this->ids = 1;
	}
	public function isQueued(int $taskId) : bool{
		return isset($this->tasks[$taskId]);
	}
	private function addTask(Task $task, int $delay, int $period){
		if(!$this->enabled){
			throw new InvalidStateException("Tried to schedule task to disabled scheduler");
		}
		if($delay <= 0){
			$delay = -1;
		}
		if($period <= -1){
			$period = -1;
		}elseif($period < 1){
			$period = 1;
		}
		return $this->handle(new TaskHandler($task, $this->nextId(), $delay, $period, $this->owner));
	}
	private function handle(TaskHandler $handler) : TaskHandler{
		if($handler->isDelayed()){
			$nextRun = $this->currentTick + $handler->getDelay();
		}else{
			$nextRun = $this->currentTick;
		}
		$handler->setNextRun($nextRun);
		$this->tasks[$handler->getTaskId()] = $handler;
		$this->queue->insert($handler, $nextRun);
		return $handler;
	}
	public function shutdown() : void{
		$this->enabled = false;
		$this->cancelAllTasks();
	}
	public function setEnabled(bool $enabled) : void{
		$this->enabled = $enabled;
	}
	public function mainThreadHeartbeat(int $currentTick){
		$this->currentTick = $currentTick;
		while($this->isReady($this->currentTick)){
			$task = $this->queue->extract();
			if($task->isCancelled()){
				unset($this->tasks[$task->getTaskId()]);
				continue;
			}
			$task->run($this->currentTick);
			if($task->isRepeating()){
				$task->setNextRun($this->currentTick + $task->getPeriod());
				$this->queue->insert($task, $this->currentTick + $task->getPeriod());
			}else{
				$task->remove();
				unset($this->tasks[$task->getTaskId()]);
			}
		}
	}
public function isEnabled() : bool {
    return $this->enabled;}
	private function isReady(int $currentTick) : bool{
		return !$this->queue->isEmpty() and $this->queue->current()->getNextRun() <= $currentTick;
	}
	private function nextId() : int{
		return $this->ids++;
	}}