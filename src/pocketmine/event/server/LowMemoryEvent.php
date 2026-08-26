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
namespace pocketmine\event\server;
use pocketmine\utils\Utils;
class LowMemoryEvent extends ServerEvent{
	private $memory;
	private $memoryLimit;
	private $triggerCount;
	private $global;
	public function __construct(int $memory, int $memoryLimit, bool $isGlobal = false, int $triggerCount = 0){
		$this->memory = $memory;
		$this->memoryLimit = $memoryLimit;
		$this->global = $isGlobal;
		$this->triggerCount = $triggerCount;
	}
	public function getMemory() : int{
		return $this->memory;
	}
	public function getMemoryLimit() : int{
		return $this->memoryLimit;
	}
	public function getTriggerCount() : int{
		return $this->triggerCount;
	}
	public function isGlobal() : bool{
		return $this->global;
	}
	public function getMemoryFreed() : int{
		return $this->getMemory() - ($this->isGlobal() ? Utils::getMemoryUsage(true)[1] : Utils::getMemoryUsage(true)[0]);
	}}