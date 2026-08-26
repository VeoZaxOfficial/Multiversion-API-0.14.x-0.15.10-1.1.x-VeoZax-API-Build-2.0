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


declare(strict_types = 1);
namespace pocketmine\entity\object;
use pocketmine\block\Liquid;use pocketmine\entity\{Entity, Living};use pocketmine\entity\hostile\Creeper;use pocketmine\event\entity\EntityDamageByEntityEvent;use pocketmine\item\Item;use pocketmine\math\AxisAlignedBB;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\PlaySoundPacket;
class Lightning extends Entity{
	public const NETWORK_ID = self::LIGHTNING_BOLT;
	public $width = 0.3;
	public $height = 1.8;
	protected $age = 0;
	public $doneDamage = false;
	public function onUpdate(int $currentTick): bool{
		if(!$this->doneDamage){
			$this->doneDamage = true;
			if($this->getLevelNonNull()->getServer()->lightningFire){
				$fire = Item::get(Item::FIRE)->getBlock();
				$oldBlock = $this->getLevelNonNull()->getBlock($this);
				if($oldBlock instanceof Liquid){
				}elseif($oldBlock->isSolid()){
					$v3 = new Vector3($this->x, $this->y + 1, $this->z);
				}else{
					$v3 = new Vector3($this->x, $this->y, $this->z);
				}
				$fire->setDamage(11); 
				if(isset($v3)) $this->getLevelNonNull()->setBlock($v3, $fire);
				foreach($this->level->getNearbyEntities($this->growAxis($this->boundingBox, 6, 6, 6), $this) as $entity){
					if($entity instanceof Living){
						$distance = $this->distance($entity);
						$distance = ($distance > 0 ? $distance : 1);
						$k = 5;
						$damage = $k / $distance;
						$ev = new EntityDamageByEntityEvent($this, $entity, 16, $damage); 
						$entity->attack($ev);
						$entity->setOnFire(mt_rand(3, 8));
					}
					if($entity instanceof Creeper){
						$entity->setPowered(true);
					}
				}
			}
			$spk = new PlaySoundPacket();
			$spk->soundName = "ambient.weather.lightning.impact";
			$spk->x = $this->getX();
			$spk->y = $this->getY();
			$spk->z = $this->getZ();
			$spk->volume = 500;
			$spk->pitch = 1;
			foreach($this->level->getPlayers() as $p){
				$p->sendDataPacket(clone $spk);
			}
		}
		if($this->age > 6 * 20){
			$this->flagForDespawn();
		}
		$this->age++;
		return parent::onUpdate($currentTick);
	}
	private function growAxis(AxisAlignedBB $axis, $x, $y, $z){
		return new AxisAlignedBB($axis->minX - $x, $axis->minY - $y, $axis->minZ - $z, $axis->maxX + $x, $axis->maxY + $y, $axis->maxZ + $z);
	}}