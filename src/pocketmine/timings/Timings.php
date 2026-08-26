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
namespace pocketmine\timings;
use pocketmine\entity\Entity;use pocketmine\network\mcpe\protocol\DataPacket;use pocketmine\Player;use pocketmine\scheduler\TaskHandler;use pocketmine\tile\Tile;use ReflectionClass;use function dechex;
abstract class Timings{
	public static $fullTickTimer;
	public static $serverTickTimer;
	public static $memoryManagerTimer;
	public static $garbageCollectorTimer;
	public static $titleTickTimer;
	public static $playerNetworkTimer;
	public static $playerNetworkReceiveTimer;
	public static $playerChunkOrderTimer;
	public static $playerChunkSendTimer;
	public static $connectionTimer;
	public static $schedulerTimer;
	public static $serverCommandTimer;
	public static $worldSaveTimer;
	public static $populationTimer;
	public static $generationCallbackTimer;
	public static $permissibleCalculationTimer;
	public static $permissionDefaultTimer;
	public static $entityMoveTimer;
	public static $mobPathFindingTimer;
	public static $mobNavigationUpdateTimer;
	public static $mobBehaviorUpdateTimer;
	public static $playerCheckNearEntitiesTimer;
	public static $tickEntityTimer;
	public static $tickTileEntityTimer;
	public static $timerEntityBaseTick;
	public static $timerLivingEntityBaseTick;
	public static $schedulerSyncTimer;
	public static $schedulerAsyncTimer;
	public static $playerCommandTimer;
	public static $craftingDataCacheRebuildTimer;
	public static $entityTypeTimingMap = [];
	public static $tileEntityTypeTimingMap = [];
	public static $packetReceiveTimingMap = [];
	public static $packetSendTimingMap = [];
	public static $pluginTaskTimingMap = [];
	public static function init(){
		if(self::$serverTickTimer instanceof TimingsHandler){
			return;
		}
		self::$fullTickTimer = new TimingsHandler("Full Server Tick");
		self::$serverTickTimer = new TimingsHandler("** Full Server Tick", self::$fullTickTimer);
		self::$memoryManagerTimer = new TimingsHandler("Memory Manager");
		self::$garbageCollectorTimer = new TimingsHandler("Garbage Collector", self::$memoryManagerTimer);
		self::$titleTickTimer = new TimingsHandler("Console Title Tick");
		self::$playerNetworkTimer = new TimingsHandler("Player Network Send");
		self::$playerNetworkReceiveTimer = new TimingsHandler("Player Network Receive");
		self::$playerChunkOrderTimer = new TimingsHandler("Player Order Chunks");
		self::$playerChunkSendTimer = new TimingsHandler("Player Send Chunks");
		self::$connectionTimer = new TimingsHandler("Connection Handler");
		self::$schedulerTimer = new TimingsHandler("Scheduler");
		self::$serverCommandTimer = new TimingsHandler("Server Command");
		self::$worldSaveTimer = new TimingsHandler("World Save");
		self::$populationTimer = new TimingsHandler("World Population");
		self::$generationCallbackTimer = new TimingsHandler("World Generation Callback");
		self::$permissibleCalculationTimer = new TimingsHandler("Permissible Calculation");
		self::$permissionDefaultTimer = new TimingsHandler("Default Permission Calculation");
		self::$entityMoveTimer = new TimingsHandler("** entityMove");
		self::$mobPathFindingTimer = new TimingsHandler("** mobPathFinding");
		self::$mobNavigationUpdateTimer = new TimingsHandler("** mobNavigationUpdate");
		self::$mobBehaviorUpdateTimer = new TimingsHandler("** mobBehaviorUpdate");
		self::$playerCheckNearEntitiesTimer = new TimingsHandler("** checkNearEntities");
		self::$tickEntityTimer = new TimingsHandler("** tickEntity");
		self::$tickTileEntityTimer = new TimingsHandler("** tickTileEntity");
		self::$timerEntityBaseTick = new TimingsHandler("** entityBaseTick");
		self::$timerLivingEntityBaseTick = new TimingsHandler("** livingEntityBaseTick");
		self::$schedulerSyncTimer = new TimingsHandler("** Scheduler - Sync Tasks");
		self::$schedulerAsyncTimer = new TimingsHandler("** Scheduler - Async Tasks");
		self::$playerCommandTimer = new TimingsHandler("** playerCommand");
		self::$craftingDataCacheRebuildTimer = new TimingsHandler("** craftingDataCacheRebuild");
	}
	public static function getScheduledTaskTimings(TaskHandler $task, int $period) : TimingsHandler{
		$name = "Task: " . ($task->getOwnerName() ?? "Unknown") . " Runnable: " . $task->getTaskName();
		if($period > 0){
			$name .= "(interval:" . $period . ")";
		}else{
			$name .= "(Single)";
		}
		if(!isset(self::$pluginTaskTimingMap[$name])){
			self::$pluginTaskTimingMap[$name] = new TimingsHandler($name, self::$schedulerSyncTimer);
		}
		return self::$pluginTaskTimingMap[$name];
	}
	public static function getEntityTimings(Entity $entity) : TimingsHandler{
		$entityType = (new ReflectionClass($entity))->getShortName();
		if(!isset(self::$entityTypeTimingMap[$entityType])){
			if($entity instanceof Player){
				self::$entityTypeTimingMap[$entityType] = new TimingsHandler("** tickEntity - EntityPlayer", self::$tickEntityTimer);
			}else{
				self::$entityTypeTimingMap[$entityType] = new TimingsHandler("** tickEntity - " . $entityType, self::$tickEntityTimer);
			}
		}
		return self::$entityTypeTimingMap[$entityType];
	}
	public static function getTileEntityTimings(Tile $tile) : TimingsHandler{
		$tileType = (new ReflectionClass($tile))->getShortName();
		if(!isset(self::$tileEntityTypeTimingMap[$tileType])){
			self::$tileEntityTypeTimingMap[$tileType] = new TimingsHandler("** tickTileEntity - " . $tileType, self::$tickTileEntityTimer);
		}
		return self::$tileEntityTypeTimingMap[$tileType];
	}
	public static function getReceiveDataPacketTimings(DataPacket $pk) : TimingsHandler{
		if(!isset(self::$packetReceiveTimingMap[$pk::NETWORK_ID])){
			$pkName = (new ReflectionClass($pk))->getShortName();
			self::$packetReceiveTimingMap[$pk::NETWORK_ID] = new TimingsHandler("** receivePacket - " . $pkName . " [0x" . dechex($pk::NETWORK_ID) . "]", self::$playerNetworkReceiveTimer);
		}
		return self::$packetReceiveTimingMap[$pk::NETWORK_ID];
	}
	public static function getSendDataPacketTimings($pk) : TimingsHandler{
		if(!isset(self::$packetSendTimingMap[$pk::NETWORK_ID])){
			$pkName = (new ReflectionClass($pk))->getShortName();
			self::$packetSendTimingMap[$pk::NETWORK_ID] = new TimingsHandler("** sendPacket - " . $pkName . " [0x" . dechex($pk::NETWORK_ID) . "]", self::$playerNetworkTimer);
		}
		return self::$packetSendTimingMap[$pk::NETWORK_ID];
	}}