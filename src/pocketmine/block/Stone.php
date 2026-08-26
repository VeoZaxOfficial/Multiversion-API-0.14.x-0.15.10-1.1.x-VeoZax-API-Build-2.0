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
use pocketmine\item\Item;use pocketmine\item\ItemFactory;use pocketmine\item\TieredTool;
class Stone extends Solid{
	public const NORMAL = 0;
	public const GRANITE = 1;
	public const POLISHED_GRANITE = 2;
	public const DIORITE = 3;
	public const POLISHED_DIORITE = 4;
	public const ANDESITE = 5;
	public const POLISHED_ANDESITE = 6;
	protected $id = self::STONE;
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	public function getHardness() : float{
		return 1.5;
	}
	public function getToolType() : int{
		return BlockToolType::TYPE_PICKAXE;
	}
	public function getToolHarvestLevel() : int{
		return TieredTool::TIER_WOODEN;
	}
	public function getName() : string{
		static $names = [
			self::NORMAL => "Stone",
			self::GRANITE => "Granite",
			self::POLISHED_GRANITE => "Polished Granite",
			self::DIORITE => "Diorite",
			self::POLISHED_DIORITE => "Polished Diorite",
			self::ANDESITE => "Andesite",
			self::POLISHED_ANDESITE => "Polished Andesite"
		];
		return $names[$this->getVariant()] ?? "Unknown";
	}
	public function getDropsForCompatibleTool(Item $item) : array{
		if($this->getDamage() === self::NORMAL){
			return [
				ItemFactory::get(Item::COBBLESTONE, $this->getDamage())
			];
		}
		return parent::getDropsForCompatibleTool($item);
	}}