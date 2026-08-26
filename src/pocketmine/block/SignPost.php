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
use pocketmine\item\Item;use pocketmine\math\AxisAlignedBB;use pocketmine\math\Vector3;use pocketmine\network\mcpe\protocol\ProtocolInfo;use pocketmine\Player;use pocketmine\tile\Sign as TileSign;use pocketmine\tile\Tile;use function floor;
class SignPost extends Transparent{
	protected $id = self::SIGN_POST;
	protected $itemId = Item::SIGN;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getHardness() : float{
		return 1;
	}
	public function isSolid() : bool{
		return false;
	}
	public function getName() : string{
		return "Sign Post";
	}
	protected function recalculateBoundingBox() : ?AxisAlignedBB{
		return null;
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		if($face !== Vector3::SIDE_DOWN){
			if($face === Vector3::SIDE_UP){
				$this->meta = $player !== null ? (floor((($player->yaw + 180) * 16 / 360) + 0.5) & 0x0f) : 0;
				$this->getLevelNonNull()->setBlock($blockReplace, $this, true);
			}else{
				$this->meta = $face;
				$this->getLevelNonNull()->setBlock($blockReplace, BlockFactory::get(Block::WALL_SIGN, $this->meta), true);
			}
			$tile = Tile::createTile(Tile::SIGN, $this->getLevelNonNull(), TileSign::createNBT($this, $face, $item, $player));
			if($player !== null){
				$tile->setEditorEntityRuntimeId($player->getId());
				
			}
			return true;
		}
		return false;
	}
	public function onNearbyBlockChange() : void{
		if($this->getSide(Vector3::SIDE_DOWN)->getId() === self::AIR){
			$this->getLevelNonNull()->useBreakOn($this);
		}
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_AXE;
	}
	public function getVariantBitmask() : int{
		return 0;
	}}