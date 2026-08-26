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
namespace pocketmine\level;
use InvalidArgumentException;use pocketmine\math\Vector3;use pocketmine\utils\AssumptionFailedError;use GlobalLogger;use function assert;
class Position extends Vector3{
	public $level = null;
	public function __construct($x = 0, $y = 0, $z = 0, Level $level = null){
		parent::__construct($x, $y, $z);
		$this->setLevel($level);
	}
	public static function fromObject(Vector3 $pos, Level $level = null){
		return new Position($pos->x, $pos->y, $pos->z, $level);
	}
	public function asPosition() : Position{
		return new Position($this->x, $this->y, $this->z, $this->level);
	}
	public function getLevel(){
		if($this->level !== null and $this->level->isClosed()){
			GlobalLogger::get()->debug("Position was holding a reference to an unloaded world");
			$this->level = null;
		}
		return $this->level;
	}
	public function getLevelNonNull() : Level{
		$world = $this->getLevel();
		if($world === null){
			throw new AssumptionFailedError("Position world is null");
		}
		return $world;
	}
	public function setLevel(Level $level = null){
		if($level !== null and $level->isClosed()){
			throw new InvalidArgumentException("Specified world has been unloaded and cannot be used");
		}
		$this->level = $level;
		return $this;
	}
	public function isValid() : bool{
		if($this->level !== null and $this->level->isClosed()){
			$this->level = null;
			return false;
		}
		return $this->level !== null;
	}
	public function getSide(int $side, int $step = 1){
		assert($this->isValid());
		return Position::fromObject(parent::getSide($side, $step), $this->level);
	}
	public function __toString(){
		return "Position(level=" . ($this->isValid() ? $this->getLevelNonNull()->getName() : "null") . ",x=" . $this->x . ",y=" . $this->y . ",z=" . $this->z . ")";
	}
	public function equals(Vector3 $v) : bool{
		if($v instanceof Position){
			return parent::equals($v) and $v->getLevel() === $this->getLevel();
		}
		return parent::equals($v);
	}}