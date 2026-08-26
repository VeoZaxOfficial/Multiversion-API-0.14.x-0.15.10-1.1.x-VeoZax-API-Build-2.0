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
namespace pocketmine\math;
use const PHP_INT_MAX;
class AxisAlignedBB{
	public $minX;
	public $minY;
	public $minZ;
	public $maxX;
	public $maxY;
	public $maxZ;
	public function __construct(float $minX, float $minY, float $minZ, float $maxX, float $maxY, float $maxZ){
		$this->setBounds($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}
	public function setBounds(float $minX, float $minY, float $minZ, float $maxX, float $maxY, float $maxZ){
		$this->minX = $minX;
		$this->minY = $minY;
		$this->minZ = $minZ;
		$this->maxX = $maxX;
		$this->maxY = $maxY;
		$this->maxZ = $maxZ;
		return $this;
	}
	public function setBB(AxisAlignedBB $bb){
		return $this->setBounds($bb->minX, $bb->minY, $bb->minZ, $bb->maxX, $bb->maxY, $bb->maxZ);
	}
	public function addCoord(float $x, float $y, float $z) : AxisAlignedBB{
		$minX = $this->minX;
		$minY = $this->minY;
		$minZ = $this->minZ;
		$maxX = $this->maxX;
		$maxY = $this->maxY;
		$maxZ = $this->maxZ;
		if($x < 0){
			$minX += $x;
		}elseif($x > 0){
			$maxX += $x;
		}
		if($y < 0){
			$minY += $y;
		}elseif($y > 0){
			$maxY += $y;
		}
		if($z < 0){
			$minZ += $z;
		}elseif($z > 0){
			$maxZ += $z;
		}
		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}
	public function expand(float $x, float $y, float $z){
		$this->minX -= $x;
		$this->minY -= $y;
		$this->minZ -= $z;
		$this->maxX += $x;
		$this->maxY += $y;
		$this->maxZ += $z;
		return $this;
	}
	public function expandedCopy(float $x, float $y, float $z) : AxisAlignedBB{
		return (clone $this)->expand($x, $y, $z);
	}
	public function offset(float $x, float $y, float $z){
		$this->minX += $x;
		$this->minY += $y;
		$this->minZ += $z;
		$this->maxX += $x;
		$this->maxY += $y;
		$this->maxZ += $z;
		return $this;
	}
	public function offsetCopy(float $x, float $y, float $z) : AxisAlignedBB{
		return (clone $this)->offset($x, $y, $z);
	}
	public function contract(float $x, float $y, float $z){
		$this->minX += $x;
		$this->minY += $y;
		$this->minZ += $z;
		$this->maxX -= $x;
		$this->maxY -= $y;
		$this->maxZ -= $z;
		return $this;
	}
	public function contractedCopy(float $x, float $y, float $z) : AxisAlignedBB{
		return (clone $this)->contract($x, $y, $z);
	}
	public function calculateXOffset(AxisAlignedBB $bb, float $x) : float{
		if($bb->maxY <= $this->minY or $bb->minY >= $this->maxY){
			return $x;
		}
		if($bb->maxZ <= $this->minZ or $bb->minZ >= $this->maxZ){
			return $x;
		}
		if($x > 0 and $bb->maxX <= $this->minX){
			$x1 = $this->minX - $bb->maxX;
			if($x1 < $x){
				$x = $x1;
			}
		}elseif($x < 0 and $bb->minX >= $this->maxX){
			$x2 = $this->maxX - $bb->minX;
			if($x2 > $x){
				$x = $x2;
			}
		}
		return $x;
	}
	public function calculateYOffset(AxisAlignedBB $bb, float $y) : float{
		if($bb->maxX <= $this->minX or $bb->minX >= $this->maxX){
			return $y;
		}
		if($bb->maxZ <= $this->minZ or $bb->minZ >= $this->maxZ){
			return $y;
		}
		if($y > 0 and $bb->maxY <= $this->minY){
			$y1 = $this->minY - $bb->maxY;
			if($y1 < $y){
				$y = $y1;
			}
		}elseif($y < 0 and $bb->minY >= $this->maxY){
			$y2 = $this->maxY - $bb->minY;
			if($y2 > $y){
				$y = $y2;
			}
		}
		return $y;
	}
	public function calculateZOffset(AxisAlignedBB $bb, float $z) : float{
		if($bb->maxX <= $this->minX or $bb->minX >= $this->maxX){
			return $z;
		}
		if($bb->maxY <= $this->minY or $bb->minY >= $this->maxY){
			return $z;
		}
		if($z > 0 and $bb->maxZ <= $this->minZ){
			$z1 = $this->minZ - $bb->maxZ;
			if($z1 < $z){
				$z = $z1;
			}
		}elseif($z < 0 and $bb->minZ >= $this->maxZ){
			$z2 = $this->maxZ - $bb->minZ;
			if($z2 > $z){
				$z = $z2;
			}
		}
		return $z;
	}
	public function intersectsWith(AxisAlignedBB $bb, float $epsilon = 0.00001) : bool{
		if($bb->maxX - $this->minX > $epsilon and $this->maxX - $bb->minX > $epsilon){
			if($bb->maxY - $this->minY > $epsilon and $this->maxY - $bb->minY > $epsilon){
				return $bb->maxZ - $this->minZ > $epsilon and $this->maxZ - $bb->minZ > $epsilon;
			}
		}
		return false;
	}
	public function isVectorInside(Vector3 $vector) : bool{
		if($vector->x <= $this->minX or $vector->x >= $this->maxX){
			return false;
		}
		if($vector->y <= $this->minY or $vector->y >= $this->maxY){
			return false;
		}
		return $vector->z > $this->minZ and $vector->z < $this->maxZ;
	}
	public function getAverageEdgeLength() : float{
		return ($this->maxX - $this->minX + $this->maxY - $this->minY + $this->maxZ - $this->minZ) / 3;
	}
	public function isVectorInYZ(Vector3 $vector) : bool{
		return $vector->y >= $this->minY and $vector->y <= $this->maxY and $vector->z >= $this->minZ and $vector->z <= $this->maxZ;
	}
	public function isVectorInXZ(Vector3 $vector) : bool{
		return $vector->x >= $this->minX and $vector->x <= $this->maxX and $vector->z >= $this->minZ and $vector->z <= $this->maxZ;
	}
	public function isVectorInXY(Vector3 $vector) : bool{
		return $vector->x >= $this->minX and $vector->x <= $this->maxX and $vector->y >= $this->minY and $vector->y <= $this->maxY;
	}
	public function calculateIntercept(Vector3 $pos1, Vector3 $pos2) : ?RayTraceResult{
		$v1 = $pos1->getIntermediateWithXValue($pos2, $this->minX);
		$v2 = $pos1->getIntermediateWithXValue($pos2, $this->maxX);
		$v3 = $pos1->getIntermediateWithYValue($pos2, $this->minY);
		$v4 = $pos1->getIntermediateWithYValue($pos2, $this->maxY);
		$v5 = $pos1->getIntermediateWithZValue($pos2, $this->minZ);
		$v6 = $pos1->getIntermediateWithZValue($pos2, $this->maxZ);
		if($v1 !== null and !$this->isVectorInYZ($v1)){
			$v1 = null;
		}
		if($v2 !== null and !$this->isVectorInYZ($v2)){
			$v2 = null;
		}
		if($v3 !== null and !$this->isVectorInXZ($v3)){
			$v3 = null;
		}
		if($v4 !== null and !$this->isVectorInXZ($v4)){
			$v4 = null;
		}
		if($v5 !== null and !$this->isVectorInXY($v5)){
			$v5 = null;
		}
		if($v6 !== null and !$this->isVectorInXY($v6)){
			$v6 = null;
		}
		$vector = null;
		$distance = PHP_INT_MAX;
		foreach([$v1, $v2, $v3, $v4, $v5, $v6] as $v){
			if($v !== null and ($d = $pos1->distanceSquared($v)) < $distance){
				$vector = $v;
				$distance = $d;
			}
		}
		if($vector === null){
			return null;
		}
		$f = -1;
		if($vector === $v1){
			$f = Vector3::SIDE_WEST;
		}elseif($vector === $v2){
			$f = Vector3::SIDE_EAST;
		}elseif($vector === $v3){
			$f = Vector3::SIDE_DOWN;
		}elseif($vector === $v4){
			$f = Vector3::SIDE_UP;
		}elseif($vector === $v5){
			$f = Vector3::SIDE_NORTH;
		}elseif($vector === $v6){
			$f = Vector3::SIDE_SOUTH;
		}
		return new RayTraceResult($this, $f, $vector);
	}
	public function __toString(){
		return "AxisAlignedBB({$this->minX}, {$this->minY}, {$this->minZ}, {$this->maxX}, {$this->maxY}, {$this->maxZ})";
	}}