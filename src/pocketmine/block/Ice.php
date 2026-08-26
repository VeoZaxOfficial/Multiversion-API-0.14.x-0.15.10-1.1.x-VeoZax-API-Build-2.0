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
use pocketmine\item\enchantment\Enchantment;use pocketmine\item\Item;use pocketmine\Player;
class Ice extends Transparent{
	protected $id = self::ICE;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getName() : string{
		return "Ice";
	}
	public function getHardness() : float{
		return 0.5;
	}
	public function getLightFilter() : int{
		return 2;
	}
	public function getFrictionFactor() : float{
		return 0.98;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}
	public function onBreak(Item $item, Player $player = null) : bool{
		if(($player === null or $player->isSurvival()) and !$item->hasEnchantment(Enchantment::SILK_TOUCH)){
			return $this->getLevelNonNull()->setBlock($this, BlockFactory::get(Block::WATER), true);
		}
		return parent::onBreak($item, $player);
	}
	public function ticksRandomly() : bool{
		return true;
	}
	public function onRandomTick() : void{
		if($this->level->getHighestAdjacentBlockLight($this->x, $this->y, $this->z) >= 12){
			$this->level->useBreakOn($this);
		}
	}
	public function getDropsForCompatibleTool(Item $item) : array{
		return [];
	}}