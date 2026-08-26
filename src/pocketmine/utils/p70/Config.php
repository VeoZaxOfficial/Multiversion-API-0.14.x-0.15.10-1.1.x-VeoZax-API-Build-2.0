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


namespace pocketmine\utils\p70;use pocketmine\scheduler\FileWriteTask;use pocketmine\Server;
class Config{
	const DETECT = -1; 
	const PROPERTIES = 0; 
	const CNF = Config::PROPERTIES; 
	const JSON = 1; 
	const YAML = 2; 
	const SERIALIZED = 4; 
	const ENUM = 5; 
	const ENUMERATION = Config::ENUM;
	private $config = [];
	private $nestedCache = [];
	private $file;
	private $correct = false;
	private $type = Config::DETECT;
	public static $formats = [
		"properties" => Config::PROPERTIES,
		"cnf" => Config::CNF,
		"conf" => Config::CNF,
		"config" => Config::CNF,
		"json" => Config::JSON,
		"js" => Config::JSON,
		"yml" => Config::YAML,
		"yaml" => Config::YAML,
		"sl" => Config::SERIALIZED,
		"serialize" => Config::SERIALIZED,
		"txt" => Config::ENUM,
		"list" => Config::ENUM,
		"enum" => Config::ENUM,
	];
	public function __construct($file, $type = Config::DETECT, $default = [], &$correct = null){
		$this->load($file, $type, $default);
		$correct = $this->correct;
	}
	public function reload(){
		$this->config = [];
		$this->nestedCache = [];
		$this->correct = false;
		$this->load($this->file);
		$this->load($this->file, $this->type);
	}
	public static function fixYAMLIndexes($str){
		return preg_replace("#^([ ]*)([a-zA-Z_]{1}[ ]*)\\:$#m", "$1\"$2\":", $str);
	}
	public function load($file, $type = Config::DETECT, $default = []){
		$this->correct = true;
		$this->type = (int) $type;
		$this->file = $file;
		if(!is_array($default)){
			$default = [];
		}
		if(!file_exists($file)){
			$this->config = $default;
			$this->save();
		}else{
			if($this->type === Config::DETECT){
				$extension = explode(".", basename($this->file));
				$extension = strtolower(trim(array_pop($extension)));
				if(isset(Config::$formats[$extension])){
					$this->type = Config::$formats[$extension];
				}else{
					$this->correct = false;
				}
			}
			if($this->correct === true){
				$content = file_get_contents($this->file);
				switch($this->type){
					case Config::PROPERTIES:
					case Config::CNF:
						$this->parseProperties($content);
						break;
					case Config::JSON:
						$this->config = json_decode($content, true);
						break;
					case Config::YAML:
						$content = self::fixYAMLIndexes($content);
						$this->config = yaml_parse($content);
						break;
					case Config::SERIALIZED:
						$this->config = unserialize($content);
						break;
					case Config::ENUM:
						$this->parseList($content);
						break;
					default:
						$this->correct = false;
						return false;
				}
				if(!is_array($this->config)){
					$this->config = $default;
				}
				if($this->fillDefaults($default, $this->config) > 0){
					$this->save();
				}
			}else{
				return false;
			}
		}
		return true;
	}
	public function check(){
		return $this->correct === true;
	}
	public function save($async = false){
		if($this->correct === true){
			try{
				$content = null;
				switch($this->type){
					case Config::PROPERTIES:
					case Config::CNF:
						$content = $this->writeProperties();
						break;
					case Config::JSON:
						$content = json_encode($this->config, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
						break;
					case Config::YAML:
						$content = yaml_emit($this->config, YAML_UTF8_ENCODING);
						break;
					case Config::SERIALIZED:
						$content = serialize($this->config);
						break;
					case Config::ENUM:
						$content = implode("\r\n", array_keys($this->config));
						break;
				}
				if($async){
					Server::getInstance()->getScheduler()->scheduleAsyncTask(new FileWriteTask($this->file, $content));
				}else{
					file_put_contents($this->file, $content);
				}
			}catch(\Throwable $e){
				$logger = Server::getInstance()->getLogger();
				$logger->critical("Could not save Config " . $this->file . ": " . $e->getMessage());
				if(\pocketmine\DEBUG > 1 and $logger instanceof MainLogger){
					$logger->logException($e);
				}
			}
			return true;
		}else{
			return false;
		}
	}
	public function __get($k){
		return $this->get($k);
	}
	public function __set($k, $v){
		$this->set($k, $v);
	}
	public function __isset($k){
		return $this->exists($k);
	}
	public function __unset($k){
		$this->remove($k);
	}
	public function setNested($key, $value){
		$vars = explode(".", $key);
		$base = array_shift($vars);
		if(!isset($this->config[$base])){
			$this->config[$base] = [];
		}
		$base =& $this->config[$base];
		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(!isset($base[$baseKey])){
				$base[$baseKey] = [];
			}
			$base =& $base[$baseKey];
		}
		$base = $value;
		$this->nestedCache[$key] = $value;
	}
	public function getNested($key, $default = null){
		if(isset($this->nestedCache[$key])){
			return $this->nestedCache[$key];
		}
		$vars = explode(".", $key);
		$base = array_shift($vars);
		if(isset($this->config[$base])){
			$base = $this->config[$base];
		}else{
			return $default;
		}
		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) and isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $default;
			}
		}
		return $this->nestedCache[$key] = $base;
	}
	public function get($k, $default = false){
		return ($this->correct and isset($this->config[$k])) ? $this->config[$k] : $default;
	}
	public function getPath($path){
		$currPath =& $this->config;
		foreach(explode(".", $path) as $component){
			if(isset($currPath[$component])){
				$currPath =& $currPath[$component];
			}else{
				$currPath = null;
			}
		}
		return $currPath;
	}
	public function setPath($path, $value){
		$currPath =& $this->config;
		$components = explode(".", $path);
		$final = array_pop($components);
		foreach($components as $component){
			if(!isset($currPath[$component])){
				$currPath[$component] = [];
			}
			$currPath =& $currPath[$component];
		}
		$currPath[$final] = $value;
	}
	public function set($k, $v = true){
		$this->config[$k] = $v;
		foreach($this->nestedCache as $nestedKey => $nvalue){
			if(substr($nestedKey, 0, strlen($k) + 1) === ($k . ".")){
				unset($this->nestedCache[$nestedKey]);
  			}
		}
	}
	public function setAll($v){
		$this->config = $v;
	}
	public function exists($k, $lowercase = false){
		if($lowercase === true){
			$k = strtolower($k); 
			$array = array_change_key_case($this->config, CASE_LOWER); 
			return isset($array[$k]); 
		}else{
			return isset($this->config[$k]);
		}
	}
	public function remove($k){
		unset($this->config[$k]);
	}
	public function getAll($keys = false){
		return ($keys === true ? array_keys($this->config) : $this->config);
	}
	public function setDefaults(array $defaults){
		$this->fillDefaults($defaults, $this->config);
	}
	private function fillDefaults($default, &$data){
		$changed = 0;
		foreach($default as $k => $v){
			if(is_array($v)){
				if(!isset($data[$k]) or !is_array($data[$k])){
					$data[$k] = [];
				}
				$changed += $this->fillDefaults($v, $data[$k]);
			}elseif(!isset($data[$k])){
				$data[$k] = $v;
				++$changed;
			}
		}
		return $changed;
	}
	private function parseList($content){
		foreach(explode("\n", trim(str_replace("\r\n", "\n", $content))) as $v){
			$v = trim($v);
			if($v == ""){
				continue;
			}
			$this->config[$v] = true;
		}
	}
	private function writeProperties(){
		$content = "#Properties Config file\r\n#" . date("D M j H:i:s T Y") . "\r\n";
		foreach($this->config as $k => $v){
			if(is_bool($v) === true){
				$v = $v === true ? "on" : "off";
			}elseif(is_array($v)){
				$v = implode(";", $v);
			}
			$content .= $k . "=" . $v . "\r\n";
		}
		return $content;
	}
	private function parseProperties($content){
		if(preg_match_all('/([a-zA-Z0-9\-_\.]*)=([^\r\n]*)/u', $content, $matches) > 0){ 
			foreach($matches[1] as $i => $k){
				$v = trim($matches[2][$i]);
				switch(strtolower($v)){
					case "on":
					case "true":
					case "yes":
						$v = true;
						break;
					case "off":
					case "false":
					case "no":
						$v = false;
						break;
				}
				if(isset($this->config[$k])){
					MainLogger::getLogger()->debug("[Config] Repeated property " . $k . " on file " . $this->file);
				}
				$this->config[$k] = $v;
			}
		}
	}
}