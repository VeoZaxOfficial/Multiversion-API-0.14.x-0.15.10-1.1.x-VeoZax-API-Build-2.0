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
namespace pocketmine\snooze;
use function assert;use function microtime;
class SleeperHandler{
	private $threadedSleeper;
	private $notifiers = [];
	private $handlers = [];
	private $nextSleeperId = 0;
	public function __construct(){
		$this->threadedSleeper = new ThreadedSleeper();
	}
	public function getThreadedSleeper() : ThreadedSleeper{
		return $this->threadedSleeper;
	}
	public function addNotifier(SleeperNotifier $notifier, callable $handler) : void{
		$id = $this->nextSleeperId++;
		$notifier->attachSleeper($this->threadedSleeper, $id);
		$this->notifiers[$id] = $notifier;
		$this->handlers[$id] = $handler;
	}
	public function removeNotifier(SleeperNotifier $notifier) : void{
		unset($this->notifiers[$notifier->getSleeperId()], $this->handlers[$notifier->getSleeperId()]);
	}
	public function sleepUntil(float $unixTime) : void{
		while(true){
			$this->processNotifications();
			$sleepTime = (int) (($unixTime - microtime(true)) * 1000000);
			if($sleepTime > 0){
				$this->threadedSleeper->sleep($sleepTime);
			}else{
				break;
			}
		}
	}
	public function sleepUntilNotification() : void{
		$this->threadedSleeper->sleep(0);
		$this->processNotifications();
	}
	public function processNotifications() : void{
		while($this->threadedSleeper->hasNotifications()){
			$processed = 0;
			foreach($this->notifiers as $id => $notifier){
				if($notifier->hasNotification()){
					++$processed;
					$notifier->clearNotification();
					if(isset($this->notifiers[$id])){
						assert(isset($this->handlers[$id]));
						$this->handlers[$id]();
					}
				}
			}
			assert($processed > 0);
			$this->threadedSleeper->clearNotifications($processed);
		}
	}}