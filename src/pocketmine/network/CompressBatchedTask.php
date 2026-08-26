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
namespace pocketmine\network;
use pocketmine\network\mcpe\protocol\BatchPacket;use pocketmine\Player;use pocketmine\scheduler\AsyncTask;use pocketmine\Server;
class CompressBatchedTask extends AsyncTask{
	public $level = 7;
	public $data;
	public $playersProtocol;
	public function __construct(BatchPacket $batch, array $targets, int $playersProtocol){
		$this->data = $batch->payload;
		$this->level = $batch->getCompressionLevel();
		$this->playersProtocol = $playersProtocol;
		$this->storeLocal($targets);
	}
	public function onRun() : void{
		$batch = new BatchPacket();
		$batch->payload = $this->data;
		$this->data = null;
		$batch->setCompressionLevel($this->level);
		$batch->setProtocol($this->playersProtocol);
		$batch->encode();
		$this->setResult($batch->buffer);
	}
	public function onCompletion(Server $server){
		$pk = new BatchPacket($this->getResult());
		$pk->setProtocol($this->playersProtocol);
		$pk->isEncoded = true;
		$targets = $this->fetchLocal();
		$server->broadcastPacketsCallback($pk, $targets);
	}}