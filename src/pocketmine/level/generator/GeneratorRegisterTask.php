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
namespace pocketmine\level\generator;
use pocketmine\block\BlockFactory;use pocketmine\item\ItemFactory;use pocketmine\level\biome\Biome;use pocketmine\level\Level;use pocketmine\level\SimpleChunkManager;use pocketmine\scheduler\AsyncTask;use pocketmine\utils\Random;use function serialize;use function unserialize;
class GeneratorRegisterTask extends AsyncTask{
	public $generatorClass;
	public $settings;
	public $seed;
	public $levelId;
	public $worldHeight = Level::Y_MAX;
	public function __construct(Level $level, string $generatorClass, array $generatorSettings = []){
		$this->generatorClass = $generatorClass;
		$this->settings = serialize($generatorSettings);
		$this->seed = $level->getSeed();
		$this->levelId = $level->getId();
		$this->worldHeight = $level->getWorldHeight();
	}
	public function onRun() : void{
		BlockFactory::init();
		ItemFactory::init();
		Biome::init();
		$manager = new SimpleChunkManager($this->seed, $this->worldHeight);
		ThreadLocalManagerContext::register(new ThreadLocalManagerContext($manager), $this->levelId);
		$generator = new $this->generatorClass(unserialize($this->settings));
		$generator->init($manager, new Random($manager->getSeed()));
		ThreadLocalGeneratorContext::register(new ThreadLocalGeneratorContext($generator), $this->levelId);
	}}