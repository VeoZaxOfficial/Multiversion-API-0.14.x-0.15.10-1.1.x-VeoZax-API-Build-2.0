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
use InvalidArgumentException;use LogLevel;use pocketmine\event\server\UpdateNotifyEvent;use pocketmine\Player;use pocketmine\Server;use pocketmine\utils\TextFormat;use pocketmine\utils\VersionString;use function date;use function sprintf;use function str_repeat;use function strlen;use function strtolower;use function ucfirst;use const pocketmine\BASE_VERSION;use const pocketmine\BUILD_NUMBER;use const pocketmine\IS_DEVELOPMENT_BUILD;
class AutoUpdater{
	protected $server;
	protected $endpoint;
	protected $updateInfo = null;
	protected $newVersion;
	public function __construct(Server $server, string $endpoint){
		$this->server = $server;
		$this->endpoint = "http://$endpoint/api/";
		 
			$this->doCheck();
		
	}
	public function checkUpdateCallback(array $updateInfo){
		$this->updateInfo = $updateInfo;
		$this->checkUpdate();
		if($this->hasUpdate()){
			(new UpdateNotifyEvent($this))->call();
			 
				$this->showConsoleUpdate();
			
		}elseif(true){ 
			if(!IS_DEVELOPMENT_BUILD and $this->getChannel() !== "stable"){
				$this->showChannelSuggestionStable();
			}elseif(IS_DEVELOPMENT_BUILD and $this->getChannel() === "stable"){
				$this->showChannelSuggestionBeta();
			}
		}
	}
	public function hasUpdate() : bool{
		return $this->newVersion !== null;
	}
	public function showConsoleUpdate(){
		$prefix = "§8[§9Veo§bZax§cAPI§8]§r §7";
		$messages = [
			$prefix . $this->server->getName() . " developed by §bVeoZax",
			$prefix . "WEBSITE: §b§nhttps://info.veozax.xyz",
			$prefix . "This API Still in Development, Expect §cBugs§7, §cCrashes§7, §cIncompatibility§7."
		];
		$this->printConsoleMessage($messages, LogLevel::INFO, "§9Veo§bZax§cAPI §fInformation");
	}
	public function showPlayerUpdate(Player $player){
		$prefix = "§8[§9Veo§bZax§cAPI§8]§r §7";
		$player->sendMessage($prefix . "This server runs on §9Veo§bZax§cAPI§7, by §bVeoZax§7.");
		$player->sendMessage($prefix . "§b§nhttps://info.veozax.xyz");
	}
	protected function showChannelSuggestionStable(){
		$this->printConsoleMessage([
			"It appears you're running a Stable build, when you've specified that you prefer to run " . ucfirst($this->getChannel()) . " builds.",
			"If you would like to be kept informed about new Stable builds only, it is recommended that you change 'preferred-channel' in your pocketmine.yml to 'stable'."
		]);
	}
	protected function showChannelSuggestionBeta(){
		$this->printConsoleMessage([
			"It appears you're running a Beta build, when you've specified that you prefer to run Stable builds.",
			"If you would like to be kept informed about new Beta or Development builds, it is recommended that you change 'preferred-channel' in your pocketmine.yml to 'beta' or 'development'."
		]);
	}
	protected function printConsoleMessage(array $lines, string $logLevel = LogLevel::INFO, string $title = null){
		$logger = $this->server->getLogger();
		$title = $title ?? ($this->server->getName() . ' Auto Updater');
		$visibleLength = strlen(TextFormat::clean($title));
		$logger->log($logLevel, sprintf('----- %s -----', $title));
		foreach($lines as $line){
			$logger->log($logLevel, $line);
		}
		$logger->log($logLevel, sprintf('----- %s -----', str_repeat('-', $visibleLength)));
	}
	public function getUpdateInfo(){
		return $this->updateInfo;
	}
	public function doCheck(){
		$this->server->getAsyncPool()->submitTask(new UpdateCheckTask($this->endpoint, $this->getChannel()));
	}
	protected function checkUpdate(){
		if($this->updateInfo === null){
			return;
		}
		$currentVersion = new VersionString(BASE_VERSION, IS_DEVELOPMENT_BUILD, BUILD_NUMBER);
		try{
			$newVersion = new VersionString($this->updateInfo["base_version"], $this->updateInfo["is_dev"], $this->updateInfo["build"]);
		}catch(InvalidArgumentException $e){
			$this->server->getLogger()->debug("[AutoUpdater] Assuming no update because \"" . $e->getMessage() . "\"");
			return;
		}
		if($currentVersion->compare($newVersion) > 0 and ($currentVersion->getFullVersion() !== $newVersion->getFullVersion() or $currentVersion->getBuild() > 0)){
			$this->newVersion = $newVersion;
		}
	}
	public function getChannel() : string{
		$channel = "stable"; 
		if($channel !== "stable" and $channel !== "beta" and $channel !== "alpha" and $channel !== "development"){
			$channel = "stable";
		}
		return $channel;
	}
	public function getEndpoint() : string{
		return $this->endpoint;
	}}