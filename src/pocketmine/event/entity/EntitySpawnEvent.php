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
namespace pocketmine\event\entity;
use pocketmine\entity\Creature;use pocketmine\entity\Entity;use pocketmine\entity\Human;use pocketmine\entity\object\ItemEntity;use pocketmine\entity\projectile\Projectile;use pocketmine\entity\Vehicle;use pocketmine\level\Position;
class EntitySpawnEvent extends EntityEvent{
	private $entityType;
	public function __construct(Entity $entity){
		$this->entity = $entity;
		$this->entityType = $entity::NETWORK_ID;
	}
	public function getPosition() : Position{
		return $this->entity->getPosition();
	}
	public function getType() : int{
		return $this->entityType;
	}
	public function isCreature() : bool{
		return $this->entity instanceof Creature;
	}
	public function isHuman() : bool{
		return $this->entity instanceof Human;
	}
	public function isProjectile() : bool{
		return $this->entity instanceof Projectile;
	}
	public function isVehicle() : bool{
		return $this->entity instanceof Vehicle;
	}
	public function isItem() : bool{
		return $this->entity instanceof ItemEntity;
	}}