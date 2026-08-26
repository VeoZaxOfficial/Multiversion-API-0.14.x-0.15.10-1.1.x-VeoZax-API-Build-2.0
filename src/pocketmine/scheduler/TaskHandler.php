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
use pocketmine\timings\Timings;use pocketmine\timings\TimingsHandler;
class TaskHandler{
	protected $task;
	protected $taskId;
	protected $delay;
	protected $period;
	protected $nextRun;
	protected $cancelled = false;
	private $timings;
	private $taskName;
	private $ownerName;
	public function __construct(Task $task, int $taskId, int $delay = -1, int $period = -1, ?string $ownerName = null){
		$this->task = $task;
		$this->taskId = $taskId;
		$this->delay = $delay;
		$this->period = $period;
		$this->taskName = $task->getName();
		$this->ownerName = $ownerName ?? "Unknown";
		$this->timings = Timings::getScheduledTaskTimings($this, $period);
		$this->task->setHandler($this);
	}
	public function isCancelled() : bool{
		return $this->cancelled;
	}
	public function getNextRun() : int{
		return $this->nextRun;
	}
	public function setNextRun(int $ticks){
		$this->nextRun = $ticks;
	}
	public function getTaskId() : int{
		return $this->taskId;
	}
	public function getTask() : Task{
		return $this->task;
	}
	public function getDelay() : int{
		return $this->delay;
	}
	public function isDelayed() : bool{
		return $this->delay > 0;
	}
	public function isRepeating() : bool{
		return $this->period > 0;
	}
	public function getPeriod() : int{
		return $this->period;
	}
	public function cancel(){
		try{
			if(!$this->isCancelled()){
				$this->task->onCancel();
			}
		}finally{
			$this->remove();
		}
	}
	public function remove(){
		$this->cancelled = true;
		$this->task->setHandler(null);
	}
	public function run(int $currentTick){
		$this->timings->startTiming();
		try{
			$this->task->onRun($currentTick);
		}finally{
			$this->timings->stopTiming();
		}
	}
	public function getTaskName() : string{
		return $this->taskName;
	}
	public function getOwnerName() : string{
		return $this->ownerName;
	}}