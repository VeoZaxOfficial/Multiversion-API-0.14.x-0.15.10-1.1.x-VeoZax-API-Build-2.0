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
namespace pocketmine\item;
use pocketmine\block\Block;use pocketmine\entity\Entity;use pocketmine\entity\object\Painting;use pocketmine\entity\object\PaintingMotive;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\LevelEventPacket;use pocketmine\Player;use function array_rand;
class PaintingItem extends Item{
	public function __construct(int $meta = 0){
		parent::__construct(self::PAINTING, $meta, "Painting");
	}
	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : bool{
		if($face === Vector3::SIDE_DOWN or $face === Vector3::SIDE_UP){
			return false;
		}
		$motives = [];
		$totalDimension = 0;
		foreach(PaintingMotive::getAll() as $motive){
			$currentTotalDimension = $motive->getHeight() + $motive->getWidth();
			if($currentTotalDimension < $totalDimension){
				continue;
			}
			if(Painting::canFit($player->level, $blockReplace, $face, true, $motive)){
				if($currentTotalDimension > $totalDimension){
					$totalDimension = $currentTotalDimension;
					$motives = [];
				}
				$motives[] = $motive;
			}
		}
		if(empty($motives)){ 
			return false;
		}
		$motive = $motives[array_rand($motives)];
		static $directions = [
			Vector3::SIDE_SOUTH => 0,
			Vector3::SIDE_WEST => 1,
			Vector3::SIDE_NORTH => 2,
			Vector3::SIDE_EAST => 3
		];
		$direction = $directions[$face] ?? -1;
		if($direction === -1){
			return false;
		}
		$nbt = Entity::createBaseNBT($blockReplace, null, $direction * 90, 0);
		$nbt->setByte("Direction", $direction);
		$nbt->setString("Motive", $motive->getName());
		$nbt->setInt("TileX", $blockClicked->getFloorX());
		$nbt->setInt("TileY", $blockClicked->getFloorY());
		$nbt->setInt("TileZ", $blockClicked->getFloorZ());
		$entity = Entity::createEntity("Painting", $blockReplace->getLevelNonNull(), $nbt);
		if($entity instanceof Entity){
			--$this->count;
			$entity->spawnToAll();
			$player->getLevelNonNull()->broadcastLevelEvent($blockReplace->add(0.5, 0.5, 0.5), LevelEventPacket::EVENT_SOUND_ITEMFRAME_PLACE); 
			return true;
		}
		return false;
	}}