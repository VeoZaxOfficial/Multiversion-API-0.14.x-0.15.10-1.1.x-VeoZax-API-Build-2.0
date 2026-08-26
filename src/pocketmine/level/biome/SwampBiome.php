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
use pocketmine\block\Block;use pocketmine\block\Flower as FlowerBlock;use pocketmine\level\generator\populator\Flower;use pocketmine\level\generator\populator\Tree;use pocketmine\level\generator\populator\LilyPad;use pocketmine\level\generator\normal\populator\SwampHut;use pocketmine\block\Sapling;
class SwampBiome extends GrassyBiome{
	public function __construct(){
		parent::__construct();
		$flower = new Flower();
		$flower->setBaseAmount(8);
		$flower->addType([Block::RED_FLOWER, FlowerBlock::TYPE_BLUE_ORCHID]);
		$this->addPopulator($flower);
		$Tree = new Tree(Sapling::OAK);
		$Tree->setBaseAmount(1);
		$this->addPopulator($Tree);
		$LilyPad = new LilyPad();
		$LilyPad->setBaseAmount(4);
		$this->addPopulator($LilyPad);
		$SwampHut = new SwampHut();
		$this->addPopulator($SwampHut);
		$this->setElevation(62, 63);
		$this->temperature = 0.8;
		$this->rainfall = 0.9;
	}
	public function getName() : string{
		return "Swamp";
	}
	public function getColor(){
		return 0x6a7039;
	}}