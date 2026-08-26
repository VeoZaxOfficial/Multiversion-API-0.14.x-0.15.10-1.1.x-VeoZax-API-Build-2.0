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
namespace pocketmine\entity\pathfinder;
use pocketmine\math\Vector3;use function array_slice;
class Path{
	protected $points = [];
	protected $currentIndex = 0;
	public function __construct(array $points = []){
		$this->points = $points;
	}
	public function havePath() : bool{
		return !empty($this->points) and $this->currentIndex < count($this->points) - 1;
	}
	public function getVectorByIndex(int $index) : ?Vector3{
		$point = $this->getPointByIndex($index);
		if($point === null) return null;
		return new Vector3($point->x, $point->height, $point->y);
	}
	public function getFinalPathPoint() : ?PathPoint{
		return end($this->points);
	}
	public function getPointByIndex(int $index) : ?PathPoint{
		return $this->points[$index] ?? null;
	}
	public function removePoint(int $index) : void{
		unset($this->points[$index]);
	}
	public function getPoints() : array{
		return $this->points;
	}
	public function getCurrentIndex() : int{
		return $this->currentIndex;
	}
	public function setCurrentIndex(int $currentIndex) : void{
		$this->currentIndex = $currentIndex;
	}
	public function limitPath(int $maxLength) : void{
		$this->points = array_slice($this->points, 0, $maxLength + 1);
	}}