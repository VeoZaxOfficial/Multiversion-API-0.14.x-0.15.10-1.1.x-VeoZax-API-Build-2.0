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
namespace pocketmine\updater;
use pocketmine\scheduler\AsyncTask;use pocketmine\Server;use pocketmine\utils\Internet;use function is_array;use function json_decode;
class UpdateCheckTask extends AsyncTask{
	private $endpoint;
	private $channel;
	private $error = "Unknown error";
	public function __construct(string $endpoint, string $channel){
		$this->endpoint = $endpoint;
		$this->channel = $channel;
	}
	public function onRun() : void{
		$error = "";
		$response = Internet::getURL($this->endpoint . "?channel=" . $this->channel, 4, [], $error);
		$this->error = $error;
		if($response !== false){
			$response = json_decode($response, true);
			if(is_array($response)){
				if(
					isset($response["base_version"]) and
					isset($response["is_dev"]) and
					isset($response["build"]) and
					isset($response["date"]) and
					isset($response["download_url"])
				){
					$response["details_url"] = $response["details_url"] ?? null;
					$this->setResult($response);
				}elseif(isset($response["error"])){
					$this->error = $response["error"];
				}else{
					$this->error = "Invalid response data";
				}
			}else{
				$this->error = "Invalid response data";
			}
		}
	}
	public function onCompletion(Server $server){
		if($this->error !== ""){
			$server->getLogger()->debug("[AutoUpdater] Async update check failed due to \"$this->error\"");
		}else{
			$updateInfo = $this->getResult();
			if(is_array($updateInfo)){
				$server->getUpdater()->checkUpdateCallback($updateInfo);
			}else{
				$server->getLogger()->debug("[AutoUpdater] Update info error");
			}
		}
	}}