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
namespace pocketmine\block;
use InvalidArgumentException;use pocketmine\item\Item;use pocketmine\math\Vector3;use pocketmine\Player;use function array_map;use function array_reverse;use function array_search;use function array_shift;use function count;use function implode;use function in_array;
abstract class BaseRail extends Flowable{
	public const STRAIGHT_NORTH_SOUTH = 0;
	public const STRAIGHT_EAST_WEST = 1;
	public const ASCENDING_EAST = 2;
	public const ASCENDING_WEST = 3;
	public const ASCENDING_NORTH = 4;
	public const ASCENDING_SOUTH = 5;
	private const ASCENDING_SIDES = [
		self::ASCENDING_NORTH => Vector3::SIDE_NORTH,
		self::ASCENDING_EAST => Vector3::SIDE_EAST,
		self::ASCENDING_SOUTH => Vector3::SIDE_SOUTH,
		self::ASCENDING_WEST => Vector3::SIDE_WEST
	];
	protected const FLAG_ASCEND = 1 << 24; 
	protected const CONNECTIONS = [
		self::STRAIGHT_NORTH_SOUTH => [
			Vector3::SIDE_NORTH,
			Vector3::SIDE_SOUTH
		],
		self::STRAIGHT_EAST_WEST => [
			Vector3::SIDE_EAST,
			Vector3::SIDE_WEST
		],
		self::ASCENDING_EAST => [
			Vector3::SIDE_WEST,
			Vector3::SIDE_EAST | self::FLAG_ASCEND
		],
		self::ASCENDING_WEST => [
			Vector3::SIDE_EAST,
			Vector3::SIDE_WEST | self::FLAG_ASCEND
		],
		self::ASCENDING_NORTH => [
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_NORTH | self::FLAG_ASCEND
		],
		self::ASCENDING_SOUTH => [
			Vector3::SIDE_NORTH,
			Vector3::SIDE_SOUTH | self::FLAG_ASCEND
		]
	];
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getHardness() : float{
		return 0.7;
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if(!$blockReplace->getSide(Vector3::SIDE_DOWN)->isTransparent() and $this->getLevelNonNull()->setBlock($blockReplace, $this, true, true)){
			$this->tryReconnect();
			return true;
		}
		return false;
	}
	protected static function searchState(array $connections, array $lookup) : int{
		$meta = array_search($connections, $lookup, true);
		if($meta === false){
			$meta = array_search(array_reverse($connections), $lookup, true);
		}
		if($meta === false){
			throw new InvalidArgumentException("No meta value matches connections " . implode(", ", array_map('\dechex', $connections)));
		}
		return $meta;
	}
	protected function getMetaForState(array $connections) : int{
		return self::searchState($connections, self::CONNECTIONS);
	}
	abstract protected function getConnectionsForState() : array;
	private function getConnectedDirections() : array{
		$connections = [];
		foreach($this->getConnectionsForState() as $connection){
			$other = $this->getSide($connection & ~self::FLAG_ASCEND);
			$otherConnection = Vector3::getOppositeSide($connection & ~self::FLAG_ASCEND);
			if(($connection & self::FLAG_ASCEND) !== 0){
				$other = $other->getSide(Vector3::SIDE_UP);
			}elseif(!($other instanceof BaseRail)){ 
				$other = $other->getSide(Vector3::SIDE_DOWN);
				$otherConnection |= self::FLAG_ASCEND;
			}
			if(
				$other instanceof BaseRail and
				in_array($otherConnection, $other->getConnectionsForState(), true)
			){
				$connections[] = $connection;
			}
		}
		return $connections;
	}
	private function getPossibleConnectionDirections(array $constraints) : array{
		switch(count($constraints)){
			case 0:
				$possible = [
					Vector3::SIDE_NORTH => true,
					Vector3::SIDE_SOUTH => true,
					Vector3::SIDE_WEST => true,
					Vector3::SIDE_EAST => true
				];
				foreach($possible as $p => $_){
					$possible[$p | self::FLAG_ASCEND] = true;
				}
				return $possible;
			case 1:
				return $this->getPossibleConnectionDirectionsOneConstraint(array_shift($constraints));
			case 2:
				return [];
			default:
				throw new InvalidArgumentException("Expected at most 2 constraints, got " . count($constraints));
		}
	}
	protected function getPossibleConnectionDirectionsOneConstraint(int $constraint) : array{
		$opposite = Vector3::getOppositeSide($constraint & ~self::FLAG_ASCEND);
		$possible = [$opposite => true];
		if(($constraint & self::FLAG_ASCEND) === 0){
			$possible[$opposite | self::FLAG_ASCEND] = true;
		}
		return $possible;
	}
	private function tryReconnect() : void{
		$thisConnections = $this->getConnectedDirections();
		$changed = false;
		do{
			$possible = $this->getPossibleConnectionDirections($thisConnections);
			$continue = false;
			foreach($possible as $thisSide => $_){
				$otherSide = Vector3::getOppositeSide($thisSide & ~self::FLAG_ASCEND);
				$other = $this->getSide($thisSide & ~self::FLAG_ASCEND);
				if(($thisSide & self::FLAG_ASCEND) !== 0){
					$other = $other->getSide(Vector3::SIDE_UP);
				}elseif(!($other instanceof BaseRail)){ 
					$other = $other->getSide(Vector3::SIDE_DOWN);
					$otherSide |= self::FLAG_ASCEND;
				}
				if(!($other instanceof BaseRail) or count($otherConnections = $other->getConnectedDirections()) >= 2){
					continue;
				}
				$otherPossible = $other->getPossibleConnectionDirections($otherConnections);
				if(isset($otherPossible[$otherSide])){
					$otherConnections[] = $otherSide;
					$other->updateState($otherConnections);
					$changed = true;
					$thisConnections[] = $thisSide;
					$continue = count($thisConnections) < 2;
					break; 
				}
			}
		}while($continue);
		if($changed){
			$this->updateState($thisConnections);
		}
	}
	private function updateState(array $connections) : void{
		if(count($connections) === 1){
			$connections[] = Vector3::getOppositeSide($connections[0] & ~self::FLAG_ASCEND);
		}elseif(count($connections) !== 2){
			throw new InvalidArgumentException("Expected exactly 2 connections, got " . count($connections));
		}
		$this->meta = $this->getMetaForState($connections);
		$this->level->setBlock($this, $this, false, false); 
	}
	public function onNearbyBlockChange() : void{
		if($this->getSide(Vector3::SIDE_DOWN)->isTransparent() or (
			isset(self::ASCENDING_SIDES[$this->meta & 0x07]) and
			$this->getSide(self::ASCENDING_SIDES[$this->meta & 0x07])->isTransparent()
		)){
			$this->getLevelNonNull()->useBreakOn($this);
		}
	}
	public function getVariantBitmask() : int{
		return 0;
	}}