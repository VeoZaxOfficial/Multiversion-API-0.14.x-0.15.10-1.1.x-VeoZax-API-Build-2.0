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
use pocketmine\item\Item;use pocketmine\math\AxisAlignedBB;use pocketmine\math\Vector3;use pocketmine\Player;use pocketmine\tile\Chest as TileChest;use pocketmine\tile\Tile;
class TrappedChest extends RedstoneSource{
	protected $id = self::TRAPPED_CHEST;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getName() : string{
		return "Trapped Chest";
	}
	public function isSolid() : bool{
		return true;
	}
	public function canBeFlowedInto() : bool{
		return false;
	}
	public function getHardness() : float{
		return 2.5;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}
	protected function recalculateBoundingBox() : ?AxisAlignedBB{
		return new AxisAlignedBB(
			$this->x + 0.025,
			$this->y,
			$this->z + 0.025,
			$this->x + 0.975,
			$this->y + 0.95,
			$this->z + 0.975
		);
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3
		];
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		Tile::createTile(Tile::CHEST, $this->getLevelNonNull(), TileChest::createNBT($this, $face, $item, $player));
		return true;
	}
	public function onActivate(Item $item, Player $player = null) : bool{
		if($player instanceof Player){
			$t = $this->getLevelNonNull()->getTile($this);
			$chest = null;
			if($t instanceof TileChest){
				$chest = $t;
			}else{
				$chest = Tile::createTile(Tile::CHEST, $this->getLevelNonNull(), TileChest::createNBT($this));
				if(!($chest instanceof TileChest)){
					return true;
				}
			}
			if(
				!$this->getSide(Vector3::SIDE_UP)->isTransparent() or
				!$chest->canOpenWith($item->getCustomName())
			){
				return true;
			}
			$player->addWindow($chest->getInventory());
		}
		return true;
	}
	public function getVariantBitmask() : int{
		return 0;
	}
	public function getFuelTime() : int{
		return 300;
	}}