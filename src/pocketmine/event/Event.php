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
namespace pocketmine\event;
use BadMethodCallException;use RuntimeException;use function assert;use function get_class;
abstract class Event{
	private const MAX_EVENT_CALL_DEPTH = 50;
	private static $eventCallDepth = 1;
	protected $eventName = null;
	private $isCancelled = false;
	final public function getEventName() : string{
		return $this->eventName ?? get_class($this);
	}
	public function isCancelled() : bool{
		if(!($this instanceof Cancellable)){
			throw new BadMethodCallException(get_class($this) . " is not Cancellable");
		}
		return $this->isCancelled;
	}
	public function setCancelled(bool $value = true) : void{
		if(!($this instanceof Cancellable)){
			throw new BadMethodCallException(get_class($this) . " is not Cancellable");
		}
		$this->isCancelled = $value;
	}
	public function call() : void{
		if(self::$eventCallDepth >= self::MAX_EVENT_CALL_DEPTH){
			throw new RuntimeException("Recursive event call detected (reached max depth of " . self::MAX_EVENT_CALL_DEPTH . " calls)");
		}
		$handlerList = HandlerList::getHandlerListFor(get_class($this));
		assert($handlerList !== null, "Called event should have a valid HandlerList");
		++self::$eventCallDepth;
		try{
			foreach(EventPriority::ALL as $priority){
				$currentList = $handlerList;
				while($currentList !== null){
					foreach($currentList->getListenersByPriority($priority) as $registration){
						$registration->callEvent($this);
					}
					$currentList = $currentList->getParent();
				}
			}
		}finally{
			--self::$eventCallDepth;
		}
	}}