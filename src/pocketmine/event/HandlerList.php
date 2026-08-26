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
use Exception;use InvalidStateException;use pocketmine\plugin\Plugin;use pocketmine\plugin\RegisteredListener;use pocketmine\utils\Utils;use ReflectionClass;use ReflectionException;use function array_fill_keys;use function in_array;use function spl_object_hash;
class HandlerList{
	private static $allLists = [];
	public static function unregisterAll($object = null) : void{
		if($object instanceof Listener or $object instanceof Plugin){
			foreach(self::$allLists as $h){
				$h->unregister($object);
			}
		}else{
			foreach(self::$allLists as $h){
				foreach($h->handlerSlots as $key => $list){
					$h->handlerSlots[$key] = [];
				}
			}
		}
	}
	public static function getHandlerListFor(string $event) : ?HandlerList{
		if(isset(self::$allLists[$event])){
			return self::$allLists[$event];
		}
		$class = new ReflectionClass($event);
		$tags = Utils::parseDocComment((string) $class->getDocComment());
		if($class->isAbstract() && !isset($tags["allowHandle"])){
			return null;
		}
		$super = $class;
		$parentList = null;
		while($parentList === null && ($super = $super->getParentClass()) !== false){
			$parentList = self::getHandlerListFor($super->getName());
		}
		return new HandlerList($event, $parentList);
	}
	public static function getHandlerLists() : array{
		return self::$allLists;
	}
	private $class;
	private $handlerSlots = [];
	private $parentList;
	public function __construct(string $class, ?HandlerList $parentList){
		$this->class = $class;
		$this->handlerSlots = array_fill_keys(EventPriority::ALL, []);
		$this->parentList = $parentList;
		self::$allLists[$this->class] = $this;
	}
	public function register(RegisteredListener $listener) : void{
		if(!in_array($listener->getPriority(), EventPriority::ALL, true)){
			return;
		}
		if(isset($this->handlerSlots[$listener->getPriority()][spl_object_hash($listener)])){
			throw new InvalidStateException("This listener is already registered to priority {$listener->getPriority()} of event {$this->class}");
		}
		$this->handlerSlots[$listener->getPriority()][spl_object_hash($listener)] = $listener;
	}
	public function registerAll(array $listeners) : void{
		foreach($listeners as $listener){
			$this->register($listener);
		}
	}
	public function unregister($object) : void{
		if($object instanceof Plugin or $object instanceof Listener){
			foreach($this->handlerSlots as $priority => $list){
				foreach($list as $hash => $listener){
					if(($object instanceof Plugin and $listener->getPlugin() === $object)
						or ($object instanceof Listener and $listener->getListener() === $object)
					){
						unset($this->handlerSlots[$priority][$hash]);
					}
				}
			}
		}elseif($object instanceof RegisteredListener){
			if(isset($this->handlerSlots[$object->getPriority()][spl_object_hash($object)])){
				unset($this->handlerSlots[$object->getPriority()][spl_object_hash($object)]);
			}
		}
	}
	public function getListenersByPriority(int $priority) : array{
		return $this->handlerSlots[$priority];
	}
	public function getParent() : ?HandlerList{
		return $this->parentList;
	}}