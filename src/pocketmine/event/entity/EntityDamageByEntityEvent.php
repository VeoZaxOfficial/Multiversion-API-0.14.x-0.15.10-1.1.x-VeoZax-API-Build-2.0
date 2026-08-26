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
use pocketmine\entity\Effect;use pocketmine\entity\Entity;use pocketmine\entity\Living;
class EntityDamageByEntityEvent extends EntityDamageEvent{
	private $damagerEntityId;
	private $knockBack;
	public function __construct(Entity $damager, Entity $entity, int $cause, float $damage, array $modifiers = [], float $knockBack = 0.4){
		$this->damagerEntityId = $damager->getId();
		$this->knockBack = $knockBack;
		parent::__construct($entity, $cause, $damage, $modifiers);
		$this->addAttackerModifiers($damager);
	}
	protected function addAttackerModifiers(Entity $damager) : void{
		if($damager instanceof Living){ 
			if($damager->hasEffect(Effect::STRENGTH)){
				$this->setModifier($this->getBaseDamage() * 0.3 * $damager->getEffect(Effect::STRENGTH)->getEffectLevel(), self::MODIFIER_STRENGTH);
			}
			if($damager->hasEffect(Effect::WEAKNESS)){
				$this->setModifier(-($this->getBaseDamage() * 0.2 * $damager->getEffect(Effect::WEAKNESS)->getEffectLevel()), self::MODIFIER_WEAKNESS);
			}
		}
	}
	public function getDamager() : ?Entity{
		return $this->getEntity()->getLevelNonNull()->getServer()->findEntity($this->damagerEntityId);
	}
	public function getKnockBack() : float{
		return $this->knockBack;
	}
	public function setKnockBack(float $knockBack) : void{
		$this->knockBack = $knockBack;
	}}