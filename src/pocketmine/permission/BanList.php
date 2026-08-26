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
namespace pocketmine\permission;
use DateTime;use pocketmine\Server;use GlobalLogger;use Throwable;use function date;use function fclose;use function fgets;use function fopen;use function fwrite;use function is_resource;use function strtolower;
class BanList{
	private $list = [];
	private $file;
	private $enabled = true;
	public function __construct(string $file){
		$this->file = $file;
	}
	public function isEnabled() : bool{
		return $this->enabled;
	}
	public function setEnabled(bool $flag){
		$this->enabled = $flag;
	}
	public function getEntry(string $name) : ?BanEntry{
		$this->removeExpired();
		return $this->list[strtolower($name)] ?? null;
	}
	public function getEntries() : array{
		$this->removeExpired();
		return $this->list;
	}
	public function isBanned(string $name) : bool{
		$name = strtolower($name);
		if(!$this->isEnabled()){
			return false;
		}else{
			$this->removeExpired();
			return isset($this->list[$name]);
		}
	}
	public function add(BanEntry $entry){
		$this->list[$entry->getName()] = $entry;
		$this->save();
	}
	public function addBan(string $target, string $reason = null, DateTime $expires = null, string $source = null) : BanEntry{
		$entry = new BanEntry($target);
		$entry->setSource($source ?? $entry->getSource());
		$entry->setExpires($expires);
		$entry->setReason($reason ?? $entry->getReason());
		$this->list[$entry->getName()] = $entry;
		$this->save();
		return $entry;
	}
	public function remove(string $name){
		$name = strtolower($name);
		if(isset($this->list[$name])){
			unset($this->list[$name]);
			$this->save();
		}
	}
	public function removeExpired(){
		foreach($this->list as $name => $entry){
			if($entry->hasExpired()){
				unset($this->list[$name]);
			}
		}
	}
	public function load(){
		$this->list = [];
		$fp = @fopen($this->file, "r");
		if(is_resource($fp)){
			while(($line = fgets($fp)) !== false){
				if($line[0] !== "#"){
					try{
						$entry = BanEntry::fromString($line);
						if($entry instanceof BanEntry){
							$this->list[$entry->getName()] = $entry;
						}
					}catch(Throwable $e){
						$logger = GlobalLogger::get();
						$logger->critical("Failed to parse ban entry from string \"$line\": " . $e->getMessage());
						$logger->logException($e);
					}
				}
			}
			fclose($fp);
		}else{
			GlobalLogger::get()->error("Could not load ban list");
		}
	}
	public function save(bool $writeHeader = true){
		$this->removeExpired();
		$fp = @fopen($this->file, "w");
		if(is_resource($fp)){
			if($writeHeader){
				fwrite($fp, "# Updated " . date("d/m/Y H:i") . " by " . Server::getInstance()->getName() . " " . Server::getInstance()->getPocketMineVersion() . "\n");
				fwrite($fp, "# victim name | ban date | banned by | banned until | reason\n\n");
			}
			foreach($this->list as $entry){
				fwrite($fp, $entry->getString() . "\n");
			}
			fclose($fp);
		}else{
			GlobalLogger::get()->error("Could not save ban list");
		}
	}}