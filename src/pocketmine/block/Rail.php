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
use InvalidArgumentException;use pocketmine\math\Vector3;
class Rail extends BaseRail{
	public const CURVE_SOUTHEAST = 6;
	public const CURVE_SOUTHWEST = 7;
	public const CURVE_NORTHWEST = 8;
	public const CURVE_NORTHEAST = 9;
	private const CURVE_CONNECTIONS = [
		self::CURVE_SOUTHEAST => [
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_EAST
		],
		self::CURVE_SOUTHWEST => [
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_WEST
		],
		self::CURVE_NORTHWEST => [
			Vector3::SIDE_NORTH,
			Vector3::SIDE_WEST
		],
		self::CURVE_NORTHEAST => [
			Vector3::SIDE_NORTH,
			Vector3::SIDE_EAST
		]
	];
	protected $id = self::RAIL;
	public function getName() : string{
		return "Rail";
	}
	protected function getMetaForState(array $connections) : int{
		try{
			return self::searchState($connections, self::CURVE_CONNECTIONS);
		}catch(InvalidArgumentException $e){
			return parent::getMetaForState($connections);
		}
	}
	protected function getConnectionsForState() : array{
		return self::CURVE_CONNECTIONS[$this->meta] ?? self::CONNECTIONS[$this->meta];
	}
	protected function getPossibleConnectionDirectionsOneConstraint(int $constraint) : array{
		static $horizontal = [
			Vector3::SIDE_NORTH,
			Vector3::SIDE_SOUTH,
			Vector3::SIDE_WEST,
			Vector3::SIDE_EAST
		];
		$possible = parent::getPossibleConnectionDirectionsOneConstraint($constraint);
		if(($constraint & self::FLAG_ASCEND) === 0){
			foreach($horizontal as $d){
				if($constraint !== $d){
					$possible[$d] = true;
				}
			}
		}
		return $possible;
	}}