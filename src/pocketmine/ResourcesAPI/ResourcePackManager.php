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
namespace pocketmine\ResourcesAPI;
use ErrorException;use InvalidArgumentException;use Logger;use LogicException;use pocketmine\utils\Config;use SplFileInfo;use function array_keys;use function copy;use function file_exists;use function file_get_contents;use function gettype;use function is_array;use function is_dir;use function mkdir;use function strtolower;use const DIRECTORY_SEPARATOR;use const pocketmine\RESOURCE_PATH;
class ResourcePackManager{
	private $path;
	private $serverForceResources = false;
	private $resourcePacks = [];
	private $uuidList = [];
	private $encryptionKeys = [];
	public function __construct(string $path, Logger $logger){
		$this->path = $path;
		if(!file_exists($this->path)){
			$logger->debug("Resource packs path $path does not exist, creating directory");
			mkdir($this->path);
		}elseif(!is_dir($this->path)){
			throw new InvalidArgumentException("Resource packs path $path exists and is not a directory");
		}
		if(!file_exists($this->path . "resource_packs.yml")){
			copy(RESOURCE_PATH . "resource_packs.yml", $this->path . "resource_packs.yml");
		}
		$resourcePacksConfig = new Config($this->path . "resource_packs.yml", Config::YAML, []);
		$this->serverForceResources = (bool) $resourcePacksConfig->get("force_resources", false);
		$resourceStack = $resourcePacksConfig->get("resource_stack", []);
		if(!is_array($resourceStack)){
			throw new InvalidArgumentException("\"resource_stack\" key should contain a list of pack names");
		}
		foreach($resourceStack as $pos => $pack){
			try{
				$pack = (string) $pack;
			}catch(ErrorException $e){
				$logger->critical("Found invalid entry in resource pack list at offset $pos of type " . gettype($pack));
				continue;
			}
			try{
				$packPath = $this->path . DIRECTORY_SEPARATOR . $pack;
				if(!file_exists($packPath)){
					throw new ResourcePackException("File or directory not found");
				}
				if(is_dir($packPath)){
					throw new ResourcePackException("Directory resource packs are unsupported");
				}
				$newPack = null;
				$info = new SplFileInfo($packPath);
				switch($info->getExtension()){
					case "zip":
					case "mcpack":
						$newPack = new ZippedResourcePack($packPath);
						break;
				}
				if($newPack instanceof ResourcePack){
					$this->resourcePacks[] = $newPack;
					$this->uuidList[strtolower($newPack->getPackId())] = $newPack;
					$keyPath = $this->path . DIRECTORY_SEPARATOR . $pack . ".key";
					if(file_exists($keyPath)){
						try{
							$key = file_get_contents($keyPath);
							if($key === false){
								throw new LogicException("Block must not return false when no error occurred. Use trap() if the block may return false.");
							}
							$this->encryptionKeys[strtolower($newPack->getPackId())] = $key;
							$newPack->setEncryptionKey($key);
						}catch(\ErrorException $e){
							throw new ResourcePackException("Could not read encryption key file: " . $e->getMessage(), 0, $e);
						}
					}
				}else{
					throw new ResourcePackException("Format not recognized");
				}
			}catch(ResourcePackException $e){
				$logger->critical("Could not load resource pack \"$pack\": " . $e->getMessage());
			}
		}
	}
    public function addPack(ResourcePack $pack) : void{
		$this->resourcePacks[] = $pack;
		$this->uuidList[strtolower($pack->getPackId())] = $pack;
		$key = $pack->getEncryptionKey();
		if($key !== null){
		    $this->encryptionKeys[strtolower($pack->getPackId())] = $key;
		}
    }
	public function getPath() : string{
		return $this->path;
	}
	public function resourcePacksRequired() : bool{
		return $this->serverForceResources;
	}
	public function getResourceStack() : array{
		return $this->resourcePacks;
	}
	public function getPackById(string $id){
		return $this->uuidList[strtolower($id)] ?? null;
	}
	public function getPackIdList() : array{
		return array_keys($this->uuidList);
	}
	public function getPackEncryptionKey(string $id) : ?string{
		return $this->encryptionKeys[strtolower($id)] ?? null;
	}}