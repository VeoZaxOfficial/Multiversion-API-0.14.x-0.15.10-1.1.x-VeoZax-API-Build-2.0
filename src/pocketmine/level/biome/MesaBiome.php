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

namespace pocketmine\level\biome;
use pocketmine\block\Block;use pocketmine\block\StainedClay;use pocketmine\level\generator\populator\Cactus;use pocketmine\level\generator\populator\DeadBush;
class MesaBiome extends SandyBiome {
	public function __construct(){
		parent::__construct();
		$cactus = new Cactus();
		$cactus->setBaseAmount(0);
		$cactus->setRandomAmount(5);
		$deadBush = new DeadBush();
		$cactus->setBaseAmount(2);
		$deadBush->setRandomAmount(10);
		$this->addPopulator($cactus);
		$this->addPopulator($deadBush);
		$this->setElevation(63, 110);
		$this->temperature = 2.0;
		$this->rainfall = 0.8;
		$this->setGroundCover([
			Block::get(Block::STAINED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::PINK),
			Block::get(Block::STAINED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::ORANGE),
			Block::get(Block::STAINED_CLAY, StainedClay::BLACK),
			Block::get(Block::STAINED_CLAY, StainedClay::GRAY),
			Block::get(Block::STAINED_CLAY, StainedClay::WHITE),
			Block::get(Block::STAINED_CLAY, StainedClay::ORANGE),
			Block::get(Block::STAINED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::YELLOW),
			Block::get(Block::STAINED_CLAY, StainedClay::BLACK),
			Block::get(Block::STAINED_CLAY, StainedClay::PINK),
			Block::get(Block::STAINED_CLAY, StainedClay::PINK),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::STAINED_CLAY, StainedClay::WHITE),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
			Block::get(Block::RED_SANDSTONE, 0),
		]);
	}
	public function getName() : string{
		return "Mesa";
	}} 