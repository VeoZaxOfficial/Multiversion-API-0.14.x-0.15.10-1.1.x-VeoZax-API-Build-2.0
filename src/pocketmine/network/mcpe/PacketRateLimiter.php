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
namespace pocketmine\network\mcpe;
use InvalidArgumentException;use function hrtime;use function intdiv;use function min;
final class PacketRateLimiter{
	private int $budget;
	private int $lastUpdateTimeNs;
	private int $maxBudget;
	public function __construct(
		private string $name,
		private int $averagePerTick,
		int $maxBufferTicks,
		private int $updateFrequencyNs = 50_000_000,
	){
		$this->maxBudget = $this->averagePerTick * $maxBufferTicks;
		$this->budget = $this->maxBudget;
		$this->lastUpdateTimeNs = hrtime(true);
	}
	public function decrement(int $amount = 1) : void{
		if($this->budget <= 0){
			$this->update();
			if($this->budget <= 0){
				throw new InvalidArgumentException("Exceeded rate limit for \"$this->name\"");
			}
		}
		$this->budget -= $amount;
	}
	public function update() : void{
		$nowNs = hrtime(true);
		$timeSinceLastUpdateNs = $nowNs - $this->lastUpdateTimeNs;
		if($timeSinceLastUpdateNs > $this->updateFrequencyNs){
			$ticksSinceLastUpdate = intdiv($timeSinceLastUpdateNs, $this->updateFrequencyNs);
			$this->budget = min($this->budget, $this->maxBudget) + ($this->averagePerTick * 2 * $ticksSinceLastUpdate);
			$this->lastUpdateTimeNs = $nowNs;
		}
	}}