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
use pocketmine\item\Item;use pocketmine\item\SpawnEgg;use pocketmine\item\TieredTool;use pocketmine\Player;use pocketmine\tile\MobSpawner;use pocketmine\tile\Tile;use function mt_rand;
class MonsterSpawner extends Transparent{
	protected $id = self::MONSTER_SPAWNER;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getHardness() : float{
		return 5;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}
	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
	public function getName() : string{
		return "Monster Spawner";
	}
	public function getDropsForCompatibleTool(Item $item) : array{
		return [];
	}
	public function isAffectedBySilkTouch() : bool{
		return false;
	}
	protected function getXpDropAmount() : int{
		return mt_rand(15, 43);
	}
	public function onActivate(Item $item, Player $player = null) : bool{
		if($item instanceof SpawnEgg){
			$tile = Tile::createTile(Tile::MOB_SPAWNER, $this->level, MobSpawner::createNBT($this));
			$tile->setEntityId($item->getDamage());
			if($player instanceof Player){
				$item->pop();
				$player->getInventory()->setItemInHand($item);
			}
		}
		return true;
	}}