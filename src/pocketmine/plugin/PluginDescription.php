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
namespace pocketmine\plugin;
use pocketmine\permission\Permission;use function array_map;use function array_values;use function constant;use function defined;use function extension_loaded;use function is_array;use function phpversion;use function preg_match;use function str_replace;use function stripos;use function strlen;use function strtoupper;use function substr;use function version_compare;
class PluginDescription{
	private $map;
	private $name;
	private $main;
	private $api;
	private $compatibleMcpeProtocols = [];
	private $extensions = [];
	private $depend = [];
	private $softDepend = [];
	private $loadBefore = [];
	private $version;
	private $commands = [];
	private $description = "";
	private $authors = [];
	private $website = "";
	private $prefix = "";
	private $order = PluginLoadOrder::POSTWORLD;
	private $permissions = [];
	public function __construct($yamlString){
		$this->loadMap(!is_array($yamlString) ? yaml_parse($yamlString) : $yamlString);
	}
	private function loadMap(array $plugin){
		$this->map = $plugin;
		$this->name = $plugin["name"];
		if(preg_match('/^[A-Za-z0-9 _.-]+$/', $this->name) === 0){
			throw new PluginException("Invalid PluginDescription name");
		}
		$this->name = str_replace(" ", "_", $this->name);
		$this->version = (string) $plugin["version"];
		$this->main = $plugin["main"];
		if(stripos($this->main, "pocketmine\\") === 0){
			throw new PluginException("Invalid PluginDescription main, cannot start within the PocketMine namespace");
		}
		$this->api = array_map("\strval", (array) ($plugin["api"] ?? []));
		$this->compatibleMcpeProtocols = array_map("\intval", (array) ($plugin["mcpe-protocol"] ?? []));
		if(isset($plugin["commands"]) and is_array($plugin["commands"])){
			$this->commands = $plugin["commands"];
		}
		if(isset($plugin["depend"])){
			$this->depend = (array) $plugin["depend"];
		}
		if(isset($plugin["extensions"])){
			$extensions = (array) $plugin["extensions"];
			$isLinear = $extensions === array_values($extensions);
			foreach($extensions as $k => $v){
				if($isLinear){
					$k = $v;
					$v = "*";
				}
				$this->extensions[$k] = is_array($v) ? $v : [$v];
			}
		}
		$this->softDepend = (array) ($plugin["softdepend"] ?? $this->softDepend);
		$this->loadBefore = (array) ($plugin["loadbefore"] ?? $this->loadBefore);
		$this->website = (string) ($plugin["website"] ?? $this->website);
		$this->description = (string) ($plugin["description"] ?? $this->description);
		$this->prefix = (string) ($plugin["prefix"] ?? $this->prefix);
		if(isset($plugin["load"])){
			$order = strtoupper($plugin["load"]);
			if(!defined(PluginLoadOrder::class . "::" . $order)){
				throw new PluginException("Invalid PluginDescription load");
			}else{
				$this->order = constant(PluginLoadOrder::class . "::" . $order);
			}
		}
		$this->authors = [];
		if(isset($plugin["author"])){
			$this->authors[] = $plugin["author"];
		}
		if(isset($plugin["authors"])){
			foreach($plugin["authors"] as $author){
				$this->authors[] = $author;
			}
		}
		if(isset($plugin["permissions"])){
			$this->permissions = Permission::loadPermissions($plugin["permissions"]);
		}
	}
	public function getFullName() : string{
		return $this->name . " v" . $this->version;
	}
	public function getCompatibleApis() : array{
		return $this->api;
	}
	public function getCompatibleMcpeProtocols() : array{
		return $this->compatibleMcpeProtocols;
	}
	public function getAuthors() : array{
		return $this->authors;
	}
	public function getPrefix() : string{
		return $this->prefix;
	}
	public function getCommands() : array{
		return $this->commands;
	}
	public function getRequiredExtensions() : array{
		return $this->extensions;
	}
	public function checkRequiredExtensions(){
		foreach($this->extensions as $name => $versionConstrs){
			if(!extension_loaded($name)){
				throw new PluginException("Required extension $name not loaded");
			}
			if(!is_array($versionConstrs)){
				$versionConstrs = [$versionConstrs];
			}
			$gotVersion = phpversion($name);
			foreach($versionConstrs as $constr){ 
				if($constr === "*"){
					continue;
				}
				if($constr === ""){
					throw new PluginException("One of the extension version constraints of $name is empty. Consider quoting the version string in plugin.yml");
				}
				foreach(["<=", "le", "<>", "!=", "ne", "<", "lt", "==", "=", "eq", ">=", "ge", ">", "gt"] as $comparator){
					if(substr($constr, 0, strlen($comparator)) === $comparator){
						$version = substr($constr, strlen($comparator));
						if(!version_compare($gotVersion, $version, $comparator)){
							throw new PluginException("Required extension $name has an incompatible version ($gotVersion not $constr)");
						}
						continue 2; 
					}
				}
				throw new PluginException("Error parsing version constraint: $constr");
			}
		}
	}
	public function getDepend() : array{
		return $this->depend;
	}
	public function getDescription() : string{
		return $this->description;
	}
	public function getLoadBefore() : array{
		return $this->loadBefore;
	}
	public function getMain() : string{
		return $this->main;
	}
	public function getName() : string{
		return $this->name;
	}
	public function getOrder() : int{
		return $this->order;
	}
	public function getPermissions() : array{
		return $this->permissions;
	}
	public function getSoftDepend() : array{
		return $this->softDepend;
	}
	public function getVersion() : string{
		return $this->version;
	}
	public function getWebsite() : string{
		return $this->website;
	}
	public function getMap() : array{
		return $this->map;
	}}