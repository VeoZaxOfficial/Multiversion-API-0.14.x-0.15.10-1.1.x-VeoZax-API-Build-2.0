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
namespace pocketmine\level;
use pocketmine\timings\Timings;use pocketmine\timings\TimingsHandler;
class LevelTimings{
	public $setBlock;
	public $doBlockLightUpdates;
	public $doBlockSkyLightUpdates;
	public $doChunkUnload;
	public $doTickPending;
	public $doTickTiles;
	public $doChunkGC;
	public $entityTick;
	public $tileEntityTick;
	public $doTick;
	public $syncChunkSendTimer;
	public $syncChunkSendPrepareTimer;
	public $syncChunkLoadTimer;
	public $syncChunkLoadDataTimer;
	public $syncChunkLoadEntitiesTimer;
	public $syncChunkLoadTileEntitiesTimer;
	public $syncChunkSaveTimer;
	public function __construct(Level $level){
		$name = $level->getFolderName() . " - ";
		$this->setBlock = new TimingsHandler("** " . $name . "setBlock");
		$this->doBlockLightUpdates = new TimingsHandler("** " . $name . "doBlockLightUpdates");
		$this->doBlockSkyLightUpdates = new TimingsHandler("** " . $name . "doBlockSkyLightUpdates");
		$this->doChunkUnload = new TimingsHandler("** " . $name . "doChunkUnload");
		$this->doTickPending = new TimingsHandler("** " . $name . "doTickPending");
		$this->doTickTiles = new TimingsHandler("** " . $name . "doTickTiles");
		$this->doChunkGC = new TimingsHandler("** " . $name . "doChunkGC");
		$this->entityTick = new TimingsHandler("** " . $name . "entityTick");
		$this->tileEntityTick = new TimingsHandler("** " . $name . "tileEntityTick");
		$this->syncChunkSendTimer = new TimingsHandler("** " . $name . "syncChunkSend");
		$this->syncChunkSendPrepareTimer = new TimingsHandler("** " . $name . "syncChunkSendPrepare");
		$this->syncChunkLoadTimer = new TimingsHandler("** " . $name . "syncChunkLoad");
		$this->syncChunkLoadDataTimer = new TimingsHandler("** " . $name . "syncChunkLoad - Data");
		$this->syncChunkLoadEntitiesTimer = new TimingsHandler("** " . $name . "syncChunkLoad - Entities");
		$this->syncChunkLoadTileEntitiesTimer = new TimingsHandler("** " . $name . "syncChunkLoad - TileEntities");
		Timings::init(); 
		$this->syncChunkSaveTimer = new TimingsHandler("** " . $name . "syncChunkSave", Timings::$worldSaveTimer);
		$this->doTick = new TimingsHandler($name . "doTick");
	}}