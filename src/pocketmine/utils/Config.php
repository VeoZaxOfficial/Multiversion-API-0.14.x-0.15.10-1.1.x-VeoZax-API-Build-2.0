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
namespace pocketmine\utils;
use GlobalLogger;use InvalidStateException;use RuntimeException;use function array_change_key_case;use function array_keys;use function array_pop;use function array_shift;use function basename;use function count;use function date;use function explode;use function file_exists;use function file_get_contents;use function file_put_contents;use function implode;use function is_array;use function is_bool;use function json_decode;use function json_encode;use function preg_match_all;use function preg_replace;use function serialize;use function str_replace;use function strlen;use function strtolower;use function substr;use function trim;use function unserialize;use function yaml_emit;use function yaml_parse;use const CASE_LOWER;use const JSON_BIGINT_AS_STRING;use const JSON_PRETTY_PRINT;
class Config{
	public const DETECT = -1; 
	public const PROPERTIES = 0; 
	public const CNF = Config::PROPERTIES; 
	public const JSON = 1; 
	public const YAML = 2; 
	public const SERIALIZED = 4; 
	public const ENUM = 5; 
	public const ENUMERATION = Config::ENUM;
	private $config = [];
	private $nestedCache = [];
	private $file;
	private $correct = false;
	private $type = Config::DETECT;
	private $jsonOptions = JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING;
	private $changed = false;
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
		"enum" => Config::ENUM
	];
	public function __construct(string $file, int $type = Config::DETECT, array $default = [], &$correct = null){
		$this->load($file, $type, $default);
		$correct = $this->correct;
	}
	public function reload(){
		$this->config = [];
		$this->nestedCache = [];
		$this->correct = false;
		$this->load($this->file, $this->type);
	}
	public function hasChanged() : bool{
		return $this->changed;
	}
	public function setChanged(bool $changed = true) : void{
		$this->changed = $changed;
	}
	public static function fixYAMLIndexes(string $str) : string{
		return preg_replace("#^( *)(y|Y|yes|Yes|YES|n|N|no|No|NO|true|True|TRUE|false|False|FALSE|on|On|ON|off|Off|OFF)( *)\:#m", "$1\"$2\"$3:", $str);
	}
	public function load(string $file, int $type = Config::DETECT, array $default = []) : bool{
		$this->correct = true;
		$this->file = $file;
		$this->type = $type;
		if($this->type === Config::DETECT){
			$extension = explode(".", basename($this->file));
			$extension = strtolower(trim(array_pop($extension)));
			if(isset(Config::$formats[$extension])){
				$this->type = Config::$formats[$extension];
			}else{
				$this->correct = false;
			}
		}
		if(!file_exists($file)){
			$this->config = $default;
			$this->save();
		}else{
			if($this->correct){
				$content = file_get_contents($this->file);
				switch($this->type){
					case Config::PROPERTIES:
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
	public function check() : bool{
		return $this->correct;
	}
	public function save() : bool{
		if($this->correct){
			$content = null;
			switch($this->type){
				case Config::PROPERTIES:
					$content = $this->writeProperties();
					break;
				case Config::JSON:
					$content = json_encode($this->config, $this->jsonOptions);
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
				default:
					throw new InvalidStateException("Config type is unknown, has not been set or not detected");
			}
			file_put_contents($this->file, $content);
			$this->changed = false;
			return true;
		}else{
			return false;
		}
	}
	public function setJsonOptions(int $options) : Config{
		if($this->type !== Config::JSON){
			throw new RuntimeException("Attempt to set JSON options for non-JSON config");
		}
		$this->jsonOptions = $options;
		$this->changed = true;
		return $this;
	}
	public function enableJsonOption(int $option) : Config{
		if($this->type !== Config::JSON){
			throw new RuntimeException("Attempt to enable JSON option for non-JSON config");
		}
		$this->jsonOptions |= $option;
		$this->changed = true;
		return $this;
	}
	public function disableJsonOption(int $option) : Config{
		if($this->type !== Config::JSON){
			throw new RuntimeException("Attempt to disable JSON option for non-JSON config");
		}
		$this->jsonOptions &= ~$option;
		$this->changed = true;
		return $this;
	}
	public function getJsonOptions() : int{
		if($this->type !== Config::JSON){
			throw new RuntimeException("Attempt to get JSON options for non-JSON config");
		}
		return $this->jsonOptions;
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
		$this->nestedCache = [];
		$this->changed = true;
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
	public function removeNested(string $key) : void{
		$this->nestedCache = [];
		$this->changed = true;
		$vars = explode(".", $key);
		$currentNode =& $this->config;
		while(count($vars) > 0){
			$nodeName = array_shift($vars);
			if(isset($currentNode[$nodeName])){
				if(empty($vars)){ 
					unset($currentNode[$nodeName]);
				}elseif(is_array($currentNode[$nodeName])){
					$currentNode =& $currentNode[$nodeName];
				}
			}else{
				break;
			}
		}
	}
	public function get($k, $default = false){
		return ($this->correct and isset($this->config[$k])) ? $this->config[$k] : $default;
	}
	public function set($k, $v = true){
		$this->config[$k] = $v;
		$this->changed = true;
		foreach($this->nestedCache as $nestedKey => $nvalue){
			if(substr($nestedKey, 0, strlen($k) + 1) === ($k . ".")){
				unset($this->nestedCache[$nestedKey]);
			}
		}
	}
	public function setAll(array $v){
		$this->config = $v;
		$this->changed = true;
	}
	public function exists($k, bool $lowercase = false) : bool{
		if($lowercase){
			$k = strtolower($k); 
			$array = array_change_key_case($this->config, CASE_LOWER); 
			return isset($array[$k]); 
		}else{
			return isset($this->config[$k]);
		}
	}
	public function remove($k){
		unset($this->config[$k]);
		$this->changed = true;
	}
	public function getAll(bool $keys = false) : array{
		return ($keys ? array_keys($this->config) : $this->config);
	}
	public function setDefaults(array $defaults){
		$this->fillDefaults($defaults, $this->config);
	}
	private function fillDefaults(array $default, &$data) : int{
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
		if($changed > 0){
			$this->changed = true;
		}
		return $changed;
	}
	private function parseList(string $content){
		foreach(explode("\n", trim(str_replace("\r\n", "\n", $content))) as $v){
			$v = trim($v);
			if($v == ""){
				continue;
			}
			$this->config[$v] = true;
		}
	}
	private function writeProperties() : string{
		$content = "#Properties Config file\r\n#" . date("D M j H:i:s T Y") . "\r\n";
		foreach($this->config as $k => $v){
			if(is_bool($v)){
				$v = $v ? "on" : "off";
			}elseif(is_array($v)){
				$v = implode(";", $v);
			}
			$content .= $k . "=" . $v . "\r\n";
		}
		return $content;
	}
	private function parseProperties(string $content){
		if(preg_match_all('/^\s*([a-zA-Z0-9\-_\.]+)[ \t]*=([^\r\n]*)/um', $content, $matches) > 0){ 
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
					GlobalLogger::get()->debug("[Config] Repeated property " . $k . " on file " . $this->file);
				}
				$this->config[$k] = $v;
			}
		}
	}}