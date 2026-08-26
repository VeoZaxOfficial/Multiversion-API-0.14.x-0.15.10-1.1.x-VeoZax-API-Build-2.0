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
namespace pocketmine\maps;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;use pocketmine\Player;use function max;use function min;
class MapInfo{
	public $player;
	public $packetSendTimer = 0;
	public $dirty = true;
	public $minX = 0;
	public $minY = 0;
	public $maxX = 127;
	public $maxY = 127;
	public function __construct(Player $player){
		$this->player = $player;
	}
	public function getPacket(MapData $mapData) : ?ClientboundMapItemDataPacket{
		if($this->dirty){
			$this->dirty = false;
			$pk = new ClientboundMapItemDataPacket();
			$pk->originX = $pk->originY = $pk->originZ = 0;
			$pk->height = $pk->width = 128;
			$pk->dimensionId = $mapData->getDimension();
			$pk->scale = $mapData->getScale();
			$pk->colors = $mapData->getColors();
			$pk->mapId = $mapData->getId();
			$pk->decorations = $mapData->getDecorations();
			$pk->trackedEntities = $mapData->getTrackedObjects();
			$pk->cropTexture($this->minX, $this->minY, $this->maxX + 1 - $this->minX, $this->maxY + 1 - $this->minY);
			return $pk;
		}elseif(($this->packetSendTimer++ % 5) === 0){ 
			$pk = new ClientboundMapItemDataPacket();
			$pk->originX = $pk->originY = $pk->originZ = 0;
			$pk->height = $pk->width = 128;
			$pk->dimensionId = $mapData->getDimension();
			$pk->scale = $mapData->getScale();
			$pk->mapId = $mapData->getId();
			$pk->decorations = $mapData->getDecorations();
			$pk->trackedEntities = $mapData->getTrackedObjects();
			return $pk;
		}
		return null;
	}
	public function updateTextureAt(int $x, int $y) : void{
		if($this->dirty){
			$this->minX = min($this->minX, $x);
			$this->minY = min($this->minY, $y);
			$this->maxX = max($this->maxX, $x);
			$this->maxY = max($this->maxY, $y);
		}else{
			$this->dirty = true;
			$this->minX = $x;
			$this->minY = $y;
			$this->maxX = $x;
			$this->maxY = $y;
		}
	}}