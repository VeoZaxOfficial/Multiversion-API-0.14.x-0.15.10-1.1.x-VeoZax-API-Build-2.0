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
use pocketmine\item\Item;use pocketmine\item\TieredTool;use pocketmine\math\Vector3;use pocketmine\Player;use pocketmine\tile\BrewingStand as BrewingStandTile;
class BrewingStand extends Transparent{
	protected $id = self::BREWING_STAND_BLOCK;
	protected $itemId = Item::BREWING_STAND;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getName() : string{
		return "Brewing Stand";
	}
	public function getHardness() : float{
		return 0.5;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}
	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
	public function getVariantBitmask() : int{
		return 0;
	}
	public function getLightLevel() : int{
		return 1;
	}
	public function getBlastResistance() : float{
		return 2.5;
	}
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$this->getLevelNonNull()->setBlock($blockReplace, $this, true, true);
		$nbt = BrewingStandTile::createNBT($this, $face, $item, $player);
		$nbt->setInt(BrewingStandTile::TAG_BREW_TIME, BrewingStandTile::MAX_BREW_TIME);
		if($item->hasCustomName()){
			$nbt->setString("CustomName", $item->getCustomName());
		}
		new BrewingStandTile($this->getLevelNonNull(), $nbt);
		return true;
	}
	public function onActivate(Item $item, Player $player = null): bool{
	    if($player instanceof Player){
	    	$tile = $player->getLevelNonNull()->getTile($this);
	    	if($tile instanceof BrewingStandTile){
		    	$player->addWindow($tile->getInventory());
	    	}else{
		    	$nbt = BrewingStandTile::createNBT($this, null, $item, $player);
		    	$nbt->setInt(BrewingStandTile::TAG_BREW_TIME, BrewingStandTile::MAX_BREW_TIME);
		    	if($item->hasCustomName()){
			    	$nbt->setString("CustomName", $item->getCustomName());
		    	}
		    	$tile = new BrewingStandTile($this->getLevelNonNull(), $nbt);
		    	$player->addWindow($tile->getInventory());
	    	}
		}
		return true;
	}}