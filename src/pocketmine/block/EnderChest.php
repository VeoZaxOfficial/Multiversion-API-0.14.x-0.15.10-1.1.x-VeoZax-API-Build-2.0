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
use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\item\TieredTool;use pocketmine\math\Vector3;use pocketmine\Player;use pocketmine\tile\EnderChest as TileEnderChest;use pocketmine\tile\Tile;
class EnderChest extends Chest{
	protected $id = self::ENDER_CHEST;
	public function getHardness() : float{
		return 22.5;
	}
	public function getBlastResistance() : float{
		return 3000;
	}
	public function getLightLevel() : int{
		return 7;
	}
	public function getName() : string{
		return "Ender Chest";
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}
	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevelNonNull()->setBlock($blockReplace, $this, true, true);
		Tile::createTile(Tile::ENDER_CHEST, $this->getLevelNonNull(), TileEnderChest::createNBT($this, $face, $item, $player));
		return true;
	}
	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			$t = $this->getLevelNonNull()->getTile($this);
			$enderChest = null;
			if($t instanceof TileEnderChest){
				$enderChest = $t;
			}else{
				$enderChest = Tile::createTile(Tile::ENDER_CHEST, $this->getLevelNonNull(), TileEnderChest::createNBT($this));
			}
			if(!$this->getSide(Vector3::SIDE_UP)->isTransparent()){
				return true;
			}
			$player->getEnderChestInventory()->setHolderPosition($enderChest);
			$player->addWindow($player->getEnderChestInventory());
		}
		return true;
	}
	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			ItemFactory::get(Item::OBSIDIAN, 0, 8)
		];
	}
	public function getFuelTime() : int{
		return 0;
	}}