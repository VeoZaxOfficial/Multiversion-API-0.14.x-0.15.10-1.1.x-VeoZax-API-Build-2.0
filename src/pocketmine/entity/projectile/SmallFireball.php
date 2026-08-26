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
namespace pocketmine\entity\projectile;
use pocketmine\block\Air;use pocketmine\block\Block;use pocketmine\block\BlockFactory;use pocketmine\entity\Living;use pocketmine\math\RayTraceResult;
class SmallFireball extends Projectile{
	public const NETWORK_ID = self::SMALL_FIREBALL;
	public $height = 0.3125;
	public $width = 0.3125;
	protected $damage = 5.0;
	protected $life = 0;
	public function getName() : string{
		return "SmallFireball";
	}
	public function initEntity() : void{
		parent::initEntity();
		$this->life = $this->namedtag->getInt("life", 0);
	}
	public function onUpdate(int $currentTick) : bool{
		if($this->isAlive() and !$this->closed and !$this->isFlaggedForDespawn()){
			$this->setOnFire(1);
			if($this->life++ > 600){
				$this->flagForDespawn();
			}
		}
		return parent::onUpdate($currentTick);
	}
	public function onHitBlock(Block $blockHit, RayTraceResult $hitResult) : void{
		parent::onHitBlock($blockHit, $hitResult);
		$this->flagForDespawn();
		$owner = $this->getOwningEntity();
		if($owner instanceof Living){
			$block = $this->level->getBlock($this);
			if($block instanceof Air){
				$this->level->setBlock($this, BlockFactory::get(Block::FIRE));
			}
		}
	}
	public function saveNBT() : void{
		parent::saveNBT();
		$this->namedtag->setInt("life", $this->life);
	}}